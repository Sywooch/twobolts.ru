<?php

namespace app\models;

use yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "cars_tech_specs".
 *
 * @property integer $id
 * @property integer $car_id
 * @property integer $tech_option_id
 * @property string $tech_option_value
 *
 * @property Car $car
 * @property TechnicalOption $option
 */
class CarTechnical extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cars_tech_specs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['car_id', 'tech_option_id'], 'required'],
            [['car_id', 'tech_option_id'], 'integer'],
            [['tech_option_value'], 'safe'],
            [['tech_option_value'], 'string', 'max' => 255],
            [['tech_option_value'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [['car_id'], 'exist', 'skipOnError' => true, 'targetClass' => Car::className(), 'targetAttribute' => ['car_id' => 'id']],
            [['tech_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => TechnicalOption::className(), 'targetAttribute' => ['tech_option_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'car_id' => Yii::t('app', 'Car ID'),
            'tech_option_id' => Yii::t('app', 'Tech Option ID'),
            'tech_option_value' => Yii::t('app', 'Tech Option Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCar()
    {
        return $this->hasOne(Car::className(), ['id' => 'car_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOption()
    {
        return $this->hasOne(TechnicalOption::className(), ['id' => 'tech_option_id']);
    }
}
