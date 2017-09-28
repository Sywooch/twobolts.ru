<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CarSearch represents the model behind the search form about `app\models\Car`.
 */
class CarSearch extends Car
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'manufacturer_id', 'model_id'], 'integer'],
            [['image', 'engine_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = Car::find()->joinWith(['manufacturer', 'model', 'engine']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'manufacturer_id' => [
                        'asc' => ['manufacturers.name' => SORT_ASC],
                        'desc' => ['manufacturers.name' => SORT_DESC],
                        'default' => SORT_ASC,
                    ],
                    'model_id' => [
                        'asc' => ['models.name' => SORT_ASC],
                        'desc' => ['models.name' => SORT_DESC],
                        'default' => SORT_ASC,
                    ],
                    'engine_id' => [
                        'asc' => ['engines.engine_name' => SORT_ASC],
                        'desc' => ['engines.engine_name' => SORT_DESC],
                        'default' => SORT_ASC,
                    ],
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cars.manufacturer_id' => $this->manufacturer_id,
            'cars.model_id' => $this->model_id
        ]);

        $query->andFilterWhere(['like', 'engines.engine_name', $this->engine_id]);

        return $dataProvider;
    }
}