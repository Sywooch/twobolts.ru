<?php

namespace app\models;

use yii;
use yii\helpers\Inflector;

/**
 * This is the model class for table "engines".
 *
 * @property integer $id
 * @property integer $model_id
 * @property string $engine_name
 * @property string $url_title
 *
 * @property Car[] $cars
 * @property Model $model
 * @property Comparison[] $comparisons
 */
class Engine extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'engines';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model_id', 'engine_name'], 'required'],
            [['model_id'], 'integer'],
            [['url_title'], 'string'],
            [['engine_name'], 'string', 'max' => 100],
            [['model_id'], 'exist', 'skipOnError' => true, 'targetClass' => Model::className(), 'targetAttribute' => ['model_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'model_id' => Yii::t('app', 'Model'),
            'engine_name' => Yii::t('app/admin', 'Engine Name'),
            'url_title' => Yii::t('app', 'Url Title'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCars()
    {
        return $this->hasMany(Car::className(), ['engine_id' => 'id']);
    }


    /**
     * @return Comparison[]|array|yii\db\ActiveRecord[]
     */
    public function getComparisons()
    {
        return Comparison::findEngineItems($this->id, 0, null);
    }

    /**
     * @return int
     */
    public function getComparisonsCount()
    {
        $result = 0;
        foreach ($this->cars as $car)
        {
            $result += count($car->comparisons);
        }

        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModel()
    {
        return $this->hasOne(Model::className(), ['id' => 'model_id']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->engine_name . ' ' . Yii::t('app', 'Horse power');
    }

    /**
     * @return int|string
     */
    public function getUrl()
    {
        return $this->url_title ? $this->url_title : $this->id;
    }

    /**
     * Generate URL and set url_title
     */
    public function setUrlTitle()
    {
        $source = $this->model->getFullName() . ' ' . $this->engine_name;
        $this->url_title = Inflector::slug(Inflector::transliterate($source, 'Russian-Latin/BGN; NFKD'));
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        parent::beforeSave($insert);

        if (!$this->url_title) {
            $this->setUrlTitle();
        }

        return true;
    }

    /**
     * Creates Car model after Engine inserted
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $car = new Car();

            $car->manufacturer_id = $this->model->manufacturer_id;
            $car->model_id = $this->model_id;
            $car->engine_id = $this->id;

            $car->save();
        }
    }
}
