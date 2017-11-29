<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "cars_requests".
 *
 * @property string $id
 * @property string $manufacturer
 * @property string $model
 * @property integer $user_id
 * @property string $created
 * @property bool $status
 *
 * @property User $user
 */
class CarRequest extends UserDependency
{
    public $updated_at;

	/**
	 * Init model
	 */
    public function init() {
	    $this->status = false;
    }

	/**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                [
                    'class' => TimestampBehavior::className(),
                    'createdAtAttribute' => 'created',
                    'value' => date('Y-m-d H:i:s')
                ]
            ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cars_requests';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['manufacturer', 'model'], 'required'],
            [['user_id'], 'integer'],
            [['status'], 'boolean'],
            [['manufacturer', 'model'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'manufacturer' => Yii::t('app', 'Manufacturer'),
            'model' => Yii::t('app', 'Model'),
            'user_id' => 'User ID',
            'created' => 'Created',
            'status' => 'Status',
        ];
    }

	/**
	 * @return \yii\db\ActiveQuery
	 */
    public function getUser()
    {
    	return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

	/**
	 * Sends notification to admin
	 */
    public function sendAdminNotification()
    {
        Yii::$app->mailer->compose('car_request', ['model' => $this])
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo(Yii::$app->params['adminEmail'])
            ->setSubject(Yii::$app->name . ' - ' . Yii::t('app/email', 'New car request'))
            ->send();
    }

	/**
	 * Send email about approved request
	 */
    public function sendUserNotification()
    {
    	if ($this->user->email) {
		    Yii::$app->mailer->compose('car_request_approved', ['model' => $this])
			    ->setFrom(Yii::$app->params['adminEmail'])
			    ->setTo($this->user->email)
			    ->setSubject(Yii::$app->name . ' - ' . Yii::t('app/email', 'Car request approved'))
			    ->send();
	    }
    }

	/**
	 * @return bool
	 */
    public function hasData()
    {
    	return $this->manufacturer || $this->model;
    }
}
