<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CommentSearch represents the model behind the search form about `app\models\Comment`.
 */
class CommentSearch extends Comment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'object_id', 'user_id', 'reply_id'], 'integer'],
            [['text', 'status', 'user_ip', 'created', 'modified', 'object'], 'safe'],
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
        $query = Comment::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created' => SORT_DESC],
            ]
        ]);

        $this->load($params);

        if (isset($params['sort']) && strpos($params['sort'], 'user_id') !== false) {
            $query->joinWith('user');
            $dataProvider->setSort([
                'attributes' => [
                    'created',
                    'text',
                    'user_id' => [
                        'asc' => ['users.username' => SORT_ASC],
                        'desc' => ['users.username' => SORT_DESC],
                        'default' => SORT_ASC,
                    ]
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
            'object_id' => $this->object_id,
            'user_id' => $this->user_id,
            'reply_id' => $this->reply_id,
            'created' => $this->created,
            'modified' => $this->modified,
        ]);

        $query->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'user_ip', $this->user_ip])
            ->andFilterWhere(['like', 'object', $this->object]);

        return $dataProvider;
    }
}