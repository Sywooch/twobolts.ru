<?php

namespace app\controllers;

use app\components\ArrayHelper;
use app\models\User;
use yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;

class SystemController extends BaseController
{
	/**
	 * @return array
	 */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['sign-out', 'recover-password', 'register'],
                'rules' => [
                    [
                        'actions' => ['sign-out'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['recover-password', 'register'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
                'denyCallback' => function () {
                    Yii::$app->session->setFlash('denied', Yii::t('app/error', 'Access denied'));
                    return Yii::$app->response->redirect(['/']);
                },
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'sign-in' => ['post'],
                    'sign-out' => ['post'],
                    'reset-password' => ['post'],
                    'register' => ['post'],
                ],
            ],
        ];
    }

	/**
	 * Signing in
	 *
	 * @return array
	 */
    public function actionSignIn()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->user->isGuest) {
            return [
                'error' => Yii::t('app', 'Already Signed In')
            ];
        }

        $model = new User();
        $model->scenario = 'login';

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->getUser()->setReturnUrl('');

            return [
                'status' => 'Ok'
            ];
        }
        
        $errors = '';
        if ($model->errors) {
            foreach ($model->errors as $error)
            {
                $errors .= implode('<br>', $error);
            }
        } else {
            $errors = Yii::t('app', 'General error');
        }
        
        return [
            'error' => $errors
        ];
    }

	/**
	 * Signing out
	 */
    public function actionSignOut()
    {
        Yii::$app->user->logout();

        //return $this->goHome();
    }

	/**
	 * Resets password
	 */
    public function actionResetPassword()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (User::resetPassword(Yii::$app->request->post('email'))) {
            echo json_encode([
                'message' => Yii::t('app', 'Password reset successfully')
            ]);
            exit;
        }

        echo json_encode([
            'error' => Yii::t('app', 'Password reset error')
        ]);
    }

	/**
	 * @return string|Response
	 */
    public function actionRecoverPassword()
    {
        $hash = Yii::$app->request->get('hash');

        /** @var User $user */
        $user = User::find()->where(['new_password_key' => $hash])->one();

        if ($user && strtotime($user->new_password_requested) < strtotime('-3 days')) {
            $user = null;
        }

        if ($user) {
            $user->scenario = 'recover';
            $user->password = '';

            if ($user->load(Yii::$app->request->post()) && $user->validate()) {
                $password = $user->password;

                $user->hashPassword();
                $user->scenario = 'login';
                $user->new_password_key = null;
                $user->new_password_requested = null;
                if ($user->save()) {
                    Yii::$app->mailer->compose('recover_complete', ['user' => $user, 'password' => $password])
                        ->setFrom(Yii::$app->params['adminEmail'])
                        ->setTo($user->email)
                        ->setSubject(Yii::$app->name . ' - ' . Yii::t('app', 'Recover complete'))
                        ->send();

                    Yii::$app->user->login($user, 3600 * 24 * 30);

                    return $this->goHome();
                }
            }
        }

        return $this->render('recover_password', [
            'user' => $user
        ]);
    }

	/**
	 * Registers user
	 */
    public function actionRegister()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->user->isGuest) {
            echo json_encode([
                'error' => Yii::t('app', 'Already Signed In')
            ]);
        }

        $model = new User();
        $model->scenario = 'register';
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            echo json_encode([
                'status' => 'Ok'
            ]);
            exit;
        }

        echo json_encode([
            'error' => $model->stringifyErrors()
        ]);
    }
}
