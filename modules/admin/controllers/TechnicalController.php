<?php
namespace app\modules\admin\controllers;


use app\components\ArrayHelper;
use app\models\ComparisonCriteria;
use app\models\TechnicalCategory;
use app\models\TechnicalOption;
use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TechnicalController extends DefaultController
{
    /**
     * Сортировка категорий тех. характеристик
     */
    public function actionSortCategoryItems()
    {
        $items = Yii::$app->request->post('items', []);

        foreach ($items as $key => $id)
        {
            Yii::$app->db->createCommand()
                ->update(TechnicalCategory::tableName(), ['category_order' => $key], 'id = :id', [':id' => $id])
                ->execute();
        }
    }

    /**
     * Форма новой категории тех. характеристик
     * @return string
     */
    public function actionGetCategoryForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->renderAjax('_technical_category_form', [
            'model' => new TechnicalCategory()
        ]);
    }

    /**
     * Форма существующей категории тех. характеристик
     * @param $id
     * @return string
     */
    public function actionGetCategory($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = TechnicalCategory::findOne(['id' => $id]);
        if ($model) {
            return $this->renderAjax('_technical_category_form', [
                'model' => $model
            ]);
        }

        return Yii::t('app/error', 'Data not found');
    }

    /**
     * Создание новой категории тех. характеристик
     * @return array
     */
    public function actionCreateCategory()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new TechnicalCategory();

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
     * Изменения категории тех. характеристик
     * @param $id
     * @return array
     */
    public function actionUpdateCategory($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = TechnicalCategory::findOne(['id' => $id]);

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
     * Удаление категории тех. характеристик
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDeleteCategory($id)
    {
        $model = TechnicalCategory::findOne(['id' => $id]);

        if ($model) {
            $model->delete();

            return $this->redirect(Yii::$app->request->isPost ? Yii::$app->request->referrer : '/admin/settings');
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Сортировка тех. характеристик
     */
    public function actionSortOptionItems()
    {
        $items = Yii::$app->request->post('items', []);

        foreach ($items as $key => $id)
        {
            Yii::$app->db->createCommand()
                ->update(TechnicalOption::tableName(), ['option_order' => $key], 'id = :id', [':id' => $id])
                ->execute();
        }
    }

    /**
     * Форма новой тех. характеристики
     * @return string
     */
    public function actionGetOptionForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->renderAjax('_technical_option_form', [
            'model' => new TechnicalOption(),
            'categories' => TechnicalCategory::find()->orderBy(['category_order' => SORT_ASC])->all()
        ]);
    }

    /**
     * Форма существующей тех. характеристики
     * @param $id
     * @return string
     */
    public function actionGetOption($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = TechnicalOption::findOne(['id' => $id]);
        if ($model) {
            return $this->renderAjax('_technical_option_form', [
                'model' => $model,
                'categories' => TechnicalCategory::find()->orderBy(['category_order' => SORT_ASC])->all()
            ]);
        }

        return Yii::t('app/error', 'Data not found');
    }

    /**
     * Создание новой тех. характеристики
     * @return array
     */
    public function actionCreateOption()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new TechnicalOption();

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
     * Изменения тех. характеристики
     * @param $id
     * @return array
     */
    public function actionUpdateOption($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = TechnicalOption::findOne(['id' => $id]);

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
     * Удаление тех. характеристики
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDeleteOption($id)
    {
        $model = TechnicalOption::findOne(['id' => $id]);

        if ($model) {
            $model->delete();

            return $this->redirect(Yii::$app->request->isPost ? Yii::$app->request->referrer : '/admin/settings');
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