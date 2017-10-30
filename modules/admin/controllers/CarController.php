<?php
namespace app\modules\admin\controllers;

use app\components\ArrayHelper;
use app\models\Car;
use app\models\CarSearch;
use app\models\CarTechnical;
use app\models\TechnicalCategory;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CarController extends DefaultController
{
    /**
     * Lists all Car models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CarSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Car model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
	    $existingEngines = Yii::$app->request->post('existing_engines');
	    $technical = Yii::$app->request->post('car_technical');
	    $model = new Car();

	    if ($existingEngines) {
	    	$isCreated = 0;

		    foreach ($existingEngines as $engineId)
		    {
			    $car = Car::findOne($engineId);

			    if (is_null($car)) {
				    $model = new Car();
				    $model->load(Yii::$app->request->post());
				    $model->engine_id = $engineId;

				    if ($model->save()) {
					    $model->createTechnical($technical);
					    ++ $isCreated;
				    }
			    }
		    }

		    if ($isCreated) {
			    Yii::$app->getSession()->setFlash('success',
				    Yii::t('app/admin', "Created cars") .
				    Yii::t('app/admin', '{n,plural,=0{cars} one{cars} few{cars} other{cars}}', ['n' => $isCreated])
			    );
		    }

		    return $this->redirect(['/admin/car']);
	    } elseif ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/admin/car']);
        } else {
            $technicalOptions = TechnicalCategory::find()
                ->joinWith('options')
                ->orderBy([
                    TechnicalCategory::tableName() . '.category_order' => SORT_ASC
                ])
                ->all();

            return $this->render('create', [
                'model' => $model,
                'technicalOptions' => $technicalOptions
            ]);
        }
    }

	/**
	 * @param $id
	 *
	 * @return string|Response
	 */
    public function actionUpdate($id)
    {
    	$model = $this->findModel($id);
	    $technical = Yii::$app->request->post('car_technical');

	    if ($technical) {
	    	$model->updateTechnical($technical);

		    return $this->redirect(['/admin/car']);
	    }

	    $technicalOptions = TechnicalCategory::find()
		    ->joinWith('options')
		    ->orderBy([
			    TechnicalCategory::tableName() . '.category_order' => SORT_ASC
		    ])
		    ->all();

	    return $this->render('update', [
		    'model' => $model,
		    'technicalOptions' => $technicalOptions
	    ]);
    }

	/**
	 * @param $id
	 *
	 * @return Response
	 */
    public function actionDelete($id)
    {
	    $this->findModel($id)->delete();

	    Yii::$app->getSession()->setFlash('success', Yii::t('app/admin', "Car was deleted."));

	    return $this->redirect(['/admin/car']);
    }

    /**
     * Load for for technical options
     * @return array
     */
    public function actionGetTechOptionsForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $carId = Yii::$app->request->post('carId');
        $car = $this->findModel($carId);

        if ($car) {
            $tree = $this->renderAjax('_tech_tree', ['car' => $car]);

            return [
                'status' => 'ok',
                'carName' => $car->getFullName(),
                'clones' => $car->getClones(),
                'tree' => $tree
            ];
        }

        return [
            'error' => Yii::t('app', 'Car not found')
        ];
    }

    /**
     * List of technical options for Car model
     * @return array
     */
    public function actionGetTechOptions()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $carId = Yii::$app->request->post('carId');
        $car = $this->findModel($carId);

        if ($car) {
            return [
                'status' => 'ok',
                'options' => $car->getTechnicalOptions()
            ];
        }

        return [
            'error' => Yii::t('app', 'Car not found')
        ];
    }

    /**
     * Save technical options for Car model
     * @return array
     */
    public function actionSaveTechOptions()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $carId = Yii::$app->request->post('carId');
        $data = Yii::$app->request->post('carData');
        $data = ArrayHelper::index($data, 'techOptionId');

        $car = $this->findModel($carId);

        if ($car) {
            if (!$car->technical) {
                $car->addCarTechnical();
            }

            /** @var CarTechnical $item */
            foreach ($car->getTechnical()->all() as $item) {
                $dataItem = ArrayHelper::getValue($data, $item->tech_option_id);
                if ($item->tech_option_value != $dataItem['techOptionValue']) {
                    $item->tech_option_value = $dataItem['techOptionValue'];
                    if ($item->validate()) {
                        $item->save();
                    }
                }
            }

            return [
                'status' => 'ok'
            ];
        }

        return [
            'error' => Yii::t('app', 'Car not found')
        ];
    }

    /**
     * Finds the Enfine model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Car the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Car::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}