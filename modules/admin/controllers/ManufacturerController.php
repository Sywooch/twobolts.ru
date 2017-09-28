<?php

namespace app\modules\admin\controllers;

use app\components\ArrayHelper;
use app\components\behaviors\Referrer;
use app\models\Engine;
use app\models\Model;
use Yii;
use app\models\Manufacturer;
use app\models\ManufacturerSearch;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ManufacturerController implements the CRUD actions for Manufacturer model.
 */
class ManufacturerController extends DefaultController
{
    /**
     * Lists all Manufacturer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ManufacturerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Manufacturer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Manufacturer();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/admin/manufacturer']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Manufacturer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/admin/manufacturer']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Manufacturer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['/admin/manufacturer']);
    }

    public function actionGetModels($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($id) {
            $models = Model::filterData(null, $id);
        } else {
            $models = Model::filterData();
        }

        return [
            'status' => 'ok',
            'data' => ArrayHelper::map($models, 'id', 'name')
        ];
    }

    /**
     * Find all engines for selected manufacturer
     * @param $id
     * @return array
     */
    public function actionGetEngines($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $engines = Engine::find()
            ->joinWith('model.manufacturer')
            ->where([Manufacturer::tableName() . '.id' => $id])
            ->orderBy(['engine_name' => SORT_ASC])
            ->groupBy(['engine_name'])
            ->asArray()
            ->all();

        return [
            'status' => 'ok',
            'engines' => $engines
        ];
    }

    /**
     * Finds the Manufacturer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Manufacturer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Manufacturer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}