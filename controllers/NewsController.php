<?php
namespace app\controllers;


use app\models\News;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class NewsController extends BaseController
{
    public function actionIndex()
    {
        $models = $this->findModels(1);
        $meta = '';

        /** @var News[] $list */
        $list = array_slice($models, 0, 3);
        foreach ($list as $item) {
            $meta .= $item->title . '. ';
        }

        return $this->render('index', [
            'models' => $models,
            'modelsCount' => $this->findModelsCount(),
            'metaDescription' => $meta
        ]);
    }

    public function actionGetNews()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $page = Yii::$app->request->post('page');

        return $this->renderAjax('list', [
            'models' => $this->findModels($page),
            'page' => $page
        ]);
    }

    public function actionView($newsId)
    {
        if (filter_var($newsId, FILTER_VALIDATE_INT)) {
            $news = self::findModel($newsId);
        } else {
            $news = News::findByUrl($newsId);
        }

        if (!$news) {
            throw new NotFoundHttpException();
        }

        ++$news->num_views;
        $views = clone $news;
        $views->save();

        return $this->render('view', ['model' => $news]);
    }

    /**
     * @param $id
     * @return News
     */
    public static function findModel($id)
    {
        /** @var News $model */
        $model = News::findOne($id);

        return $model;
    }

    /**
     * @param $page
     * @return array|\yii\db\ActiveRecord[]|News[]
     */
    private function findModels($page)
    {
        return News::find()
            ->orderBy(['created' => SORT_DESC])
            ->limit(News::NEWS_PER_PAGE)
            ->offset(($page - 1) * News::NEWS_PER_PAGE)
            ->all();
    }

    private function findModelsCount()
    {
        return News::find()->count();
    }
}