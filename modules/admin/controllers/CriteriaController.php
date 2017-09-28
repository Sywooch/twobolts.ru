<?php
namespace app\modules\admin\controllers;


use app\components\ArrayHelper;
use app\models\CarComparisonCriteria;
use app\models\ComparisonCriteria;
use app\models\TechnicalCategory;
use app\models\TechnicalOption;
use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CriteriaController extends DefaultController
{
    /**
     * Сортировка пользовательских балов
     */
    public function actionSortItems()
    {
        $items = Yii::$app->request->post('items', []);

        foreach ($items as $key => $id)
        {
            Yii::$app->db->createCommand()
                ->update(ComparisonCriteria::tableName(), ['sort_order' => $key], 'id = :id', [':id' => $id])
                ->execute();
        }
    }

    /**
     * Форма нового пользовательского бала
     * @return string
     */
    public function actionGetForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->renderAjax('_criteria_form', [
            'model' => new ComparisonCriteria()
        ]);
    }

    /**
     * Форма существующего пользовательского бала
     * @param $id
     * @return string
     */
    public function actionGet($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = ComparisonCriteria::findOne(['id' => $id]);
        if ($model) {
            return $this->renderAjax('_criteria_form', [
                'model' => $model
            ]);
        }

        return Yii::t('app/error', 'Data not found');
    }

    /**
     * Создание пользовательского бала
     * @return array
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ComparisonCriteria();
        $model->setScenario('insert');

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
     * Изменения пользовательского бала
     * @param $id
     * @return array
     */
    public function actionUpdate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = ComparisonCriteria::findOne(['id' => $id]);
        $model->setScenario('update');

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
     * Удаление пользовательского бала
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = ComparisonCriteria::findOne(['id' => $id]);

        if ($model) {
            $model->setScenario('delete');
            $model->delete();

            return $this->redirect(Yii::$app->request->isPost ? Yii::$app->request->referrer : '/admin/settings');
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Model model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ComparisonCriteria the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ComparisonCriteria::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}