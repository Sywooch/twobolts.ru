<?php

namespace app\models;

use yii\data\ActiveDataProvider;

/**
 * ComparisonSearch represents the model behind the search form about `app\models\Comparison`.
 */
class ComparisonSearch extends Comparison
{
    public $manufacturer;
    public $model;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'show_on_home', 'active', 'views', 'manufacturer', 'model'], 'integer'],
            [['user_id', 'date', 'calculatedRating'], 'safe'],
            [['rating'], 'number'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Comparison::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['date' => SORT_DESC],
            ]
        ]);

        $this->load($params);

        if (isset($params['sort']) && strpos($params['sort'], 'user_id') !== false) {
            $query->joinWith('user');
            $dataProvider->setSort([
                'attributes' => [
                    'user_id' => [
                        'asc' => ['users.username' => SORT_ASC],
                        'desc' => ['users.username' => SORT_DESC],
                        'default' => SORT_ASC,
                    ],
                    'date',
                    'active',
                    'rating'
                ]
            ]);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'car_main_id' => $this->car_main_id,
            'car_compare_id' => $this->car_compare_id,
            'show_on_home' => $this->show_on_home,
            'active' => $this->active,
            'rating' => $this->rating,
            'views' => $this->views,
        ]);

        if ($this->user_id) {
            $query->joinWith('user');
            $query->andFilterWhere(['like', 'users.username', $this->user_id]);
        }

        if ($this->manufacturer) {
            $query->join('INNER JOIN', Car::tableName() . ' cm', 'cm.id = car_main_id or cm.id = car_compare_id');

            if ($this->manufacturer) {
                $query->join('INNER JOIN', Manufacturer::tableName() . ' ma', 'ma.id = cm.manufacturer_id AND ma.id = :manufacturerId', [':manufacturerId' => $this->manufacturer]);
            }

            if ($this->model) {
                $query->join('INNER JOIN', Model::tableName() . ' mo', 'mo.id = cm.model_id AND mo.id = :modelId', [':modelId' => $this->model]);
            }
        }

        if ($this->date) {
            $range = explode(' - ', $this->date);
            $timestampStart = strtotime($range[0]);
            $timestampEnd = strtotime($range[1]);
            $query->andFilterWhere(['>=', 'date', date('Y-m-d H:i:s', mktime(0,0,0,date('m', $timestampStart), date('d', $timestampStart), date('Y', $timestampStart)))]);
            $query->andFilterWhere(['<=', 'date', date('Y-m-d H:i:s', mktime(23,59,59,date('m', $timestampEnd), date('d', $timestampEnd), date('Y', $timestampEnd)))]);
        }

        $query->groupBy('id');

        return $dataProvider;
    }
}