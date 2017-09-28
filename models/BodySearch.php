<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BodySearch represents the model behind the search form about `app\models\Body`.
 */
class BodySearch extends Body
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['body_id'], 'integer'],
            [['body_name', 'body_url_title'], 'safe'],
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
        $query = Body::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['body_name' => SORT_ASC],
                'attributes' => ['body_name', 'body_url_title']
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
            'body_id' => $this->body_id,
        ]);

        $query->andFilterWhere(['like', 'body_name', $this->body_name])
            ->andFilterWhere(['like', 'body_url_title', $this->body_url_title]);

        return $dataProvider;
    }
}