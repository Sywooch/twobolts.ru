<?php

namespace app\models;

use yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "cars_comparisons_criteria".
 *
 * @property integer $id
 * @property integer $comparison_id
 * @property integer $criteria_id
 * @property integer $criteria_main_value
 * @property string $criteria_main_comment
 * @property integer $criteria_compare_value
 * @property string $criteria_compare_comment
 * @property string $criteria_comment
 *
 * @property Comparison $comparison
 * @property ComparisonCriteria $criteria
 */
class CarComparisonCriteria extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cars_comparisons_criteria';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comparison_id', 'criteria_id', 'criteria_main_value', 'criteria_compare_value'], 'required'],
            [['comparison_id', 'criteria_id', 'criteria_main_value', 'criteria_compare_value'], 'integer'],
            [['criteria_main_comment', 'criteria_compare_comment', 'criteria_comment'], 'string'],
            [['criteria_main_comment', 'criteria_compare_comment', 'criteria_comment'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [['comparison_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comparison::className(), 'targetAttribute' => ['comparison_id' => 'id']],
            [['criteria_id'], 'exist', 'skipOnError' => true, 'targetClass' => ComparisonCriteria::className(), 'targetAttribute' => ['criteria_id' => 'id']],
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
            'criteria_id' => Yii::t('app', 'Criteria ID'),
            'criteria_main_value' => Yii::t('app', 'Criteria Main Value'),
            'criteria_main_comment' => Yii::t('app', 'Criteria Main Comment'),
            'criteria_compare_value' => Yii::t('app', 'Criteria Compare Value'),
            'criteria_compare_comment' => Yii::t('app', 'Criteria Compare Comment'),
            'criteria_comment' => Yii::t('app', 'Criteria Comment'),
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
    public function getCriteria()
    {
        return $this->hasOne(ComparisonCriteria::className(), ['id' => 'criteria_id']);
    }
}
