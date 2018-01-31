<?php
namespace app\controllers;

use app\components\ArrayHelper;
use app\components\widgets\ComparisonList;
use app\models\Car;
use app\models\CarRequest;
use app\models\Comparison;
use app\models\ComparisonCriteria;
use app\models\ComparisonThanks;
use app\models\Engine;
use app\models\Manufacturer;
use app\models\Model;
use app\models\Notification;
use app\models\User;
use app\models\UserCar;
use app\models\UserFavorite;
use yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ComparisonController extends BaseController
{
	/**
	 * @return array
	 */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['add-model'],
                'rules' => [
                    [
                        'actions' => ['add-model'],
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
                    'find-items' => ['post'],
                    'find-manufacturer-items' => ['post'],
                    'find-model-items' => ['post'],
                    'find-user-items' => ['post'],
                    'add-opinion' => ['post'],
                    'add-favorite' => ['post'],
                    'add-model' => ['post']
                ],
            ],
        ];
    }

	/**
	 * @return string
	 */
    public function actionIndex()
    {
        $itemsCount = Comparison::getItemsCount();

        $sorting = isset($_COOKIE['_comparison__0_sorting_']) ? $_COOKIE['_comparison__0_sorting_'] : 'date';
        $pageNum = isset($_COOKIE['_comparison__0_page_num_']) ? $_COOKIE['_comparison__0_page_num_'] : 1;

        return $this->render('index', [
            'items' => Comparison::findItems(0, $pageNum * ComparisonList::ITEMS_PER_PAGE, $sorting),
            'itemsCount' => $itemsCount,
            'findAction' => 'find-items',
            'pageNum' => $pageNum,
            'sorting' => $sorting,
            'params' => 0,
            'title' => Yii::t('app', 'Car compares'),
            'metaDescription' => Yii::t('app/meta', 'Comparison Index Page Description')
        ]);
    }

	/**
	 * @param null $carId
	 *
	 * @return string
	 */
    public function actionAdd($carId = null)
    {
        $car = null;
        if ($carId) {
            $car = filter_var($carId, FILTER_VALIDATE_INT);
            if ($car == false) {
                $car = Car::findByUrl($carId);
            } else {
                $car = Car::findOne($carId);
            }
        }

        $carRequest = new CarRequest();
        $comparisonData = Yii::$app->session->get('comparisonData');
	    $carData = Yii::$app->session->get('carRequest');

        if ($carData) {
	        $carRequest->load(['CarRequest' => $carData]);
        }

        return $this->render('add', [
            'manufacturers' => Manufacturer::find()
                ->joinWith('cars', true, 'INNER JOIN')
                ->groupBy(['id'])
                ->orderBy(['name' => SORT_ASC])
                ->asArray()
                ->all(),
            'car' => $car,
            'carRequest' => $carRequest,
            'criteria' => ComparisonCriteria::find()->orderBy(['sort_order' => SORT_ASC])->all(),
	        'comparisonData' => $comparisonData
        ]);
    }

	/**
	 * @return array
	 */
    public function actionExistComparison()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $params = [
            'mainCar' => [
                'manufacturerId' => Yii::$app->request->post('mainManufacturerId'),
                'modelId' => Yii::$app->request->post('mainModelId'),
                'engineId' => Yii::$app->request->post('mainEngineId')
            ],
            'compareCar' => [
                'manufacturerId' => Yii::$app->request->post('compareManufacturerId'),
                'modelId' => Yii::$app->request->post('compareModelId'),
                'engineId' => Yii::$app->request->post('compareEngineId')
            ]
        ];

        $model = Comparison::findByCarParts($params, $byUser = true);

        if (is_null($model)) {
            return [
                'status' => 'ok'
            ];
        }

        return [
            'error' => Yii::t('app', 'Already compare these cars')
        ];
    }

	/**
	 * @return array
	 * @throws \Exception
	 */
    public function actionFindItems()
    {
        return $this->renderItems();
    }

	/**
	 * @param $manufacturerId
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
    public function actionManufacturer($manufacturerId)
    {
        /** @var $model Manufacturer */
        if (filter_var($manufacturerId, FILTER_VALIDATE_INT)) {
            $model = Manufacturer::find()->where('id = :manufacturer', [':manufacturer' => $manufacturerId])->one();
        } else {
            $model = Manufacturer::find()->where('url_title = :manufacturer', [':manufacturer' => $manufacturerId])->one();
        }

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $itemsCount = Comparison::getManufacturerItemsCount($model->id);

        $sorting = isset($_COOKIE['_comparison_manufacturer_' . $model->id . '_sorting_']) ? $_COOKIE['_comparison_manufacturer_' . $model->id . '_sorting_'] : 'date';
        $pageNum = isset($_COOKIE['_comparison_manufacturer_' . $model->id . '_page_num_']) ? $_COOKIE['_comparison_manufacturer_' . $model->id . '_page_num_'] : 1;

        return $this->render('index', [
            'items' => Comparison::findManufacturerItems($model->id, 0, $pageNum * ComparisonList::ITEMS_PER_PAGE, $sorting),
            'itemsCount' => $itemsCount,
            'findAction' => 'find-manufacturer-items',
            'pageNum' => $pageNum,
            'sorting' => $sorting,
            'params' => $model->id,
            'title' => Yii::t('app', 'Compares') . ' ' . $model->name,
            'metaDescription' => Yii::t('app', 'Compares') . ' ' . $model->name
        ]);
    }

	/**
	 * @return array
	 * @throws \Exception
	 */
    public function actionFindManufacturerItems()
    {
        return $this->renderItems('manufacturer');
    }

	/**
	 * @param null $modelId
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
    public function actionModel($modelId = null)
    {
        /** @var $model Model */
        if (filter_var($modelId, FILTER_VALIDATE_INT)) {
            $model = Model::find()->where('id = :model', [':model' => $modelId])->one();
        } else {
            $model = Model::find()->where('url_title = :model', [':model' => $modelId])->one();
        }

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $itemsCount = Comparison::getModelItemsCount($model->id);

        $sorting = isset($_COOKIE['_comparison_model_' . $model->id . '_sorting_']) ? $_COOKIE['_comparison_model_' . $model->id . '_sorting_'] : 'date';
        $pageNum = isset($_COOKIE['_comparison_model_' . $model->id . '_page_num_']) ? $_COOKIE['_comparison_model_' . $model->id . '_page_num_'] : 1;

        return $this->render('index', [
            'items' => Comparison::findModelItems($model->id, 0, $pageNum * ComparisonList::ITEMS_PER_PAGE, $sorting),
            'itemsCount' => $itemsCount,
            'findAction' => 'find-model-items',
            'pageNum' => $pageNum,
            'sorting' => $sorting,
            'params' => $model->id,
            'title' => Yii::t('app', 'Compares') . ' ' . $model->getFullName(),
            'metaDescription' => Yii::t('app', 'Compares') . ' ' . $model->getFullName()
        ]);
    }

	/**
	 * @return array
	 * @throws \Exception
	 */
    public function actionFindModelItems()
    {
        return $this->renderItems('model');
    }

	/**
	 * @param null $username
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
    public function actionUser($username = null)
    {
        /** @var $model User */
        if (filter_var($username, FILTER_VALIDATE_INT)) {
            $model = User::find()->where('id = :user', [':user' => $username])->one();
        } else {
            $model = User::find()->where('username = :user', [':user' => $username])->one();
        }

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $itemsCount = Comparison::getUserItemsCount($model->id);

        $sorting = isset($_COOKIE['_comparison_user_' . $model->id . '_sorting_']) ? $_COOKIE['_comparison_user_' . $model->id . '_sorting_'] : 'date';
        $pageNum = isset($_COOKIE['_comparison_user_' . $model->id . '_page_num_']) ? $_COOKIE['_comparison_user_' . $model->id . '_page_num_'] : 1;

        return $this->render('index', [
            'items' => Comparison::findUserItems($model->id, 0, $pageNum * ComparisonList::ITEMS_PER_PAGE, $sorting),
            'itemsCount' => $itemsCount,
            'findAction' => 'find-user-items',
            'pageNum' => $pageNum,
            'sorting' => $sorting,
            'params' => $model->id,
            'title' => Yii::t('app', 'User Comparisons') . ' ' . $model->username,
            'metaDescription' => Yii::t('app', 'User Comparisons') . ' ' . $model->username
        ]);
    }

	/**
	 * @return array
	 * @throws \Exception
	 */
    public function actionFindUserItems()
    {
        return $this->renderItems('user');
    }

	/**
	 * @param string $pattern
	 *
	 * @return array
	 * @throws \Exception
	 */
    private function renderItems($pattern = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $options = Yii::$app->request->post('options');
        $sorting = Yii::$app->request->post('sorting');
        $pageNum = Yii::$app->request->post('pageNum');

        setcookie('_comparison_' . $pattern . '_' . $options . '_sorting_', $sorting, time() + 86400, Yii::$app->params['cookieDomain']);
        setcookie('_comparison_' . $pattern . '_' . $options . '_page_num_', $pageNum, time() + 86400, Yii::$app->params['cookieDomain']);

        $counter = 'get' . ucfirst($pattern) . 'ItemsCount';
        $getter = 'find' . ucfirst($pattern) . 'Items';
        if ($options) {
            $itemsCount = call_user_func_array([Comparison::className(), $counter], [$options]);
            $items = call_user_func_array([Comparison::className(), $getter], [$options, 0, $pageNum * ComparisonList::ITEMS_PER_PAGE, $sorting]);
        } else {
            $itemsCount = call_user_func([Comparison::className(), $counter]);
            $items = call_user_func_array([Comparison::className(), $getter], [0, $pageNum * ComparisonList::ITEMS_PER_PAGE, $sorting]);
        }

        $html = ComparisonList::widget([
            'items' => $items,
            'itemsCount' => $itemsCount,
            'loadMore' => false
        ]);

        return [
            'html' => $html,
            'pageNum' => $pageNum,
            'itemsRemain' => $itemsCount - $pageNum * ComparisonList::ITEMS_PER_PAGE
        ];
    }

	/**
	 * @param null $comparisonId
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
    public function actionView($comparisonId = null)
    {
        if (filter_var($comparisonId, FILTER_VALIDATE_INT)) {
            $comparison = Comparison::findById($comparisonId);
        } else {
            $comparison = Comparison::findByUrl($comparisonId);
        }

        if (!$comparison) {
            throw new NotFoundHttpException();
        }

        return $this->render('view', [
            'comparison' => $comparison,
            'carMainOptions' => $comparison->carMain->getTechnicalOptions(),
            'carCompareOptions' => $comparison->carCompare->getTechnicalOptions()
        ]);
    }

	/**
	 * @return array
	 */
    public function actionAddOpinion()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $comparisonId = Yii::$app->request->post('comparisonId');
        $like = Yii::$app->request->post('like');
        
        $model = $this->findModel($comparisonId);
        
        if ($model) {
            $thanks = new ComparisonThanks();
            $thanks->comparison_id = $model->id;
            $thanks->user_id = Yii::$app->user->identity->getId();
            $thanks->dislike = $like;
            
            if ($thanks->validate() && $thanks->save()) {
            	$type = $like ? Notification::TYPE_DISLIKE_COMPARISON : Notification::TYPE_LIKE_COMPARISON;

            	Notification::create($type, $model->user_id, [
            		'type' => $like ? Yii::t('app', 'disliked') : Yii::t('app', 'liked'),
		            'url' => Html::a($model->getShortName(), $model->getUrl())
	            ]);

                return [
                    'list' => $this->renderAjax('_thanks', ['comparison' => $model])
                ];
            } else {
                return [
                    'error' => ArrayHelper::toString($thanks->errors, '<br>')
                ];
            }
        }

        return [
            'error' => ArrayHelper::toString($model->errors, '<br>')
        ];
    }

	/**
	 * @return array
	 */
    public function actionAddFavorite()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $comparisonId = Yii::$app->request->post('comparisonId');
        $model = $this->findModel($comparisonId);

        if ($model) {
            $favorite = new UserFavorite();
            $favorite->comparison_id = $model->id;
            $favorite->user_id = Yii::$app->user->identity->getId();

            if ($favorite->validate() && $favorite->save()) {
	            Notification::create(Notification::TYPE_FAVORITE_COMPARISON, $model->user_id, [
		            'url' => Html::a($model->getShortName(), $model->getUrl())
	            ]);

                return [
                    'status' => 'ok'
                ];
            } else {
                return [
                    'error' => ArrayHelper::toString($favorite->errors, '<br>')
                ];
            }
        }

        return [
            'error' => ArrayHelper::toString($model->errors, '<br>')
        ];
    }

	/**
	 * @return array
	 */
    public function actionGetManufacturerModels()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $manufacturerId = Yii::$app->request->post('manufacturerId');
        $model = Manufacturer::findOne(['id' => $manufacturerId]);

        if ($model) {
            /** @var Model[] $list */
            $list = $model->getModels(true)->all();
            $optionString = '';

            foreach ($list as $item)
            {
                $optionString .= '<option value="' . $item->id . '">' . $item->getFullName(false) . '</option>';
            }

            return [
                'status' => 'ok',
                'models' => $optionString
            ];
        }

        return [
            'error' => Yii::t('app', 'Manufacturer not found')
        ];
    }

	/**
	 * @return array
	 */
    public function actionGetModel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $modelId = Yii::$app->request->post('modelId');
        $model = Model::findOne(['id' => $modelId]);
        $optionString = '';

        foreach ($model->getEngines(true)->all() as $engine)
        {
            /** @var Engine $engine */
            $optionString .= '<option value="' . $engine->id . '">' . $engine->getName() . '</option>';
        }

        if ($model) {
            return [
                'status' => 'ok',
                'image' => Html::img($model->getImage()),
                'engines' => $optionString
            ];
        }

        return [
            'error' => Yii::t('app', 'Model not found')
        ];
    }

	/**
	 * Increase comparison views
	 */
    public function actionUpdateViews()
    {
        $comparisonId = Yii::$app->request->post('comparisonId');
        $model = $this->findModel($comparisonId);

        if ($model) {
            $model->views = $model->views + 1;
            $model->save();
        }
    }

	/**
	 * @return array
	 * @throws \Exception
	 * @throws yii\db\StaleObjectException
	 */
    public function actionAddModel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = Comparison::add(Yii::$app->request->post());

        if ($model->hasErrors() || $model->customError) {
            $errors = [];
            if ($model->hasErrors()) {
                $errors[] = $model->errors;
            }
            if ($model->customError) {
                $errors[] = $model->customError;
            }

            return [
                'error' => ArrayHelper::toString($errors)
            ];
        }

        Yii::$app->session->set('comparisonData', null);

        return [
            'status' => 'ok'
        ];
    }

	/**
	 * Save session data
	 */
    public function actionSaveData()
    {
	    Yii::$app->session->set('comparisonData', Yii::$app->request->post('comparisonData'));
	    Yii::$app->session->set('carRequest', Yii::$app->request->post('carRequest'));
    }

    /**
     * @param $id
     * @return null|Comparison
     */
    public function findModel($id)
    {
        /** @var Comparison $model */
        $model = Comparison::findOne($id);

        return $model;
    }
}