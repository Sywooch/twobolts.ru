<?php

namespace app\models;

use app\components\ArrayHelper;
use yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "manufacturers".
 *
 * @property integer $id
 * @property string $name
 * @property string $url_title
 * @property integer $is_popular
 *
 * @property Car[] $cars
 * @property Model[] $models
 * @property News[] $news
 */
class Manufacturer extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manufacturers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'url_title'], 'string'],
            [['is_popular'], 'integer'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app/admin', 'Name'),
            'url_title' => Yii::t('app', 'Url Title'),
            'is_popular' => Yii::t('app/admin', 'Popular'),
        ];
    }

    /**
     * @param bool $hasCar
     * @return array|ActiveRecord[]
     */
    public static function findModels($hasCar = false)
    {
        $query = self::find();

        if ($hasCar) {
            $query->innerJoinWith('cars');
        }

        $query->orderBy(['name' => SORT_ASC]);

        return $query->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCars()
    {
        return $this->hasMany(Car::className(), ['manufacturer_id' => 'id']);
    }

    /**
     * @param bool
     * @return \yii\db\ActiveQuery
     */
    public function getModels($hasCar = false)
    {
        $query = $this->hasMany(Model::className(), ['manufacturer_id' => 'id'])->joinWith('body')->orderBy(['name' => SORT_ASC]);

        if ($hasCar) {
            $query->joinWith('cars', true, 'INNER JOIN')->groupBy(['id']);
        }

        return $query;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasMany(News::className(), ['id' => 'news_id'])
            ->viaTable('news_models', ['manufacturer_id' => 'id'])
            ->orderBy([News::tableName() . '.created' => SORT_DESC]);
    }

    /**
     * @param $orderBy
     * @return array
     */
    public function findCars($orderBy)
    {
        /** @var Car[] $cars */
        $query = Car::find()
            ->select([
                'c.*',
                '(
                    SELECT COUNT(cc1.id) 
                    FROM '.Comparison::tableName().' as cc1 
                    WHERE cc1.car_main_id = c.id AND cc1.active = 1
                ) as main_comparisons_count',
                '(
                    SELECT SUM(ccc.criteria_main_value) FROM '.Comparison::tableName().' AS cc
			        JOIN '.CarComparisonCriteria::tableName().' AS ccc ON ccc.comparison_id = cc.id
			        WHERE cc.car_main_id = c.id AND cc.active = 1
			        GROUP BY cc.car_main_id
			    ) as main_comparisons_value',
                '(
                    SELECT COUNT(ccc.id) FROM '.Comparison::tableName().' AS cc
			        JOIN '.CarComparisonCriteria::tableName().' AS ccc ON ccc.comparison_id = cc.id
			        WHERE cc.car_main_id = c.id AND cc.active = 1
			        GROUP BY cc.car_main_id
                ) as main_actual_criteria',
                '(
                    SELECT COUNT(cc1.id) 
                    FROM '.Comparison::tableName().' as cc1 
                    WHERE cc1.car_compare_id = c.id  AND cc1.active = 1
			    ) as compare_comparisons_count',
                '(
                    SELECT SUM(ccc.criteria_compare_value) FROM '.Comparison::tableName().' AS cc
			        JOIN '.CarComparisonCriteria::tableName().' AS ccc ON ccc.comparison_id = cc.id
			        WHERE cc.car_compare_id = c.id  AND cc.active = 1
			        GROUP BY cc.car_compare_id
			    ) as compare_comparisons_value',
                '(
                    SELECT COUNT(ccc.id) FROM '.Comparison::tableName().' AS cc
			        JOIN '.CarComparisonCriteria::tableName().' AS ccc ON ccc.comparison_id = cc.id
			        WHERE cc.car_compare_id = c.id AND cc.active = 1
			        GROUP BY cc.car_compare_id
                ) as compare_actual_criteria',
                '(
                    SELECT COUNT(id) FROM '.ComparisonCriteria::tableName().'
			    ) as comparisons_criteria'
            ])
            ->from(Car::tableName() . ' as c')
            ->where(['c.manufacturer_id' => $this->id]);

        if ($orderBy == 'name') {
            $query->join('INNER JOIN', Model::tableName() . ' as m', 'm.id = c.model_id')->orderBy(['m.name' => SORT_ASC]);
        }

        $cars = $query->all();
        $list = [];

        if ($cars) {
            foreach ($cars as $car)
            {
                if (!array_key_exists($car->model_id, $list)) {
                    $list[$car->model_id] = [
                        'model' => $car->model,
                        'compares_total' => 0,
                        'main_criteria' => 0,
                        'main_value' => 0,
                        'compare_criteria' => 0,
                        'compare_value' => 0,
                    ];

                    $comparesTotal[$car->model_id] = $comparesValue[$car->model_id] = 0;
                }

                $list[$car->model_id]['cars'][] = $car;

                $list[$car->model_id]['compares_total'] += $car->main_comparisons_count + $car->compare_comparisons_count;
                $list[$car->model_id]['main_criteria'] += $car->main_actual_criteria;
                $list[$car->model_id]['main_value'] += $car->main_comparisons_value;
                $list[$car->model_id]['compare_criteria'] += $car->compare_actual_criteria;
                $list[$car->model_id]['compare_value'] += $car->compare_comparisons_value;
            }

            foreach ($list as &$item)
            {
                $main = 0;
                if ($item['main_criteria']) {
                    $main = $item['main_criteria'];
                }
                $compare = 0;
                if ($item['compare_criteria']) {
                    $compare = $item['compare_criteria'];
                }

                if ($main + $compare) {
                    $item['grade'] = number_format(($item['main_value'] + $item['compare_value']) / ($main + $compare), 1);
                } else {
                    $item['grade'] = 0;
                }
            }

            if ($orderBy == 'rating') {
                ArrayHelper::multisort($list, ['grade'], [SORT_DESC]);
            }
        }

        return $list;
    }

    /**
     * @return int|string
     */
    public function getUrl()
    {
        return $this->url_title ? $this->url_title : $this->id;
    }

    /**
     * @param null $with
     * @return array|ActiveRecord[]
     */
    public static function filterData($with = null)
    {
        $query = self::find();

        if ($with == 'comparisons') {
            $query->joinWith('cars', true, 'INNER JOIN')
                ->join(
                    'INNER JOIN',
                    Comparison::tableName() . ' cc',
                    '(cc.car_main_id = ' . Car::tableName() . '.id OR cc.car_compare_id = ' . Car::tableName() . '.id)');
        }

        return $query->orderBy(['name' => SORT_ASC])->all();
    }
}
