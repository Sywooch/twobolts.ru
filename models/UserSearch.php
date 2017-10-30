<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form about `app\models\User`.
 */
class UserSearch extends User
{
	public $name;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'activated', 'banned', 'role', 'karma'], 'integer'],
			[['username', 'password', 'email', 'ban_reason', 'new_password_key', 'new_password_requested',
				'new_email', 'new_email_key', 'last_ip', 'last_login', 'created', 'modified', 'timezone', 'vkontakte_id', 'vkontakte_token',
				'odnoklassniki_id', 'odnoklassniki_token', 'facebook_id', 'facebook_token', 'twitter_id', 'twitter_token', 'google_id', 'google_token',
				'avatar', 'uploaded_avatar', 'hash', 'hash_created', 'name'], 'safe'],
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
		$query = User::find();

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => ['username' => SORT_ASC],
				'attributes' => [
					'username',
					'name' => [
						'asc' => ['user_profiles.last_name' => SORT_ASC, 'user_profiles.first_name' => SORT_ASC],
						'desc' => ['user_profiles.last_name' => SORT_DESC, 'user_profiles.first_name' => SORT_DESC],
						'default' => SORT_ASC
					],
					'email',
					'activated',
					'banned',
					'created'
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
			'id' => $this->id,
			'created' => $this->created,
			'role' => $this->role,
			'karma' => $this->karma,
		]);

		if ($this->activated == 1) {
			$query->andWhere(['activated' => 1]);
		} elseif ($this->activated == -1) {
			$query->andWhere(['activated' => 0]);
		}

		if ($this->banned == 1) {
			$query->andWhere(['banned' => 1]);
		} elseif ($this->banned == -1) {
			$query->andWhere(['banned' => 0]);
		}

		if ($this->avatar == 1) {
			$query->andWhere(['!=', 'avatar', '']);
		} elseif ($this->avatar == -1) {
			$query->andWhere(['avatar' => '']);
		}

		if ($this->created) {
			$createdArray = explode(' - ', $this->created);
			$start = $createdArray[0];
			$end = $createdArray[1];

			$query->andFilterCompare('created',  date('Y-m-d', strtotime($start)), '>=')
				->andFilterCompare('created', date('Y-m-d', strtotime($end)), '<=');
		}

		$query->andFilterWhere(['like', 'username', $this->username])
			->andFilterWhere(['like', 'email', $this->email]);

		return $dataProvider;
	}
}