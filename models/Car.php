<?php

namespace app\models;

use app\components\ArrayHelper;
use app\components\ImageHelper;
use app\components\UrlHelper;
use yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "cars".
 *
 * @property integer $id
 * @property string $image
 * @property integer $manufacturer_id
 * @property integer $model_id
 * @property integer $engine_id
 *
 * @property Manufacturer $manufacturer
 * @property Model $model
 * @property Engine $engine
 * @property Comparison[] $comparisons
 * @property CarTechnical[] $technical
 * @property UserCar[] $users
 * @property News[] $news
 */

class Car extends ActiveRecord
{
    public $main_comparisons_count;
    public $main_comparisons_value;
    public $main_actual_criteria;

    public $compare_comparisons_count;
    public $compare_comparisons_value;
    public $compare_actual_criteria;

    public $comparisons_criteria;

    const CATALOG_POPULAR_COMPARISONS_COUNT = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cars';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['manufacturer_id', 'model_id'], 'required'],
            [['engine_id'], 'safe'],
            [['image'], 'string'],
            [['manufacturer_id', 'model_id', 'engine_id'], 'integer'],
            [['manufacturer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Manufacturer::className(), 'targetAttribute' => ['manufacturer_id' => 'id']],
            [['model_id'], 'exist', 'skipOnError' => true, 'targetClass' => Model::className(), 'targetAttribute' => ['model_id' => 'id']],
            [['engine_id'], 'exist', 'skipOnError' => true, 'targetClass' => Engine::className(), 'targetAttribute' => ['engine_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Car ID'),
            'image' => Yii::t('app', 'Image'),
            'manufacturer_id' => Yii::t('app/admin', 'Manufacturer'),
            'model_id' => Yii::t('app/admin', 'Model'),
            'engine_id' => Yii::t('app/admin', 'Engine'),
        ];
    }

    /**
     * @param $url
     * @return array|null|ActiveRecord|self
     */
    public static function findByUrl($url)
    {
        /** @var self $model */
        $model = self::find()->joinWith('engine')->where(['url_title' => $url])->one();

        return $model;
    }

    /**
     * @param $manufacturerId
     * @param $modelId
     * @param $engineId
     * @return null|self
     */
    public static function findByParts($manufacturerId, $modelId, $engineId)
    {
        return self::findOne([
            'manufacturer_id' => $manufacturerId,
            'model_id' => $modelId,
            'engine_id' => $engineId
        ]);
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
    public function getModel()
    {
        return $this->hasOne(Model::className(), ['id' => 'model_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEngine()
    {
        return $this->hasOne(Engine::className(), ['id' => 'engine_id']);
    }

    /**
     * @param array
     * @param int|null
     * @param int
     * @return self|array|yii\db\ActiveRecord[]|Comparison[]
     */
    public function getComparisons($orderBy = [], $limit = null, $offset = 0)
    {
        $query = Comparison::find();

        if ($limit) {
            $query->limit($limit);
            $query->offset($offset);
        }

        if ($orderBy) {
            $query->orderBy($orderBy);
        }

        return $query
            ->where('active = 1 AND (car_main_id = :carId OR car_compare_id = :carId)', [':carId' => $this->id])
            ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTechnical()
    {
        return $this->hasMany(CarTechnical::className(), ['car_id' => 'id'])
            ->joinWith('option.category')
            ->orderBy([
                TechnicalCategory::tableName() . '.category_order' => SORT_ASC,
                TechnicalOption::tableName() . '.option_order' => SORT_ASC
            ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(UserCar::className(), ['car_id' => 'id']);
    }

    /**
     * @param bool $absolute
     * @return string
     */
    public function getImage($absolute = true)
    {
    	if ($this->image) {
    		if ($absolute) {
    			return UrlHelper::absolute($this->image);
		    } else {
    			return $this->image;
		    }
	    } else {
    		return $this->model->getImage(false, $absolute);
	    }
    }

    /**
     * @param string $image
     * @return string
     */
    public static function getDefaultImage($image = '')
    {
        if ($image) {
            return UrlHelper::absolute($image);
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
        
        return UrlHelper::absolute($img);
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->manufacturer->name . ' ' . 
        $this->model->name . 
        ($this->model->body ? ' ' . $this->model->body->body_name : '') . ' ' . 
        $this->engine->engine_name . ' ' . Yii::t('app', 'Horse power');
    }

    /**
     * @param bool $asArray
     * @return array|yii\db\ActiveRecord[]|self[]
     */
    public function getClones($asArray = true)
    {
        $query = self::find()
            ->joinWith(['manufacturer', 'model', 'engine'])
            ->where([Car::tableName() . '.model_id' => $this->model_id])
            ->andWhere(['!=', Car::tableName() . '.id', $this->id])
            ->orderBy([Engine::tableName() . '.engine_name' => SORT_ASC]);

        if ($asArray) {
            $query = $query->asArray();
        }

        return $query->all();
    }

	/**
	 * @param $technical
	 */
    public function createTechnical($technical)
    {
    	if ($technical) {
		    foreach ($technical as $optionId => $optionValue) {
			    $carTechnical = new CarTechnical();
			    $carTechnical->car_id = $this->id;
			    $carTechnical->tech_option_id = $optionId;
			    $carTechnical->tech_option_value = $optionValue;

			    $carTechnical->save();

			    unset($carTechnical);
		    }
	    }
    }

	/**
	 * @param $technical
	 */
    public function updateTechnical($technical)
    {
	    if ($technical) {
		    if (!$this->technical) {
			    $this->addCarTechnical();
		    }

		    /** @var CarTechnical $item */
		    foreach ($this->getTechnical()->all() as $item)
		    {
			    if ($item->tech_option_value != $technical[$item->tech_option_id]) {
				    $item->tech_option_value = $technical[$item->tech_option_id];

				    if ($item->validate()) {
					    $item->save();
				    }
			    }
		    }
	    }
    }

    /**
     * Create car technical options
     */
    public function addCarTechnical()
    {
        /** @var TechnicalOption[] $technicalOptions */
        $technicalOptions = TechnicalOption::find()->all();

        foreach ($technicalOptions as $option)
        {
            $carTechnical = new CarTechnical();
            $carTechnical->car_id = $this->id;
            $carTechnical->tech_option_id = $option->id;
            $carTechnical->save();
        }
    }

    /**
     * @return array
     */
    public function getTechnicalOptions()
    {
        $category = [];

        if (!$this->technical) {
            $this->addCarTechnical();
        }

        /** @var CarTechnical $item */
        foreach ($this->getTechnical()->all() as $item) {
            if (!isset($category[$item->option->category->id])) {
                $category[$item->option->category->id] = [
                    'name' => $item->option->category->category_name,
                    'items' => []
                ];
            }

            $category[$item->option->category->id]['items'][$item->option->id] = [
                'name' => $item->option->option_name,
                'value' => $item->tech_option_value,
                'units' => $item->option->option_units
            ];
        }

        return $category;
    }

    /**
     * @return bool
     */
    public function hasTechnicalOptions()
    {
        if (count($this->technical) == 0) {
            return false;
        }

        $isEmpty = true;

        foreach ($this->technical as $technical)
        {
            if ($technical->tech_option_value) {
                $isEmpty = false;
            }
        }

        return !$isEmpty;
    }

    /**
     * @return array|yii\db\ActiveRecord[]|Comment[]
     */
    public function getComments()
    {
        $query = Comment::find()
            ->from(['grp' => Comment::find()->distinct('object_id')->orderBy(['created' => SORT_DESC])])
            ->join('INNER JOIN', Comparison::tableName() . ' cc', 'cc.id = grp.object_id AND (cc.car_main_id = :carId OR cc.car_compare_id = :carId)', [':carId' => $this->id])
            ->groupBy('grp.object_id')
            ->limit(self::CATALOG_POPULAR_COMPARISONS_COUNT);

        return $query->all();
    }

    /**
     * @return array|yii\db\ActiveRecord[]|News[]
     */
    public function getNews()
    {
        return News::find()
            ->select('n.*')
            ->from(self::tableName() . ' c')
            ->join('INNER JOIN', 'news_models nm', 'nm.model_id = c.model_id OR nm.manufacturer_id = c.manufacturer_id')
            ->join('INNER JOIN', News::tableName() . ' n', 'n.id = nm.news_id')
            ->where(['c.id' => $this->id])
            ->orderBy(['n.created' => SORT_DESC])
            ->limit(self::CATALOG_POPULAR_COMPARISONS_COUNT)
            ->all();
    }

    /**
     * @return array|bool
     */
    public function getAvgComparison()
    {
        $subQueryMainValue = (new yii\db\Query())
            ->select('SUM(ccct.criteria_main_value)')
            ->from(['mot' => Model::tableName()])
            ->join('INNER JOIN', self::tableName() . ' ct', 'ct.id = :carId', [':carId' => $this->id])
            ->join('INNER JOIN', Comparison::tableName() . ' cct','cct.car_main_id = ct.id AND cct.active = 1')
            ->join('INNER JOIN', CarComparisonCriteria::tableName() . ' ccct', 'ccct.comparison_id = cct.id')
            ->where('mot.id = c.model_id');

        $subQueryMainCriteria = (new yii\db\Query())
            ->select('COUNT(cct.id)')
            ->from(['mot' => Model::tableName()])
            ->join('INNER JOIN', self::tableName() . ' ct', 'ct.id = :carId', [':carId' => $this->id])
            ->join('INNER JOIN', Comparison::tableName() . ' cct','cct.car_main_id = ct.id AND cct.active = 1')
            ->join('INNER JOIN', CarComparisonCriteria::tableName() . ' ccct', 'ccct.comparison_id = cct.id')
            ->where('mot.id = c.model_id');

        $subQueryMain = (new yii\db\Query())
            ->select([
                'car_compares' => 'COUNT(c.engine_id)',
                'c.engine_id',
                'compares_value' => $subQueryMainValue,
                'compares_criteria' => $subQueryMainCriteria
            ])
            ->from(Comparison::tableName() . ' cc')
            ->join('INNER JOIN', self::tableName() . ' c', 'c.id = cc.car_main_id AND c.id = :carId', [':carId' => $this->id])
            ->where(['cc.active' => 1])
            ->groupBy('c.engine_id');

        $subQueryCompareValue = (new yii\db\Query())
            ->select('SUM(ccct.criteria_compare_value)')
            ->from(['mot' => Model::tableName()])
            ->join('INNER JOIN', self::tableName() . ' ct', 'ct.id = :carId', [':carId' => $this->id])
            ->join('INNER JOIN', Comparison::tableName() . ' cct','cct.car_compare_id = ct.id AND cct.active = 1')
            ->join('INNER JOIN', CarComparisonCriteria::tableName() . ' ccct', 'ccct.comparison_id = cct.id')
            ->where('mot.id = c.model_id');

        $subQueryCompareCriteria = (new yii\db\Query())
            ->select('COUNT(cct.id)')
            ->from(['mot' => Model::tableName()])
            ->join('INNER JOIN', self::tableName() . ' ct', 'ct.id = :carId', [':carId' => $this->id])
            ->join('INNER JOIN', Comparison::tableName() . ' cct','cct.car_compare_id = ct.id AND cct.active = 1')
            ->join('INNER JOIN', CarComparisonCriteria::tableName() . ' ccct', 'ccct.comparison_id = cct.id')
            ->where('mot.id = c.model_id');

        $subQueryCompare = (new yii\db\Query())
            ->select([
                'car_compares' => 'COUNT(c.engine_id)',
                'c.engine_id',
                'compares_value' => $subQueryCompareValue,
                'compares_criteria' => $subQueryCompareCriteria
            ])
            ->from(Comparison::tableName() . ' cc')
            ->join('INNER JOIN', self::tableName() . ' c', 'c.id = cc.car_compare_id AND c.id = :carId', [':carId' => $this->id])
            ->where(['cc.active' => 1])
            ->groupBy('c.engine_id');

        $subQueryMain->union($subQueryCompare);

        $query = (new yii\db\Query())
            ->select([
                'car_compares' => 'SUM(final.car_compares)',
                'compares_value' => 'FORMAT(SUM(final.compares_value) / SUM(final.compares_criteria), 1)'
            ])
            ->from(['final' => $subQueryMain])
            ->groupBy('final.engine_id');

        return $query->one();
    }
}
