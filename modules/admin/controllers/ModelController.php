<?php

namespace app\modules\admin\controllers;

use app\components\behaviors\Referrer;
use app\models\Car;
use app\models\Engine;
use app\models\Manufacturer;
use Yii;
use app\models\Model;
use app\models\ModelSearch;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ModelController implements the CRUD actions for Model model.
 */
class ModelController extends DefaultController
{
    /**
     * Lists all Model models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ModelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Model model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Model();
        $model->setScenario('insert');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/admin/model']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Model model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('update');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/admin/model']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Model model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['/admin/model']);
    }

    /**
     * AJAX-запрос на получение списка моделей
     * Необязательный параметр - идентификатор производителя
     *
     * @param null|int $id
     * @return array
     */
    public function actionGetModels($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $hasCar = Yii::$app->request->post('hasCar', false);

        if ($id) {
            $query = Manufacturer::findOne($id)->getModels($hasCar);
        } else {
            $query = Model::find();
            if ($hasCar) {
                $query->joinWith('cars', true, 'INNER JOIN');
            }
        }

        $models = $query->joinWith('body')->orderBy([Model::tableName() . '.name' => SORT_ASC])->asArray()->all();

        return [
            'status' => 'ok',
            'models' => $models ? $models : []
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function actionDeleteImage($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($id) {
            $this->findModel($id)->deleteImage()->save();
        } else {
            (new Model())->deleteImage();
        }

        return [];
    }

    /**
     * Find all engines for selected model
     * @param $id
     * @return array
     */
    public function actionGetEngines($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $engines = Engine::find()
            ->joinWith(['model'])
            ->where([Model::tableName() . '.id' => $id])
            ->orderBy(['engine_name' => SORT_ASC])
            ->asArray()
            ->all();

        return [
            'status' => 'ok',
            'engines' => $engines
        ];
    }

    public function actionGetCars($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $cars = Car::find()
            ->joinWith(['manufacturer', 'model', 'model.body', 'engine'])
            ->where([Model::tableName() . '.id' => $id])
            ->orderBy([Model::tableName() . '.name' => SORT_ASC, 'engine_name' => SORT_ASC])
            ->asArray()
            ->all();

        return [
            'status' => 'ok',
            'cars' => $cars
        ];
    }

    /**
     * Finds the Model model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Model the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Model::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}