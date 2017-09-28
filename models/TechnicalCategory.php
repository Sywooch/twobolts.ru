<?php

namespace app\models;

use yii;

/**
 * This is the model class for table "tech_categories".
 *
 * @property integer $id
 * @property string $category_name
 * @property integer $category_order
 *
 * @property TechnicalOption[] $options
 */
class TechnicalCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tech_categories';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_name'], 'required'],
            [['category_order'], 'integer'],
            [['category_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category_name' => Yii::t('app', 'Category Name'),
            'category_order' => Yii::t('app', 'Category Order'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptions()
    {
        return $this->hasMany(TechnicalOption::className(), ['tech_category_id' => 'id'])->orderBy(['option_order' => SORT_ASC]);
    }
}
