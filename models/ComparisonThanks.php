<?php

namespace app\models;

use yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "comparisons_users_thanks".
 *
 * @property integer $id
 * @property integer $comparison_id
 * @property integer $user_id
 * @property string $thanks_date
 * @property integer $dislike
 *
 * @property Comparison $comparison
 * @property User $user
 */
class ComparisonThanks extends UserDependency
{
    public $updated_at;

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
                    'createdAtAttribute' => 'thanks_date',
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
        return 'comparisons_users_thanks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comparison_id'], 'required'],
            [['comparison_id', 'user_id', 'dislike'], 'integer'],
            [['thanks_date'], 'safe'],
            [['comparison_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comparison::className(), 'targetAttribute' => ['comparison_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'thanks_date' => Yii::t('app', 'Thanks Date'),
            'dislike' => Yii::t('app', 'Dislike'),
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}