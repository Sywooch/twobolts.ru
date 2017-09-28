<?php
namespace app\controllers;

use cics\widgets\VideoEmbed;
use Yii;
use yii\web\Response;

class VideoController extends BaseController
{
    public function actionShow()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return VideoEmbed::widget([
            'url' => Yii::$app->request->post('video')
        ]);
    }
}