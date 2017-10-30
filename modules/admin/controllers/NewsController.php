<?php

namespace app\modules\admin\controllers;

use app\components\behaviors\Referrer;
use app\components\StringHelper;
use app\models\Manufacturer;
use app\models\Upload;
use Yii;
use app\models\News;
use app\models\NewsSearch;
use yii\helpers\Inflector;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends DefaultController
{
    /**
     * Lists all News models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new News();
        $model->created = date('Y-m-d');
        $model->setScenario('insert');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/admin/news']);
        } else {
            $manufacturers = Manufacturer::findModels(true);

            return $this->render('create', [
                'model' => $model,
                'manufacturers' => $manufacturers
            ]);
        }
    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('update');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/admin/news']);
        } else {
            $manufacturers = Manufacturer::findModels(true);

            return $this->render('update', [
                'model' => $model,
                'manufacturers' => $manufacturers
            ]);
        }
    }

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['/admin/news']);
    }

    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param $id
     * @return array
     */
    public function actionDeleteFeaturedImage($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($id) {
            $this->findModel($id)->deleteFeaturedImage()->save();
        } else {
            (new News())->deleteFeaturedImage();
        }

        return [];
    }

    /**
     * Загрузка изображений в галерею новостей
     *
     * @return array
     */
    public function actionUploadGalleryImage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $upload = new Upload();
        $upload->imageFile = UploadedFile::getInstance($upload, 'imageFile');

        $imageName = '/uploads/temp/' . md5(News::className() . time() . $upload->imageFile->tempName) . '.' . $upload->imageFile->extension;

        if ($upload->imageFile->saveAs(Yii::getAlias('@webroot') . $imageName)) {
            return [
                'initialPreview' => [$imageName],
                'initialPreviewConfig' => [
                    [
                        'caption' => basename(Yii::getAlias('@webroot') . $imageName),
                        'size' => filesize(Yii::getAlias('@webroot') . $imageName),
                        'key' => $imageName
                    ]
                ],
                'imageName' => $imageName
            ];
        }

        return [
            'error' => $upload->errors
        ];
    }

	/**
	 * @param $id
	 *
	 * @return array
	 */
    public function actionDeleteGalleryImage($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($id) {
            $this->findModel($id)->deleteGalleryImage()->save();
        } else {
            (new News())->deleteGalleryImage();
        }

        return [];
    }

    /**
     * Формирует ссылку для новости
     * @return array
     */
    public function actionUrlTitle()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $title = Yii::$app->request->post('title');
        $urlTitle = Inflector::slug(Inflector::transliterate($title, 'Russian-Latin/BGN; NFKD'));

        return [
            'full' => $urlTitle,
            'ellipsized' => StringHelper::ellipsize($urlTitle, 30, .5)
        ];
    }
}