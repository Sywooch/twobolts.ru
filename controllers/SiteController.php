<?php

namespace app\controllers;

use app\models\Comparison;
use app\models\News;
use app\models\User;
use yii;
use yii\web\Controller;

class SiteController extends BaseController
{
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        //echo '<pre>'.print_r($_SESSION, 2);
        //echo '<pre>'.print_r(Yii::$app->session, 2);exit;
        $news = News::find()->orderBy(['created' => SORT_DESC])->limit(3)->all();
        
        return $this->render('index', [
            'comparison' => Comparison::getHomeComparison(),
            'newComparisons' => Comparison::getLastComparisons(3),
            'topComparisons' => Comparison::getTopComparisons(),
            'activeUsers' => User::getActiveUsers(),
            'lastNews' => $news
        ]);
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;

        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }

        return null;
    }
}
