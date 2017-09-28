<?php

namespace app\controllers;

use app\components\SocialAuthHandler;
use yii;
use yii\authclient\clients\Facebook;
use yii\authclient\clients\Google;
use yii\authclient\clients\Twitter;
use yii\authclient\clients\VKontakte;
use yii\web\Controller;

class BaseController extends Controller
{
    /**
     * @param yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        if (Yii::$app->getUser()->isGuest) {
            $request = Yii::$app->getRequest();
            // исключаем страницу авторизации или ajax-запросы
            if (!($request->getIsAjax() || strpos($request->getUrl(), 'sign-in') !== false)) {
                Yii::$app->getUser()->setReturnUrl($request->getUrl());
            }
        }

        if ($action->controller->getUniqueId() != 'social' && !Yii::$app->request->isAjax) {
	        Yii::$app->session->set('referenceUrl', Yii::$app->request->url);
        }

        return parent::beforeAction($action);
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * @param $name
     * @return string
     */
    public function getSocialAuthUrl($name)
    {
        if (Yii::$app->getUser()->isGuest) {
            /** @var VKontakte|Facebook|Twitter|Google $client */
            $client = Yii::$app->authClientCollection->getClient($name);
            $client->setReturnUrl(SocialAuthHandler::getReturnUrl($name));

            return $name == 'twitter' ? $client->buildAuthUrl($client->fetchRequestToken()) : $client->buildAuthUrl();
        }

        return '';
    }
}
