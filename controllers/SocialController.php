<?php
namespace app\controllers;

use app\components\SocialAuthHandler;
use yii\authclient\clients\Facebook;
use yii\authclient\clients\Google;
use yii\authclient\clients\Twitter;
use yii\authclient\clients\VKontakte;

class SocialController extends BaseController
{
    /**
     * @return array
     */
    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    /**
     * @param VKontakte|Facebook|Twitter|Google $client
     */
    public function onAuthSuccess($client)
    {
	    //echo '<pre>'.print_r($client, 2);exit;
        //$attributes = $client->getUserAttributes();
        (new SocialAuthHandler($client))->handle();
    }
}