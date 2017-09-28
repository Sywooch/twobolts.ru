<?php

namespace app\modules\admin;
use app\models\User;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * admin module definition class
 */
class AdminModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        /** @var User $user */
        $user = Yii::$app->user->identity;

        if (!$user || $user->role != User::ROLE_ADMIN) {
            throw new ForbiddenHttpException();
        }
    }
}
