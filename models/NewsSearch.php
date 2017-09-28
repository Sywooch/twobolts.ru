<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * NewsSearch represents the model behind the search form about `app\models\News`.
 */
class NewsSearch extends News
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'include_excerpt', 'user_id', 'num_views'], 'integer'],
            [['created', 'modified', 'title', 'content', 'excerpt', 'featured_image', 'video', 'gallery', 'source', 'featured_image_caption', 'url_title'], 'safe'],
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
        $query = News::find();

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
                    'title',
                    'content',
                    'num_views',
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
            'created' => $this->created,
            'modified' => $this->modified,
            'include_excerpt' => $this->include_excerpt,
            'user_id' => $this->user_id,
            'num_views' => $this->num_views,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'excerpt', $this->excerpt])
            ->andFilterWhere(['like', 'featured_image', $this->featured_image])
            ->andFilterWhere(['like', 'video', $this->video])
            ->andFilterWhere(['like', 'gallery', $this->gallery])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'featured_image_caption', $this->featured_image_caption])
            ->andFilterWhere(['like', 'url_title', $this->url_title]);

        return $dataProvider;
    }
}