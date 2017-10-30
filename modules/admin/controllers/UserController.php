<?php
namespace app\modules\admin\controllers;

use app\models\User;
use app\models\UserProfile;
use app\models\UserSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UserController extends DefaultController {

	/**
	 * Lists all Car models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new UserSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Creates a new User model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new User();
		$model->activated = 1;
		$model->karma = 0;
		$model->banned = 0;
		$model->role = User::ROLE_USER;
		$model->timezone = 'Europe/Moscow';
		$model->last_login = date('Y-m-d H:i:s');
		$model->sendNewPassword = 0;
		$model->setScenario('create');

		$profile = new UserProfile();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$model->createProfile(Yii::$app->request->post('UserProfile', []));

			return $this->redirect(['/admin/user']);
		} else {
			return $this->render('create', [
				'model' => $model,
				'profile' => $profile
			]);
		}
	}

	/**
	 * Updates an existing User model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);
		$model->sendNewPassword = 0;
		$model->setScenario('update');

		$profile = $model->profile;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$profile->load(Yii::$app->request->post());
			$profile->save();

			return $this->redirect(['/admin/user']);
		} else {
			return $this->render('update', [
				'model' => $model,
				'profile' => $profile
			]);
		}
	}

	/**
	 * Deletes an existing User model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		$this->findModel($id)->delete();

		return $this->redirect(['/admin/user']);
	}

	/**
	 * @return array
	 */
	public function actionTestUniqueness()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$id = Yii::$app->request->post('id');
		$attr = Yii::$app->request->post('attr');
		$value = Yii::$app->request->post('value');

		$query = User::find();

		if ($id) {
			$query->andWhere(['!=', 'id', $id]);
		}

		$exists = $query->andWhere([$attr => $value])->count();

		return [
			'status' => $exists ? 'error' : 'success',
			'text' => $exists ? Yii::t('app', 'User with such ' . $attr . ' is exists.') : Yii::t('app', ucfirst($attr) . ' is available.')
		];
	}

	/**
	 * @param $id
	 *
	 * @return array
	 */
	public function actionDeleteAvatar($id)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$model = $this->findModel($id);

		$model->deleteAvatarFile();
		$model->updateAttributes([
			'avatar' => ''
		]);

		return [];
	}

	/**
	 * Finds the User model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return User the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = User::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}