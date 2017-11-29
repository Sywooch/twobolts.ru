<?php

namespace app\models;

use app\components\ArrayHelper;
use app\components\UrlHelper;
use app\components\widgets\ComparisonList;
use yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Inflector;
use yii\helpers\Url;

/**
 * This is the model class for table "cars_comparisons".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $car_main_id
 * @property integer $car_compare_id
 * @property string $date
 * @property string $comment
 * @property string $main_foto
 * @property string $compare_foto
 * @property string $main_time
 * @property string $compare_time
 * @property integer $show_on_home
 * @property string $url_title
 * @property integer $active
 * @property double $rating
 * @property integer $views
 *
 * @property User $user
 * @property Car $carMain
 * @property Car $carCompare
 * @property CarComparisonCriteria[] $criteria
 * @property Comment[] $comments
 * @property ComparisonThanks[] $thanks
 * @property ComparisonThanks[] $dislikes
 * @property UserFavorite[] $favorites
 * @property UserCommentKarma[] $karma
 */
class Comparison extends UserDependency
{
    const MAX_CRITERIA = 10;
    const COMMENTS_PER_PAGE = 10;
    
    public $calculatedRating = 0;
    public $calculatedComments = 0;

    public $updated_at;
    public $customError;

    public static $statusLabels = [
        0 => 'На модерации',
        1 => 'Активное'
    ];

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
                    'createdAtAttribute' => 'date',
                    'value' => date('Y-m-d H:i:s')
                ],
                [
                    'class' => SluggableBehavior::className(),
                    //'value' => [$this, 'prepareUrl'],
                    'ensureUnique' => true,
                    'slugAttribute' => 'url_title',
                    'attribute' => 'url_title'
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cars_comparisons';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['car_main_id', 'car_compare_id', 'main_time', 'compare_time'], 'required'],
            [['car_main_id', 'car_compare_id', 'show_on_home', 'active', 'views'], 'integer'],
            [['comment', 'main_foto', 'compare_foto'], 'safe'],
            [['comment', 'main_foto', 'compare_foto'], 'string'],
            [['comment'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [['rating'], 'number'],
            [['main_time', 'compare_time'], 'string', 'max' => 100],
            [['car_main_id'], 'exist', 'skipOnError' => true, 'targetClass' => Car::className(), 'targetAttribute' => ['car_main_id' => 'id']],
            [['car_compare_id'], 'exist', 'skipOnError' => true, 'targetClass' => Car::className(), 'targetAttribute' => ['car_compare_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t('app', 'User'),
            'car_main_id' => 'Car Main ID',
            'car_compare_id' => 'Car Compare ID',
            'date' => Yii::t('app', 'Date'),
            'comment' => 'Comment',
            'main_foto' => 'Main Foto',
            'compare_foto' => 'Compare Foto',
            'main_time' => 'Main Time',
            'compare_time' => 'Compare Time',
            'show_on_home' => 'Show On Home',
            'url_title' => 'Url Title',
            'active' => Yii::t('app', 'Status'),
            'rating' => Yii::t('app', 'Rating'),
            'views' => 'Views',
        ];
    }

    /**
     * @param bool
     * @return yii\db\ActiveQuery
     */
    public static function getQuery($activeOnly = true)
    {
        $query = self::find()
            ->addSelect('cc.*, ' . self::getQueryRating() . ' as calculatedRating, ' . self::getQueryComments() . ' as calculatedComments')
            ->from(self::tableName() . ' AS cc');

        if ($activeOnly || User::identity()->isAdmin()) {
            $query->andWhere(['cc.active' => 1]);
        }

        return $query;
    }

    public static function findById($id)
    {
        return self::getQuery()
            ->where('id = :id', [':id' => $id])
            ->one();
    }

    public static function findByUrl($url)
    {
        return self::getQuery()
            ->where('url_title = :url', [':url' => $url])
            ->one();
    }


    /**
     * @param array $params
     * [
     *     'mainCar' => [
     *         'manufacturerId' => ...,
     *         'modelId' => ...,
     *         'engineId' => ...,
     *     ],
     *     'compareCar' => [
     *         'manufacturerId' => ...,
     *         'modelId' => ...,
     *         'engineId' => ...,
     *     ]
     * ]
     * @param bool
     * @return yii\db\ActiveRecord|self|null
     */
    public static function findByCarParts($params = [], $byUser = false)
    {
        $mainParams = ArrayHelper::getValue($params, 'mainCar');
        if (!$mainParams) {
            return null;
        }

        $mainCar = Car::findByParts(
            ArrayHelper::getValue($mainParams, 'manufacturerId'),
            ArrayHelper::getValue($mainParams, 'modelId'),
            ArrayHelper::getValue($mainParams, 'engineId')
        );
        if (!$mainCar) {
            return null;
        }

        $compareParams = ArrayHelper::getValue($params, 'compareCar');
        if (!$compareParams) {
            return null;
        }

        $compareCar = Car::findByParts(
            ArrayHelper::getValue($compareParams, 'manufacturerId'),
            ArrayHelper::getValue($compareParams, 'modelId'),
            ArrayHelper::getValue($compareParams, 'engineId')
        );
        if (!$compareCar) {
            return null;
        }

        return self::findByCars($mainCar->id, $compareCar->id, $byUser);
    }

    public static function findByCars($carMainId, $carCompareId, $byUser = false)
    {
        $condition = [
            'car_main_id' => $carMainId,
            'car_compare_id' => $carCompareId
        ];

        if ($byUser) {
            $condition = ArrayHelper::merge($condition, ['user_id' => Yii::$app->user->getId()]);
        }

        return self::findOne($condition);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarMain()
    {
        return $this->hasOne(Car::className(), ['id' => 'car_main_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarCompare()
    {
        return $this->hasOne(Car::className(), ['id' => 'car_compare_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCriteria()
    {
        return $this->hasMany(CarComparisonCriteria::className(), ['comparison_id' => 'id'])
            ->joinWith('criteria')
            ->orderBy([ComparisonCriteria::tableName() . '.sort_order' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['object_id' => 'id'])
            ->andWhere(['object' => self::className()])
            ->orderBy(['created' => SORT_ASC]);
    }

    /**
     * @return int
     */
    public function getCommentsCount()
    {
        return $this->hasMany(Comment::className(), ['object_id' => 'id'])
            ->andWhere(['object' => self::className()])
            ->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getThanks()
    {
        return $this->hasMany(ComparisonThanks::className(), ['comparison_id' => 'id'])->andWhere(['dislike' => '0']);
    }

    /**
     * @return int
     */
    public function getThanksCount()
    {
        return $this->hasMany(ComparisonThanks::className(), ['comparison_id' => 'id'])->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDislikes()
    {
        return $this->hasMany(ComparisonThanks::className(), ['comparison_id' => 'id'])->andWhere(['dislike' => '1']);
    }

    /**
     * @return int
     */
    public function getDislikesCount()
    {
        return $this->hasMany(ComparisonThanks::className(), ['comparison_id' => 'id'])->andWhere(['dislike' => '1'])->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavorites()
    {
        return $this->hasMany(UserFavorite::className(), ['comparison_id' => 'id']);
    }

    /**
     * @return int
     */
    public function getFavoritesCount()
    {
        return $this->hasMany(UserFavorite::className(), ['comparison_id' => 'id'])->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKarma()
    {
        return $this->hasMany(UserCommentKarma::className(), ['comparison_id' => 'id']);
    }

    /**
     * @param string $type
     * @return int|string
     */
    public function getGrade($type)
    {
        $totalCriteria = count($this->criteria);
        $sum = 0;
        $property = 'criteria_' . $type . '_value';
        foreach ($this->criteria as $criteria)
        {
            $sum += $criteria->{$property};
        }

        if ($totalCriteria) {
            return number_format($sum / $totalCriteria, 1);
        }

        return 0;
    }

    public function getShortName()
    {
        return implode(' ', [
            $this->carMain->manufacturer->name,
            $this->carMain->model->name,
            $this->carMain->engine->engine_name,
            Yii::t('app', 'Horse power'),
            Yii::t('app', 'and'),
            $this->carCompare->manufacturer->name,
            $this->carCompare->model->name,
            $this->carCompare->engine->engine_name,
            Yii::t('app', 'Horse power')
        ]);
    }

    public function getFullName()
    {
        return implode(' ', [
            $this->carMain->manufacturer->name,
            $this->carMain->model->name,
            $this->carMain->model->body->body_name,
            $this->carMain->engine->engine_name,
            Yii::t('app', 'Horse power'),
            Yii::t('app', 'and'),
            $this->carCompare->manufacturer->name,
            $this->carCompare->model->name,
            $this->carCompare->model->body->body_name,
            $this->carCompare->engine->engine_name,
            Yii::t('app', 'Horse power')
        ]);
    }

    public function getCarName($type)
    {
        $carProp = 'car' . ucfirst($type);
        /** @var Car $car */
        $car = $this->{$carProp};

        return $car->getFullName();
    }

    /**
     * @param string
     * @return string
     */
    public static function getQueryRating($alias = 'cc.')
    {
        return '
        (
			' . $alias . 'rating + 
			(' . $alias . 'views * 0.01) + 
			(
				SELECT COUNT(rcc.id) * 0.1 
				FROM ' . Comment::tableName() . ' rcc
				WHERE rcc.object_id	= ' . $alias . 'id AND rcc.object = \'' . Comparison::className() . '\'
			) + 
			(
				SELECT COUNT(ruf.favorite_id) * 0.2 
				FROM ' . UserFavorite::tableName() . ' ruf
				WHERE ruf.comparison_id = ' . $alias . 'id
			) + 
			(
				((
					SELECT COUNT(cul.id) 
					FROM ' . ComparisonThanks::tableName() . ' cul 
					WHERE cul.dislike = 0 AND cul.comparison_id = ' . $alias . 'id
				) - 
				(
					SELECT COUNT(cud.id) 
					FROM ' . ComparisonThanks::tableName() . ' cud 
					WHERE cud.dislike = 1 AND cud.comparison_id = ' . $alias . 'id
				)) * 0.1
			)
		)';
    }

    /**
     * @param string
     * @return string
     */
    public static function getQueryComments($alias = 'cc.')
    {
        return '
        (
            SELECT COUNT(rcc.id) 
            FROM ' . Comment::tableName() . ' rcc
            WHERE rcc.object_id	= ' . $alias . 'id  AND rcc.object = \'' . addslashes(Comparison::className()) . '\'
		)';
    }

    private static function getOrderBy($orderBy)
    {
        switch ($orderBy) {
            case 'rating': $sorting = 'calculatedRating'; break;
            case 'comments': $sorting = 'calculatedComments'; break;
            default: $sorting = $orderBy; break;
        }

        return $sorting;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param string $orderBy
     * @return array|\yii\db\ActiveRecord[]|self[]
     */
    public static function findItems($offset = 0, $limit = ComparisonList::ITEMS_PER_PAGE, $orderBy = 'date')
    {
        return self::getQuery()
            ->orderBy([self::getOrderBy($orderBy) => SORT_DESC])
            ->offset($offset)
            ->limit($limit)
            ->all();
    }

    /**
     * @return int
     */
    public static function getItemsCount()
    {
        return self::find()
            ->where(['active' => 1])
            ->count();
    }

    /**
     * @param int $manufacturerId
     * @param int $offset
     * @param int $limit
     * @param string $orderBy
     * @return array|yii\db\ActiveRecord[]|self[]
     */
    public static function findManufacturerItems($manufacturerId, $offset = 0, $limit = ComparisonList::ITEMS_PER_PAGE, $orderBy = 'date')
    {
        $queryMain = self::getQuery();
        $queryMain
            ->joinWith('carMain', true, 'INNER JOIN')
            ->join('INNER JOIN', Manufacturer::tableName() . ' AS ma', 'ma.id = cars.manufacturer_id AND ma.id = :manufacturerId', [':manufacturerId' => $manufacturerId]);

        $queryCompare = self::getQuery();
        $queryCompare
            ->joinWith('carCompare', true, 'INNER JOIN')
            ->join('INNER JOIN', Manufacturer::tableName() . ' AS ma', 'ma.id = cars.manufacturer_id AND ma.id = :manufacturerId', [':manufacturerId' => $manufacturerId]);

        $queryMain->union($queryCompare, true)
            ->orderBy([self::getOrderBy($orderBy) => SORT_DESC]);

        return self::find()
            ->select('*')
            ->from(['u' => $queryMain])
            ->orderBy([self::getOrderBy($orderBy) => SORT_DESC])
            ->groupBy('id')
            ->offset($offset)
            ->limit($limit)
            ->all();
    }

    /**
     * @param int $manufacturerId
     * @return int
     */
    public static function getManufacturerItemsCount($manufacturerId)
    {
        $queryMain = self::getQuery();
        $queryMain
            ->joinWith('carMain', true, 'INNER JOIN')
            ->join('INNER JOIN', Manufacturer::tableName() . ' AS ma', 'ma.id = cars.manufacturer_id AND ma.id = :manufacturerId', [':manufacturerId' => $manufacturerId]);

        $queryCompare = self::getQuery();
        $queryCompare
            ->joinWith('carCompare', true, 'INNER JOIN')
            ->join('INNER JOIN', Manufacturer::tableName() . ' AS ma', 'ma.id = cars.manufacturer_id AND ma.id = :manufacturerId', [':manufacturerId' => $manufacturerId]);

        $queryMain->union($queryCompare, true);

        return self::find()->select('*')->from(['u' => $queryMain])->groupBy('id')->count();
    }
    
    /**
     * @param int $modelId
     * @param int $offset
     * @param int $limit
     * @param string $orderBy
     * @return array|yii\db\ActiveRecord[]|self[]
     */
    public static function findModelItems($modelId, $offset = 0, $limit = ComparisonList::ITEMS_PER_PAGE, $orderBy = 'date')
    {
        $queryMain = self::getQuery();
        $queryMain
            ->joinWith('carMain', true, 'INNER JOIN')
            ->join('INNER JOIN', Model::tableName() . ' AS mo', 'mo.id = cars.model_id AND mo.id = :modelId', [':modelId' => $modelId]);

        $queryCompare = self::getQuery();
        $queryCompare
            ->joinWith('carCompare', true, 'INNER JOIN')
            ->join('INNER JOIN', Model::tableName() . ' AS mo', 'mo.id = cars.model_id AND mo.id = :modelId', [':modelId' => $modelId]);

        $queryMain->union($queryCompare, true)
            ->orderBy([self::getOrderBy($orderBy) => SORT_DESC]);

        return self::find()
            ->select('*')
            ->from(['u' => $queryMain])
            ->orderBy([self::getOrderBy($orderBy) => SORT_DESC])
            ->groupBy('id')
            ->offset($offset)
            ->limit($limit)
            ->all();
    }

    /**
     * @param int $modelId
     * @return int
     */
    public static function getModelItemsCount($modelId)
    {
        $queryMain = self::getQuery();
        $queryMain
            ->joinWith('carMain', true, 'INNER JOIN')
            ->join('INNER JOIN', Model::tableName() . ' AS mo', 'mo.id = cars.model_id AND mo.id = :modelId', [':modelId' => $modelId]);

        $queryCompare = self::getQuery();
        $queryCompare
            ->joinWith('carCompare', true, 'INNER JOIN')
            ->join('INNER JOIN', Model::tableName() . ' AS mo', 'mo.id = cars.model_id AND mo.id = :modelId', [':modelId' => $modelId]);

        $queryMain->union($queryCompare, true);

        return self::find()->select('*')->from(['u' => $queryMain])->groupBy('id')->count();
    }

    /**
     * @param int $engineId
     * @param int $offset
     * @param int $limit
     * @param string $orderBy
     * @return array|yii\db\ActiveRecord[]|self[]
     */
    public static function findEngineItems($engineId, $offset = 0, $limit = ComparisonList::ITEMS_PER_PAGE, $orderBy = 'date')
    {
        $queryMain = self::getQuery();
        $queryMain
            ->joinWith('carMain', true, 'INNER JOIN')
            ->join('INNER JOIN', Engine::tableName() . ' AS e', 'e.id = cars.engine_id AND e.id = :engineId', [':engineId' => $engineId]);

        $queryCompare = self::getQuery();
        $queryCompare
            ->joinWith('carCompare', true, 'INNER JOIN')
            ->join('INNER JOIN', Engine::tableName() . ' AS e', 'e.id = cars.engine_id AND e.id = :engineId', [':engineId' => $engineId]);

        $queryMain->union($queryCompare, true)
            ->orderBy([self::getOrderBy($orderBy) => SORT_DESC]);

        return self::find()
            ->select('*')
            ->from(['u' => $queryMain])
            ->orderBy([self::getOrderBy($orderBy) => SORT_DESC])
            ->groupBy('id')
            ->offset($offset)
            ->limit($limit)
            ->all();
    }

    /**
     * @param int $engineId
     * @return int
     */
    public static function getEngineItemsCount($engineId)
    {
        $queryMain = self::getQuery();
        $queryMain
            ->joinWith('carMain', true, 'INNER JOIN')
            ->join('INNER JOIN', Engine::tableName() . ' AS e', 'e.id = cars.engine_id AND e.id = :engineId', [':engineId' => $engineId]);

        $queryCompare = self::getQuery();
        $queryCompare
            ->joinWith('carCompare', true, 'INNER JOIN')
            ->join('INNER JOIN', Engine::tableName() . ' AS e', 'e.id = cars.engine_id AND e.id = :engineId', [':engineId' => $engineId]);

        $queryMain->union($queryCompare, true);

        return self::find()->select('*')->from(['u' => $queryMain])->groupBy('id')->count();
    }

    /**
     * @param int $userId
     * @param int $offset
     * @param int $limit
     * @param string $orderBy
     * @return array|yii\db\ActiveRecord[]|self[]
     */
    public static function findUserItems($userId, $offset = 0, $limit = ComparisonList::ITEMS_PER_PAGE, $orderBy = 'date')
    {
        return self::getQuery()
            ->andWhere('user_id = :userId', [':userId' => $userId])
            ->orderBy([self::getOrderBy($orderBy) => SORT_DESC])
            ->offset($offset)
            ->limit($limit)
            ->all();
    }

    /**
     * @param int $userId
     * @return int
     */
    public static function getUserItemsCount($userId)
    {
        return self::getQuery()
            ->andWhere('user_id = :userId', [':userId' => $userId])
            ->count();
    }

    /**
     * @return array|yii\db\ActiveRecord|self
     */
    public static function getHomeComparison()
    {
        /** @var self $model */
        $model = self::find()
            ->where(['show_on_home' => 1, 'active' => 1])
            ->orderBy(new Expression('rand()'))
            ->one();

        if (!$model) {
            $model = self::find()
                ->where(['active' => 1])
                ->orderBy(new Expression('rand()'))
                ->one();
        }

        return $model;
    }
    
    /**
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]|self[]
     */
    public static function getLastComparisons($limit = 3)
    {
        return self::find()
            ->addSelect(Comparison::tableName() . '.*, ' . Comparison::getQueryRating('') . ' as calculatedRating')
            ->andWhere(['active' => 1])
            ->andWhere(['!=', 'show_on_home', 1])
            ->orderBy(['date' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    public static function getTopComparisons()
    {
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand('
            SELECT 
				SUM(final.model_compares) AS model_compares, 
				FORMAT(SUM(final.compares_value) / SUM(final.compares_criterias), 1) AS compares_value,
				final.manufacturer_name, 
				final.model_name,
				final.model_image,
				final.body_name, 
				final.url_title, 
				final.model_id 
			FROM 
			(
				(SELECT 
					COUNT(c.model_id) AS model_compares, 
					ma.name AS manufacturer_name, 
					mo.name AS model_name,
					mo.image AS model_image,
					b.body_name, 
					mo.url_title, 
					mo.id AS model_id, 
					(
						SELECT SUM(ccct.criteria_main_value)
						FROM models AS mot 
						JOIN cars AS ct ON ct.model_id = mot.id
						JOIN cars_comparisons AS cct ON cct.car_main_id = ct.id AND cct.active = 1
						JOIN cars_comparisons_criteria AS ccct ON ccct.comparison_id = cct.id 
						WHERE mot.id = c.model_id
					) AS compares_value,
					(
						SELECT COUNT(cct.id)
						FROM models AS mot
						JOIN cars AS ct ON ct.model_id = mot.id
						JOIN cars_comparisons AS cct ON cct.car_main_id = ct.id AND cct.active = 1
						JOIN cars_comparisons_criteria AS ccct ON ccct.comparison_id = cct.id
						WHERE mot.id = c.model_id
					) AS compares_criterias
					FROM (cars_comparisons AS cc) 
					JOIN cars AS c ON c.id = cc.car_main_id 
					JOIN manufacturers AS ma ON ma.id = c.manufacturer_id 
					JOIN models AS mo ON mo.id = c.model_id 
					LEFT JOIN bodies AS b ON b.body_id = mo.body_id 
					WHERE cc.active = 1 
					GROUP BY c.model_id 
					ORDER BY model_compares DESC, compares_value DESC
				) 
				
				UNION 
				
				(SELECT 
					COUNT(c.model_id) AS model_compares, 
					ma.name AS manufacturer_name, 
					mo.name AS model_name,
					mo.image AS model_image,
					b.body_name, 
					mo.url_title, 
					mo.id AS model_id, 
					(
						SELECT SUM(ccct.criteria_compare_value)
						FROM models AS mot JOIN cars AS ct ON ct.model_id = mot.id 
						JOIN cars_comparisons AS cct ON cct.car_compare_id = ct.id AND cct.active = 1
						JOIN cars_comparisons_criteria AS ccct ON ccct.comparison_id = cct.id WHERE mot.id = c.model_id
					) AS compares_value,
					(
						SELECT COUNT(cct.id)
						FROM models AS mot JOIN cars AS ct ON ct.model_id = mot.id
						JOIN cars_comparisons AS cct ON cct.car_compare_id = ct.id AND cct.active = 1
						JOIN cars_comparisons_criteria AS ccct ON ccct.comparison_id = cct.id WHERE mot.id = c.model_id
					) AS compares_criterias
					FROM (cars_comparisons AS cc) 
					JOIN cars AS c ON c.id = cc.car_compare_id 
					JOIN manufacturers AS ma ON ma.id = c.manufacturer_id 
					JOIN models AS mo ON mo.id = c.model_id 
					LEFT JOIN bodies AS b ON b.body_id = mo.body_id 
					WHERE cc.active = 1 
					GROUP BY c.model_id 
					ORDER BY model_compares DESC, compares_value DESC
				)
			) as final 
			GROUP BY final.model_id 
			ORDER BY model_compares DESC, compares_value DESC
			LIMIT 11
        ');
        
        return $command->queryAll();
    }

    public function isLikable()
    {
        if (Yii::$app->user->isGuest || Yii::$app->user->identity->getId() == $this->user->id) {
            return false;
        }

        foreach ($this->thanks as $thanks)
        {
            if ($thanks->user_id == Yii::$app->user->identity->getId()) {
                return false;
            }
        }

        foreach ($this->dislikes as $dislike)
        {
            if ($dislike->user_id == Yii::$app->user->identity->getId()) {
                return false;
            }
        }

        return true;
    }

    public function isFavorite()
    {
        foreach ($this->favorites as $favorite)
        {
            if (!Yii::$app->user->isGuest && $favorite->user_id == Yii::$app->user->identity->getId()) {
                return true;
            }
        }

        return false;
    }

    public function canFavorite()
    {
        if (Yii::$app->user->isGuest || Yii::$app->user->identity->getId() == $this->user->id) {
            return false;
        }
        
        return !$this->isFavorite();
    }

    /**
     * @return array
     */
    public static function getComparisonTimes()
    {
        $comparisonTime1Year = Yii::t('app', 'Comparison time less 1 year');
        $comparisonTime2Years = Yii::t('app', 'Comparison time 2 years');
        $comparisonTime6Years = Yii::t('app', 'Comparison time 6 years');
        $comparisonTimeManyYears = Yii::t('app', 'Comparison time more 6 years');

        return [
            $comparisonTime1Year => $comparisonTime1Year,
            $comparisonTime2Years => $comparisonTime2Years,
            $comparisonTime6Years => $comparisonTime6Years,
            $comparisonTimeManyYears => $comparisonTimeManyYears
        ];
    }

    public static function add($postData)
    {
        $carMain = Car::findByParts(
            $postData['mainManufacturerId'],
            $postData['mainModelId'],
            $postData['mainEngineId']
        );
        $carCompare = Car::findByParts(
            $postData['compareManufacturerId'],
            $postData['compareModelId'],
            $postData['compareEngineId']
        );

        if ($carMain == null || $carCompare == null) {
            return false;
        }

        // Check if user already compare these cars
        $model = self::findByCars($carMain->id, $carCompare->id, true);

        if ($model) {
            $model->customError = Yii::t('app', 'Already compare these cars');
            return false;
        }

        $model = new self();

        $model->car_main_id = $carMain->id;
        $model->car_compare_id = $carCompare->id;
        $model->rating = 0;
        $model->active = 0;
        $model->show_on_home = 0;
        $model->views = 0;
        $model->url_title = $model->prepareUrl();

        $model->main_foto = $model->prepareImage($postData['mainPhoto']);
        $model->compare_foto = $model->prepareImage($postData['comparePhoto']);

        $model->main_time = $postData['mainTime'];
        $model->compare_time = $postData['compareTime'];

        $garage = ArrayHelper::getValue($postData, 'garage');
        $before = ArrayHelper::getValue($postData, 'before');

        $user = User::identity();
        if ($garage) {
        	foreach ($garage as $garageItem) {
		        $user->updateCar(${'car' . ucfirst($garageItem)}->id, 'garage', $model->{$garageItem . '_foto'});
	        }
        }
        if ($before) {
	        foreach ($before as $beforeItem)
	        {
		        $user->updateCar(${'car' . ucfirst($beforeItem)}->id, 'before', $model->{$beforeItem . '_foto'});
	        }
        }

        switch ($model->main_time) {
            case 'До 1 года': $model->rating += .2; break;
            case '1-2 года': $model->rating += .4; break;
            case '3-6 лет': $model->rating += .6; break;
            case 'Более 6 лет': $model->rating += 1; break;
        }
        switch ($model->compare_time) {
            case 'До 1 года': $model->rating += .2; break;
            case '1-2 года': $model->rating += .4; break;
            case '3-6 лет': $model->rating += .6; break;
            case 'Более 6 лет': $model->rating += 1; break;
        }

        $model->comment = $postData['comment'];
        if (strlen($model->comment) >= 140) {
            $model->rating += 1;
        }  else if (strlen($model->comment) > 0) {
            $model->rating += .5;
        }

        $criteriaSuccess = true;
        if ($model->validate() && $model->save()) {
            $criteria = $postData['criteria'];
            foreach ($criteria as $item)
            {
                $comparisonCriteria = new CarComparisonCriteria();
                $comparisonCriteria->load(['CarComparisonCriteria' => $item]);
                $comparisonCriteria->comparison_id = $model->id;

                if (strlen($comparisonCriteria->criteria_comment) >= 140) {
                    $model->rating += .5;
                } else if (strlen($comparisonCriteria->criteria_comment) > 0) {
                    $model->rating += .2;
                }

                if (!$comparisonCriteria->validate() || !$comparisonCriteria->save()) {
                    $criteriaSuccess = false;
                }
            }

            if ($criteriaSuccess) {
                $model->save();

                Yii::$app->mailer->compose('new_comparison', ['model' => $model, 'user' => $user])
                    ->setFrom(Yii::$app->params['adminEmail'])
                    ->setTo(Yii::$app->params['adminEmail'])
                    ->setSubject(Yii::$app->name . ' - ' . Yii::t('app', 'New comparison added'))
                    ->send();
            } else {
                $model->delete();
                $model->customError = Yii::t('app/error', 'Error occurred while adding comparison');
            }
        }

        return $model;
    }

    public function prepareImage($imagePath)
    {
        if (!Url::isRelative($imagePath)) {
            $imagePath = str_replace(Url::home(true), '/', $imagePath);
        }

        $isTemp = strpos($imagePath, 'temp');
        if ($isTemp !== false) {
            $newPath = str_replace('temp', 'compares', $imagePath);
            $old = Yii::$app->basePath . '/' . Yii::$app->params['webRoot'] . $imagePath;
            $new = Yii::$app->basePath . '/' . Yii::$app->params['webRoot'] . $newPath;
            if (copy($old, $new)) {
                unlink($old);
                $imagePath = $newPath;
            }

            $this->rating += .5;
        }

        return $imagePath;
    }

    public function prepareUrl()
    {
        $string = $this->carMain->model->getFullName() . ' ' .
            $this->carMain->engine->engine_name .
            ' vs ' .
            $this->carCompare->model->getFullName() . ' ' .
            $this->carCompare->engine->engine_name;

        return Inflector::transliterate($string, 'Russian-Latin/BGN; NFKD');
    }

    public function getImage($type)
    {
        $imageProp = $type . '_foto';
        $carProp = 'car' . ucfirst($type);
        /** @var Car $car */
        $car = $this->{$carProp};

        return $this->{$imageProp} ? UrlHelper::absolute($this->{$imageProp}) : $car->getImage();
    }

    public function getUrl()
    {
        $url = $this->url_title ? $this->url_title : $this->id;
        return UrlHelper::absolute('comparison/view/' . $url);
    }

    public static function findRatingRange()
    {
        return (new yii\db\Query())
            ->select('MIN(rating) AS min, MAX(rating) AS max')
            ->from(self::tableName())
            ->one();
    }

    public function recalculateRating()
    {
        $rating = 0;

        $isUpload = strpos($this->main_foto, 'compares');
        if ($isUpload !== false) {
            $rating += .5;
        }

        $isUpload = strpos($this->compare_foto, 'compares');
        if ($isUpload !== false) {
            $rating += .5;
        }

        switch ($this->main_time) {
            case 'До 1 года': $rating += .2; break;
            case '1-2 года': $rating += .4; break;
            case '3-6 лет': $rating += .6; break;
            case 'Более 6 лет': $rating += 1; break;
        }

        switch ($this->compare_time) {
            case 'До 1 года': $rating += .2; break;
            case '1-2 года': $rating += .4; break;
            case '3-6 лет': $rating += .6; break;
            case 'Более 6 лет': $rating += 1; break;
        }

        if (strlen($this->comment) >= 140) {
            $rating += 1;
        } else if (strlen($this->comment) > 0) {
            $rating += .5;
        }

        if ($this->criteria) {
            foreach ($this->criteria as $criteria)
            {
                if (strlen($criteria->criteria_comment) >= 140) {
                    $rating += .5;
                } else if (strlen($criteria->criteria_comment) > 0) {
                    $rating += .2;
                }
            }
        }

        $this->rating = $rating;
        $this->save();
    }
}
