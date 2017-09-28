<?php

namespace app\models;

use yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tech_options".
 *
 * @property integer $id
 * @property string $option_name
 * @property integer $tech_category_id
 * @property string $option_units
 * @property integer $option_order
 *
 * @property TechnicalCategory $category
 */
class TechnicalOption extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tech_options';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['option_name', 'tech_category_id'], 'required'],
            [['tech_category_id', 'option_order'], 'integer'],
            [['option_name'], 'string', 'max' => 255],
            [['option_units'], 'string', 'max' => 50],
            [['tech_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => TechnicalCategory::className(), 'targetAttribute' => ['tech_category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'option_name' => Yii::t('app/admin', 'Name'),
            'tech_category_id' => Yii::t('app/admin', 'Category'),
            'option_units' => Yii::t('app/admin', 'Option Units'),
            'option_order' => Yii::t('app', 'Option Order'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(TechnicalCategory::className(), ['id' => 'tech_category_id']);
    }
}
