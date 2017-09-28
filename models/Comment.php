<?php

namespace app\models;

use app\components\ArrayHelper;
use app\components\behaviors\UserBehavior;
use ruskid\YiiBehaviors\IpBehavior;
use yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "comments".
 *
 * @property integer $id
 * @property integer $object_id
 * @property integer $user_id
 * @property string $text
 * @property integer $reply_id
 * @property string $status
 * @property string $user_ip
 * @property string $created
 * @property string $modified
 * @property string $object
 *
 * @property User $user
 * @property UserCommentKarma[] $karma
 * @property Comparison|News $owner
 */
class Comment extends UserDependency
{
    const COMMENTS_PER_PAGE = 10;
    const COMMENT_STATUS_APPROVED = 'approved';

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
                    'updatedAtAttribute' => 'modified',
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
        return 'comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['object_id', 'reply_id'], 'integer'],
            [['object_id', 'text', 'status', 'object'], 'required'],
            [['text', 'object'], 'string'],
            [['status'], 'string', 'max' => 50],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['reply_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comment::className(), 'targetAttribute' => ['reply_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Comment ID'),
            'object_id' => Yii::t('app', 'Comment Relation ID'),
            'user_id' => Yii::t('app', 'Author'),
            'text' => Yii::t('app', 'Comment Text'),
            'reply_id' => Yii::t('app', 'Comment Reply ID'),
            'status' => Yii::t('app', 'Comment Status'),
            'user_ip' => Yii::t('app', 'Comment User Ip'),
            'created' => Yii::t('app', 'Publish Date'),
            'modified' => Yii::t('app', 'Comment Modified'),
            'object' => Yii::t('app/admin', 'Comment Relation'),
        ];
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne($this->object, ['id' => 'object_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKarma()
    {
        return $this->hasMany(UserCommentKarma::className(), ['comment_id' => 'id']);
    }

    public function getTotalKarma()
    {
        $total = 0;
        
        if ($this->karma) {
            foreach ($this->karma as $karma)
            {
                $total += $karma->increase - $karma->decrease;
            }
        }

        return $total;
    }
}
