<?php

namespace app\models;

use app\components\ImageHelper;
use app\components\UrlHelper;
use mongosoft\file\UploadBehavior;
use mongosoft\file\UploadImageBehavior;
use yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "models".
 *
 * @property integer $id
 * @property integer $manufacturer_id
 * @property integer $body_id
 * @property string $name
 * @property string $image
 * @property string $url_title
 * @property integer $is_popular
 *
 * @property Car[] $cars
 * @property Engine[] $engines
 * @property Manufacturer $manufacturer
 * @property Body $body
 * @property News[] $news
 * @property Comparison[] $comparisons
 */
class Model extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => UploadImageBehavior::className(),
                'attribute' => 'image',
                'scenarios' => ['insert', 'update'],
	            'thumbs' => [
		            'thumb' => ['width' => ImageHelper::THUMBNAIL_LARGE_WIDTH, 'height' => ImageHelper::THUMBNAIL_LARGE_HEIGHT, 'quality' => 90]
	            ],
                'path' => '@webroot/uploads/models',
                'url' => '@web/uploads/models',
                'unlinkOnDelete' => true
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'models';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['manufacturer_id', 'name'], 'required'],
            [['image', 'url_title'], 'safe'],
            [['manufacturer_id', 'body_id', 'is_popular'], 'integer'],
            [['url_title'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['manufacturer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Manufacturer::className(), 'targetAttribute' => ['manufacturer_id' => 'id']],
            [['body_id'], 'exist', 'skipOnError' => true, 'targetClass' => Body::className(), 'targetAttribute' => ['body_id' => 'body_id']],
            ['image', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['insert', 'update']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'manufacturer_id' => Yii::t('app', 'Manufacturer'),
            'body_id' => Yii::t('app/admin', 'Body'),
            'name' => Yii::t('app/admin', 'Name'),
            'image' => Yii::t('app/admin', 'Image'),
            'url_title' => Yii::t('app', 'Url Title'),
            'is_popular' => Yii::t('app/admin', 'Popular'),
        ];
    }

    public static function findLastAdded()
    {
        return self::find()
            ->innerJoinWith('cars')
            ->groupBy(['models.id'])
            ->orderBy(['cars.id' => SORT_DESC])
            ->limit(15)
            ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCars()
    {
        return $this->hasMany(Car::className(), ['model_id' => 'id']);
    }

    /**
     * @param bool
     * @return \yii\db\ActiveQuery
     */
    public function getEngines($hasCar = false)
    {
        $query = $this->hasMany(Engine::className(), ['model_id' => 'id']);

        if ($hasCar) {
            $query->joinWith('cars', true, 'INNER JOIN')->groupBy(['id']);
        }

        return  $query;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManufacturer()
    {
        return $this->hasOne(Manufacturer::className(), ['id' => 'manufacturer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBody()
    {
        return $this->hasOne(Body::className(), ['body_id' => 'body_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasMany(News::className(), ['id' => 'news_id'])
            ->viaTable('news_models', ['model_id' => 'id'])
            ->orderBy([News::tableName() . '.created' => SORT_DESC]);
    }

    /**
     * @return Comparison[]|array|yii\db\ActiveRecord[]
     */
    public function getComparisons()
    {
        return Comparison::findModelItems($this->id, 0, null);
    }

	/**
	 * @param bool $withManufacturer
	 *
	 * @return string
	 */
    public function getFullName($withManufacturer = true)
    {
        $manufacturer = [];
        if ($withManufacturer) {
            $manufacturer = [$this->manufacturer->name];
        }

        if ($this->body) {
            $bodyName = $this->body->body_name;
        } else {
            $bodyName = '';
        }

        return implode(
            ' ',
            array_merge(
                $manufacturer,
                [
                    $this->name,
                    $bodyName
                ]
            )
        );
    }

    /**
     * @param bool $path
     * @param bool $absolute
     * @return string
     */
    public function getImage($path = false, $absolute = true)
    {
        if ($this->image) {
            // without behavior
            $file = Yii::getAlias('@webroot') . $this->image;
            if (@file_get_contents($file)) {
            	if ($path) {
            		return $file;
	            }


                return $absolute ? UrlHelper::absolute($this->image) : $this->image;
            }

            // with behavior
            /** @var self|UploadBehavior $this */
            $file = Yii::getAlias('@webroot') . $this->getUploadUrl('image');
            if (file_get_contents($file)) {
            	if ($path) {
            		return $file;
	            }
                return $absolute ? UrlHelper::absolute($this->getUploadUrl('image')) : $this->getUploadUrl('image');
            }

            $img = $this->image;
        } else {
            $default = glob(Yii::$app->getBasePath() . '/' . Yii::$app->params['webRoot'] . '/uploads/default_foto.*');
            $file = isset($default[0]) ? $default[0] : null;
            if ($file) {
                $path = pathinfo($file);
                $img = '/uploads/' . $path['basename'];
            } else {
                $img = '/images/default_car_480x270.png';
            }
        }

        return $absolute ? UrlHelper::absolute($img) : $img;
    }

    /**
     * Удаляет файл иозбражения и очищает свойство
     *
     * @return $this
     */
    public function deleteImage()
    {
        if (ImageHelper::deleteImageFile($this, 'image')) {
            $this->image = '';
        }

        return $this;
    }

    /**
     * @return int|string
     */
    public function getUrl()
    {
        return $this->url_title ? $this->url_title : $this->id;
    }

    /**
     * @return string
     */
    public function getBodyName()
    {
        return $this->body ? $this->body->getName() : '';
    }

    /**
     * Список моделей для фильтра
     *
     * @param null $with
     * @param null $manufacturerId
     * @return array|ActiveRecord[]|self[]
     */
    public static function filterData($with = null, $manufacturerId = null)
    {
        $query = self::find()->joinWith('body');

        if ($with == 'comparisons') {
            $query->joinWith('cars', true, 'INNER JOIN')
                ->join(
                    'INNER JOIN',
                    Comparison::tableName() . ' cc',
                    '(cc.car_main_id = ' . Car::tableName() . '.id OR cc.car_compare_id = ' . Car::tableName() . '.id)');
        }

        if ($manufacturerId) {
            $query->where([self::tableName() . '.manufacturer_id' => $manufacturerId]);
        }

        return $query->orderBy(['name' => SORT_ASC])->all();
    }
}
