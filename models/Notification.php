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
 * @property string $created
 * @property string $message
 * @property integer $is_read
 *
 * @property User $user
 */
class Notification extends ActiveRecord
{
	const TYPE_NEW_COMMENT = 'comment';
	const TYPE_NEW_COMPARISON = 'comparison';
	const TYPE_FAVORITE_COMPARISON = 'favorite';
	const TYPE_LIKE_COMPARISON = 'like';
	const TYPE_KARMA = 'karma';

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
			default:
				$message = Yii::t('app', '{user} has commenting your comparison {title}', $data);
				break;
		}

		$notification = new self();
		$notification->user_id = $user_id;
		$notification->message = $message;

		$notification->save();
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
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
			[['user_id', 'is_read'], 'integer'],
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
			'created' => 'Created',
			'message' => 'Message',
			'is_read' => 'Is Read',
		];
	}
}