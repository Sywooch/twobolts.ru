<?php

namespace app\models;

use yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "users_comments_karma".
 *
 * @property integer $id
 * @property integer $comparison_id
 * @property integer $user_id
 * @property integer $comment_id
 * @property integer $increase
 * @property string $increase_date
 * @property integer $decrease
 * @property string $decrease_date
 *
 * @property Comparison $comparison
 * @property User $user
 * @property Comment $comment
 */
class UserCommentKarma extends UserDependency
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users_comments_karma';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comparison_id', 'comment_id'], 'required'],
            [['comparison_id', 'comment_id', 'increase', 'decrease'], 'integer'],
            [['increase_date', 'decrease_date'], 'safe'],
            [['comparison_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comparison::className(), 'targetAttribute' => ['comparison_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['comment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comment::className(), 'targetAttribute' => ['comment_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'comparison_id' => Yii::t('app', 'Comparison ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'comment_id' => Yii::t('app', 'Comment ID'),
            'increase' => Yii::t('app', 'Increase'),
            'increase_date' => Yii::t('app', 'Increase Date'),
            'decrease' => Yii::t('app', 'Decrease'),
            'decrease_date' => Yii::t('app', 'Decrease Date'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComparison()
    {
        return $this->hasOne(Comparison::className(), ['id' => 'comparison_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComment()
    {
        return $this->hasOne(Comment::className(), ['id' => 'comment_id']);
    }
}