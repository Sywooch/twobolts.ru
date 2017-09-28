<?php
namespace app\modules\admin\controllers;

use app\models\ComparisonCriteria;
use app\models\TechnicalCategory;

class SettingsController extends DefaultController
{
    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $criteria = ComparisonCriteria::find()->orderBy(['sort_order' => SORT_ASC])->all();
        $categories = TechnicalCategory::find()->orderBy(['category_order' => SORT_ASC])->all();

        return $this->render('index', [
            'criteria' => $criteria,
            'categories' => $categories
        ]);
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