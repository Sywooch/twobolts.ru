<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "news_models".
 *
 * @property integer $news_models_id
 * @property integer $news_id
 * @property integer $manufacturer_id
 * @property integer $model_id
 *
 * @property News $news
 * @property Model $model
 * @property Manufacturer $manufacturer
 */
class NewsModels extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news_models';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['news_id'], 'required'],
            [['news_id', 'manufacturer_id', 'model_id'], 'integer'],
            [['news_id'], 'exist', 'skipOnError' => true, 'targetClass' => News::className(), 'targetAttribute' => ['news_id' => 'id']],
            [['model_id'], 'exist', 'skipOnError' => true, 'targetClass' => Model::className(), 'targetAttribute' => ['model_id' => 'id']],
            [['manufacturer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Manufacturer::className(), 'targetAttribute' => ['manufacturer_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'news_models_id' => 'News Models ID',
            'news_id' => 'News ID',
            'manufacturer_id' => 'Manufacturer ID',
            'model_id' => 'Model ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasOne(News::className(), ['id' => 'news_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModel()
    {
        return $this->hasOne(Model::className(), ['id' => 'model_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManufacturer()
    {
        return $this->hasOne(Manufacturer::className(), ['id' => 'manufacturer_id']);
    }
}
