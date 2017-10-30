<?php

namespace app\controllers;

use app\models\Model;
use app\models\User;
use app\models\UserCar;
use yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ProfileController extends BaseController
{
	/**
	 * @return array
	 */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'save', 'recover-email', 'edit-password', 'delete-car'],
                'rules' => [
                    [
                        'actions' => ['index', 'save', 'recover-email', 'edit-password', 'delete-car'],
                        'allow' => true,
                        'roles' => ['@'],
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
                    'save' => ['post'],
                    'edit-password' => ['post'],
                ],
            ],
        ];
    }

	/**
	 * @return string
	 */
    public function actionIndex()
    {
        return $this->renderProfile('index', User::identity());
    }

	/**
	 * @param $username
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
    public function actionView($username)
    {
        if (filter_var($username, FILTER_VALIDATE_INT)) {
            $user = User::getUser($username);
        } else {
            $user = User::findByUsername($username);
        }

	    if (!$user) {
		    throw new NotFoundHttpException(Yii::t('app/error', 'User not found'));
	    }

        return $this->renderProfile('view', $user);
    }

	/**
	 * @param $view
	 * @param $user
	 *
	 * @return string
	 */
    private function renderProfile($view, $user)
    {
        $before = $garage = [];
        foreach ($user->cars as $car)
        {
            if ($car->type == 'garage') {
                $garage[] = $car;
            } else {
                $before[] = $car;
            }
        }

        $comparisons = $user->comparisons;
        if (count($comparisons) > User::PROFILE_COMPARISONS_PER_PAGE) {
            $comparisons = array_slice($comparisons, 0, User::PROFILE_COMPARISONS_PER_PAGE);
        }

        $favorites = $user->favorites;
        if (count($favorites) > User::PROFILE_COMPARISONS_PER_PAGE) {
            $favorites = array_slice($favorites, 0, User::PROFILE_COMPARISONS_PER_PAGE);
        }

        return $this->render($view, [
            'user' => $user,
            'comparisons' => $comparisons,
            'comparisonsCount' => count($user->comparisons),
            'favorites' => $favorites,
            'favoritesCount' => count($user->favorites),
            'before' => $before,
            'garage' => $garage
        ]);
    }

	/**
	 * @return array
	 */
    public function actionSave()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = User::identity();
        $user->scenario = Yii::$app->request->post('scenario');
        $postData = Yii::$app->request->post('User');
        $email = ArrayHelper::getValue($postData, 'email');
        
        if ($user->scenario == 'edit-email') {
            if (!$user->email && $user->load(Yii::$app->request->post()) && $user->validate() && $user->resetEmail($email)) {
                return [
                    'message' => Yii::t('app', 'Email reset successfully')
                ];
            } else if ($user->email && $user->resetEmail()) {
                return [
                    'message' => Yii::t('app', 'Email reset successfully')
                ];
            } else {
                return [
                    'error' => $user->stringifyErrors()
                ];
            }
        } elseif ($user->scenario == 'edit-profile') {
            if ($user->profile->load(Yii::$app->request->post()) && $user->profile->save()) {
            	$user->updateAttributes([
            		'timezone' => Yii::$app->request->post('timezone', 'Europe/Moscow')
	            ]);

                return [
                    'message' => Yii::t('app', 'Profile saved successfully')
                ];
            } else {
                return [
                    'error' => $user->profile->errors
                ];
            }
        } else {
            $user->scenario = Model::SCENARIO_DEFAULT;
            if ($user->load(Yii::$app->request->post()) && $user->save()) {
                return [
                    'status' => 'ok'
                ];
            } else {
                return [
                    'error' => $user->stringifyErrors()
                ];
            }
        }
    }

	/**
	 * @return string|Response
	 */
    public function actionRecoverEmail()
    {
        $user = User::identity();
        $user->scenario = 'recover-email';

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post('User');
            $loadData = [
                'User' => [
                    'email' => $postData['email']
                ]
            ];
            if ($user->load($loadData) && $user->validate() && $user->validatePassword($postData['password']) && $user->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Email recover successfully'));
                return $this->redirect('/profile');
            }
        } else {
            $hash = Yii::$app->request->get('hash');

            if ($hash != $user->new_email_key) {
                Yii::$app->session->setFlash('error', Yii::t('app/error', 'Wrong email hash'));
                return $this->redirect('/profile');
            }
        }

        return $this->render('recover_email', [
            'user' => $user
        ]);
    }

	/**
	 * Edit password
	 */
    public function actionEditPassword()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = User::identity();
        $passwordUser = new User();
        $passwordUser->scenario = 'edit-password';

        if ($passwordUser->load(Yii::$app->request->post()) && $passwordUser->validate() && $user->validatePassword($passwordUser->password)) {
            $passwordData = [
                'User' => [
                    'password' => $passwordUser->newPassword
                ]
            ];
            $user->load($passwordData);
            $user->hashPassword();
            if ($user->save()) {
                if ($user->email) {
                    Yii::$app->mailer->compose('recover_complete', ['user' => $user, 'password' => $passwordUser->newPassword])
                        ->setFrom(Yii::$app->params['adminEmail'])
                        ->setTo($user->email)
                        ->setSubject(Yii::$app->name . ' - ' . Yii::t('app', 'Recover complete'))
                        ->send();
                }

                Yii::$app->user->logout();

                echo json_encode([
                    'status' => 'ok'
                ]);
                exit;
            }
        }

        echo json_encode([
            'error' => ($passwordUser->stringifyErrors() ? $passwordUser->stringifyErrors() . '<br>' : '') . $user->stringifyErrors()
        ]);
    }

	/**
	 * @return Response
	 */
    public function actionDeleteCar()
    {
        $carId = Yii::$app->request->get('id');

        if (!filter_var($carId, FILTER_VALIDATE_INT)) {
            Yii::$app->session->setFlash('error', Yii::t('app/error', 'Wrong access'));
            return $this->redirect('/profile');
        }

        $model = UserCar::find()
            ->where(
                'id = :carId and user_id = :userId',
                [':carId' => $carId, ':userId' => Yii::$app->user->identity->getId()]
            )
            ->one();
        
        if ($model) {
            $model->delete();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Car removed from garage successfully'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Car removing from garage failed'));
        }
        
        return $this->redirect('/profile');
    }

	/**
	 * @param $username
	 *
	 * @return string
	 */
    public function actionFavorites($username)
    {
        if (filter_var($username, FILTER_VALIDATE_INT)) {
            $user = User::getUser($username);
        } else {
            $user = User::findByUsername($username);
        }

        return $this->render('favorites', ['user' => $user]);
    }

	/**
	 * @param $clientName
	 *
	 * @return Response
	 */
    public function actionDisconnect($clientName)
    {
    	User::identity()->updateAttributes([
		    $clientName . '_id' => '',
		    $clientName . '_token' => ''
	    ]);

    	return $this->redirect('/profile');
    }

	/**
	 * Update profile notification
	 */
    public function actionNotification()
    {
    	$status = Yii::$app->request->post('status', 0);

    	User::identity()->profile->updateAttributes([
    		'notification' => $status
	    ]);
    }
}
