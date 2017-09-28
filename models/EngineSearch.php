<?php

namespace app\models;

use app\components\ArrayHelper;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EngineSearch represents the model behind the search form about `app\models\Engine`.
 */
class EngineSearch extends Engine
{
    public $manufacturer_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'model_id', 'manufacturer_id'], 'integer'],
            [['engine_name', 'url_title'], 'safe'],
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
        $query = Engine::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['engine_name' => SORT_ASC],
                'attributes' => ['engine_name', 'manufacturer_id', 'model_id', 'url_title']
            ]
        ]);

        $this->load($params);

        if (isset($params['sort']) && strpos($params['sort'], 'model_id') !== false) {
            $query->joinWith('model');
            $dataProvider->setSort([
                'attributes' => [
                    'model_id' => [
                        'asc' => ['models.name' => SORT_ASC],
                        'desc' => ['models.name' => SORT_DESC],
                        'default' => SORT_ASC,
                    ],
                    'engine_name',
                    'manufacturer_id',
                    'url_title'
                ]
            ]);
        }

        if (isset($params['sort']) && strpos($params['sort'], 'manufacturer_id') !== false) {
            $query->joinWith('model.manufacturer');
            $dataProvider->setSort([
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
                    'engine_name',
                    'url_title'
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
            'model_id' => $this->model_id,
        ]);

        $query->andFilterWhere(['like', 'engine_name', $this->engine_name])
            ->andFilterWhere(['like', 'url_title', $this->url_title]);

        if ($this->manufacturer_id) {
            $query->joinWith('model.manufacturer');
            $query->andFilterWhere(['manufacturer_id' => $this->manufacturer_id]);
        }

        return $dataProvider;
    }
}