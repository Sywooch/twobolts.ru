<?php
namespace app\modules\admin\controllers;

use app\components\StringHelper;
use Yii;
use yii\helpers\Inflector;
use yii\web\Response;

class UrlTitleController extends DefaultController
{
    /**
     * @return array
     */
    public function actionGet()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $source = Yii::$app->request->post('source');
        $urlTitle = Inflector::slug(Inflector::transliterate($source, 'Russian-Latin/BGN; NFKD'));

        return [
            'full' => $urlTitle,
            'ellipsized' => StringHelper::ellipsize($urlTitle, 30, .5)
        ];
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