<?php
namespace app\modules\admin\controllers;

use app\models\CarRequest;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CatalogController extends DefaultController
{
	/**
	 * @return string
	 */
	public function actionRequests()
	{
		$dataProvider = new ActiveDataProvider([
			'query' => CarRequest::find()->where(['status' => false]),
		]);

		return $this->render('index', [
			'dataProvider' => $dataProvider
		]);
	}

	/**
	 * @param $id
	 *
	 * @return array
	 */
	public function actionDeleteRequest($id)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$this->findRequestModel($id)->delete();

		return ['status' => 'ok'];
	}

	/**
	 * @param $id
	 *
	 * @return array
	 */
	public function actionApproveRequest($id)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$model = $this->findRequestModel($id);
		$model->updateAttributes([
			'status' => true
		]);
		$model->sendUserNotification();

		return ['status' => 'ok'];
	}

	/**
	 * @param $id
	 *
	 * @return CarRequest
	 * @throws NotFoundHttpException
	 */
	protected function findRequestModel($id) {
		if (($model = CarRequest::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Finds the model based on its primary key value.
	 * @param integer $id
	 * @return mixed the loaded model
	 */
	protected function findModel($id)
	{
		return null;
	}
}