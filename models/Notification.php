<?php

namespace app\models;

use app\components\ArrayHelper;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "notification".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $author_id
 * @property string $created
 * @property string $message
 * @property integer $is_read
 * @property string $type
 *
 * @property User $user
 * @property User $author
 */
class Notification extends ActiveRecord
{
	const TYPE_NEW_COMMENT = 'comment';
	const TYPE_NEW_COMPARISON = 'comparison';
	const TYPE_FAVORITE_COMPARISON = 'favorite';
	const TYPE_LIKE_COMPARISON = 'like';
	const TYPE_DISLIKE_COMPARISON = 'dislike';
	const TYPE_KARMA_PLUS = 'increase';
	const TYPE_KARMA_MINUS = 'decrease';

	public static $icons = [
		self::TYPE_NEW_COMMENT => 'commenting',
		self::TYPE_NEW_COMPARISON => 'sliders',
		self::TYPE_FAVORITE_COMPARISON => 'star',
		self::TYPE_LIKE_COMPARISON => 'thumbs-up',
		self::TYPE_DISLIKE_COMPARISON => 'thumbs-down',
		self::TYPE_KARMA_PLUS => 'smile-o',
		self::TYPE_KARMA_MINUS => 'frown-o'
	];

	/**
	 * Init
	 */
	public function init()
	{
		parent::init();

		$this->created = date('Y-m-d H:i:s');
		$this->is_read = 0;
	}

	/**
	 * @param $type
	 * @param $user_id
	 * @param array $data
	 */
	public static function create($type, $user_id, $data = [])
	{
		$data = ArrayHelper::merge(['user' => User::identity()->username], $data);

		switch ($type) {
			case self::TYPE_LIKE_COMPARISON:
				$message = Yii::t('app', '{user} {type} your comparison {url}', $data);
				break;
			case self::TYPE_DISLIKE_COMPARISON:
				$message = Yii::t('app', '{user} {type} your comparison {url}', $data);
				break;
			case self::TYPE_FAVORITE_COMPARISON:
				$message = Yii::t('app', '{user} favorites your comparison {url}', $data);
				break;
			case self::TYPE_NEW_COMMENT:
				$message = Yii::t('app', '{user} commented your comparison {url}', $data);
				break;
			case self::TYPE_KARMA_PLUS:
				$message = Yii::t('app', '{user} increase your karma', $data);
				break;
			case self::TYPE_KARMA_MINUS:
				$message = Yii::t('app', '{user} decrease your karma', $data);
				break;
			default:
				$message = Yii::t('app', '{user} has commenting your comparison {url}', $data);
				break;
		}
		
		if ($user_id != User::identity()->id) {
			$notification = new self();
			$notification->user_id = $user_id;
			$notification->author_id = User::identity()->id;
			$notification->message = $message;
			$notification->type = $type;
			
			$notification->save();
			
			if ($notification->user->isNotifiable()) {
				Yii::$app->mailer->compose('new_notification', ['model' => $notification])
					->setFrom(Yii::$app->params['adminEmail'])
					->setTo($notification->user->email)
					->setSubject(Yii::$app->name . ' - ' . Yii::t('app/email', 'New notification'))
					->send();
			}
		}
	}

	/**
	 * @param $data
	 *
	 * @throws \yii\db\Exception
	 */
	public static function batchCreate($data)
	{
		Yii::$app->db->createCommand()
			->batchInsert(self::tableName(), ['created', 'is_read', 'user_id', 'author_id', 'message'], $data)
			->execute();
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAuthor()
	{
		return $this->hasOne(User::className(), ['id' => 'author_id']);
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'notification';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'author_id', 'is_read'], 'integer'],
			[['created'], 'safe'],
			[['message'], 'string'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'user_id' => 'User ID',
			'author_id' => 'Author ID',
			'created' => 'Created',
			'message' => 'Message',
			'is_read' => 'Is Read',
		];
	}
}