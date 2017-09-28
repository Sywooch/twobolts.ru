<?php
/**
 * Created by PhpStorm.
 * User: rzyuzin
 * Date: 04.08.2016
 * Time: 12:36
 */

namespace app\controllers;


use app\components\ArrayHelper;
use app\models\Car;
use app\models\CarComparisonCriteria;
use app\models\CarRequest;
use app\models\Comparison;
use app\models\ComparisonCriteria;
use app\models\Engine;
use app\models\Manufacturer;
use app\models\Model;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CatalogController extends BaseController
{
    public function actions()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'car-request' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $manufacturers = Manufacturer::findModels(true);
        $models = Model::findLastAdded();

        return $this->render('index', [
            'manufacturers' => $manufacturers,
            'models' => $models,
            'title' => Yii::t('app', 'Catalog full'),
            'metaDescription' => Yii::t('app/meta', 'Catalog Index Page Description')
        ]);
    }

    /**
     * @param $manufacturerId
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionManufacturer($manufacturerId)
    {
        /** @var $manufacturer Manufacturer */
        if (filter_var($manufacturerId, FILTER_VALIDATE_INT)) {
            $manufacturer = Manufacturer::find()->where('id = :manufacturer', [':manufacturer' => $manufacturerId])->one();
        } else {
            $manufacturer = Manufacturer::find()->where('url_title = :manufacturer', [':manufacturer' => $manufacturerId])->one();
        }

        if (!$manufacturer) {
            throw new NotFoundHttpException();
        }

        $sorting = isset($_COOKIE['_catalog_manufacturer_' . $manufacturer->id . '_sorting_']) ? $_COOKIE['_catalog_manufacturer_' . $manufacturer->id . '_sorting_'] : 'name';
        $cars = $manufacturer->findCars($sorting);

        return $this->render('manufacturer', [
            'manufacturer' => $manufacturer,
            'models' => $cars,
            'findAction' => 'find-manufacturer-items',
            'sorting' => $sorting,
            'params' => $manufacturer->id,
            'title' => $manufacturer->name,
            'metaDescription' => Yii::t('app', 'Catalog full') . ' ' . $manufacturer->name
        ]);
    }

    /**
     * @return array
     */
    public function actionGetManufacturerCars()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $manufacturerId = Yii::$app->request->post('manufacturerId');
        $sorting = Yii::$app->request->post('sorting');

        $manufacturer = Manufacturer::findOne($manufacturerId);

        if (!$manufacturer) {
            return [
                'error' => Yii::t('app/error', 'Manufacturer not found')
            ];
        }

        setcookie('_catalog_manufacturer_' . $manufacturer->id . '_sorting_', $sorting, time() + 86400, Yii::$app->params['cookieDomain']);
        $cars = $manufacturer->findCars($sorting);

        return [
            'list' => $this->renderAjax('manufacturer_list', ['models' => $cars])
        ];
    }

    /**
     * @param $carId
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($carId)
    {
        if (filter_var($carId, FILTER_VALIDATE_INT)) {
            $car = self::findModel($carId);
        } else {
            $car = Car::find()
                ->joinWith('engine')
                ->where(Engine::tableName() . '.url_title = :carId', [':carId' => $carId])
                ->one();
        }

        if (!$car) {
            throw new NotFoundHttpException();
        }

        //echo '<pre>'.print_r($car->getComments(), true);

        return $this->render('view', ['car' => $car]);
    }

    public function actionCarRequest()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new CarRequest();

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            $model->sendAdminNotification();

            return [
                'status' => 'ok',
                'message' => Yii::t('app', 'New car request was submitted')
            ];
        }

        return [
            'status' => 'error',
            'message' => ArrayHelper::toString($model->errors, '<br>')
        ];
    }

    /**
     * @param $id
     * @return null|Car
     */
    public static function findModel($id)
    {
        /** @var Car $model */
        $model = Car::findOne($id);

        return $model;
    }
}