<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Model as CarModel;

/**
 * ModelSearch represents the model behind the search form about `app\models\Model`.
 */
class ModelSearch extends CarModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'manufacturer_id', 'body_id', 'is_popular'], 'integer'],
            [['name', 'image', 'url_title'], 'safe'],
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
        $query = CarModel::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC],
                'attributes' => ['name', 'manufacturer_id', 'body_id', 'is_popular', 'url_title']
            ]
        ]);

        $this->load($params);

        if (isset($params['sort']) && strpos($params['sort'], 'manufacturer_id') !== false) {
            $query->joinWith('manufacturer');
            $dataProvider->setSort([
                'attributes' => [
                    'manufacturer_id' => [
                        'asc' => ['manufacturers.name' => SORT_ASC],
                        'desc' => ['manufacturers.name' => SORT_DESC],
                        'default' => SORT_ASC,
                    ],
                    'body_id' => [
                        'asc' => ['bodies.name' => SORT_ASC],
                        'desc' => ['bodies.name' => SORT_DESC],
                        'default' => SORT_ASC,
                    ],
                    'name',
                    'url_title',
                    'is_popular'
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
            'body_id' => $this->body_id,
            'manufacturer_id' => $this->manufacturer_id,
            'is_popular' => $this->is_popular,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'url_title', $this->url_title]);

        if ($this->image == 1) {
            $query->andWhere(['!=', 'image', '']);
        } elseif ($this->image == -1) {
            $query->andWhere(['=', 'image', '']);
        }

        return $dataProvider;
    }
}
