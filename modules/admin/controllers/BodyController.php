<?php

namespace app\modules\admin\controllers;

use app\components\ArrayHelper;
use app\models\Body;
use app\models\BodySearch;
use Yii;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * BodyController implements the CRUD actions for Manufacturer model.
 */
class BodyController extends DefaultController
{
    /**
     * Lists all Manufacturer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BodySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Body model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new Body();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'status' => 'ok'
            ];
        }

        return [
            'status' => 'error',
            'message' => ArrayHelper::toString($model->errors, '<br>')
        ];
    }

    /**
     * Updates an existing Body model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'status' => 'ok'
            ];
        }

        return [
            'status' => 'error',
            'message' => ArrayHelper::toString($model->errors, '<br>')
        ];
    }

    /**
     * Deletes an existing Body model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['/admin/body']);
    }

    public function actionLoadForm($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($id) {
            $model = $this->findModel($id);
        } else {
            $model = new Body();
        }

        return $this->renderAjax('_form', [
            'model' => $model
        ]);
    }

    /**
     * Finds the Manufacturer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Body|ActiveRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Body::find()->where(['body_id' => $id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}