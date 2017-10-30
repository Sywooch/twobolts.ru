<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\db\ActiveRecord;
use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
{
    public $layout = 'admin';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect('admin/settings');
    }

    /**
     * @return \yii\web\Response
     */
    public function actionSignOut()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Удаляет выбранные записи
     * @return mixed
     */
    public function actionDeleteSelected()
    {
        $selected = Yii::$app->request->post('selected');

        if (is_array($selected) && $selected) {
            foreach ($selected as $id)
            {
                $this->findModel($id)->delete();
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param $id
     * @return mixed|ActiveRecord
     */
    protected function findModel($id)
    {
    	return null;
    }
}
