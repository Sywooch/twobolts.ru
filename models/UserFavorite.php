<?php

namespace app\models;

use yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_favorites".
 *
 * @property integer $favorite_id
 * @property integer $user_id
 * @property integer $comparison_id
 *
 * @property User $user
 * @property Comparison $comparison
 */
class UserFavorite extends UserDependency
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_favorites';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comparison_id'], 'required'],
            [['comparison_id'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['comparison_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comparison::className(), 'targetAttribute' => ['comparison_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'favorite_id' => Yii::t('app', 'Favorite ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'comparison_id' => Yii::t('app', 'Comparison ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComparison()
    {
        return $this->hasOne(Comparison::className(), ['id' => 'comparison_id']);
    }
}
