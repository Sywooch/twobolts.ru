<?php

namespace app\models;

use ruskid\YiiBehaviors\IpBehavior;
use yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "user_autologin".
 *
 * @property string $key_id
 * @property integer $user_id
 * @property string $user_agent
 * @property string $last_ip
 * @property string $last_login
 *
 * @property User $user
 */
class UserAutologin extends UserDependency
{
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
                    'createdAtAttribute' => 'last_login',
                    'updatedAtAttribute' => 'last_login',
                    'value' => date('Y-m-d H:i:s')
                ],
                'ip' => [
                    'class' => IpBehavior::className(),
                    'createdIpAttribute' => 'user_ip',
                    'updatedIpAttribute' => 'user_ip',
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_autologin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key_id', 'user_agent'], 'required'],
            [['key_id'], 'string', 'max' => 32],
            [['user_agent'], 'string', 'max' => 150],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'key_id' => Yii::t('app', 'Key ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'user_agent' => Yii::t('app', 'User Agent'),
            'last_ip' => Yii::t('app', 'Last Ip'),
            'last_login' => Yii::t('app', 'Last Login'),
        ];
    }
}
