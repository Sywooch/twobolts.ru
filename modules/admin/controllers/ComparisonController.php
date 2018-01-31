<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Comparison;
use app\models\ComparisonSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ComparisonController implements the CRUD actions for Comparison model.
 */
class ComparisonController extends DefaultController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'activate' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all Comparison models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ComparisonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

	/**
	 * Разрешает для показа существующее сравнение.
	 *
	 * @param $id
	 *
	 * @return Response
	 * @throws NotFoundHttpException
	 */
    public function actionActivate($id)
    {
        $model = $this->findModel($id);

        $model->active = 1;
        $model->save();

        return $this->redirect(Yii::$app->request->referrer);
    }

	/**
	 * Запрещает для показа существующее сравнение.
	 *
	 * @param $id
	 *
	 * @return Response
	 * @throws NotFoundHttpException
	 */
    public function actionBan($id)
    {
        $model = $this->findModel($id);

        $model->active = 0;
        $model->save();

        return $this->redirect(Yii::$app->request->referrer);
    }

	/**
	 * Запрещает или разрешает для показа существующее сравнение на главной странице.
	 *
	 * @param $id
	 *
	 * @return Response
	 * @throws NotFoundHttpException
	 */
    public function actionHome($id)
    {
        $model = $this->findModel($id);

        $model->show_on_home = $model->show_on_home ? 0 : 1;
        $model->save();

        return $this->redirect(Yii::$app->request->referrer);
    }

	/**
	 * Deletes an existing Comparison model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param $id
	 *
	 * @return Response
	 * @throws NotFoundHttpException
	 * @throws \Exception
	 * @throws \yii\db\StaleObjectException
	 */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Пересчитывает рейтинг для выбранных сравнений
     */
    public function actionRecalculateRating()
    {
        $selected = Yii::$app->request->post('selected');

        if (is_array($selected) && $selected) {
            /** @var Comparison[] $comparisons */
            $comparisons = Comparison::find()->where(['id' => [4, 8, 15]])->all();

            foreach ($comparisons as $comparison)
            {
                $comparison->recalculateRating();
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Comparison model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Comparison the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Comparison::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}