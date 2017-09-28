<?php

namespace app\modules\admin\controllers;

use app\models\Engine;
use app\models\EngineSearch;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * EngineController implements the CRUD actions for Manufacturer model.
 */
class EngineController extends DefaultController
{
    /**
     * Lists all Engine models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EngineSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Engine model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $existingEngines = Yii::$app->request->post('existing_engines');
        $model = new Engine();

        if ($existingEngines) {
            foreach ($existingEngines as $engineId)
            {
                $engine = $this->findModel($engineId);

                $model = new Engine();
                $model->load(Yii::$app->request->post());
                $model->engine_name = $engine->engine_name;

                $model->save();
            }

            return $this->redirect(['/admin/engine']);
        } elseif ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/admin/engine']);
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing Engine model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/admin/engine']);
        } else {
            return $this->render('update', [
                'model' => $model
            ]);
        }
    }

    /**
     * Deletes an existing Engine model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['/admin/engine']);
    }

    /**
     * Finds the Enfine model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Engine the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Engine::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}