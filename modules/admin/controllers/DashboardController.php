<?php
namespace app\modules\admin\controllers;

class DashboardController extends DefaultController
{
    public function actionIndex()
    {
        return $this->render('index');
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