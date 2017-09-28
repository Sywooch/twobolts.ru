<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Comment;
use app\models\CommentSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CommentController implements the CRUD actions for Comment model.
 */
class CommentController extends DefaultController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Comment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CommentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing Comment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['/admin/comment']);
    }

    /**
     * Удаляет выбранные новости
     * @return mixed
     */
    public function actionDeleteSelected()
    {
        $selected = Yii::$app->request->post('selected');

        if (is_array($selected) && $selected) {
            foreach ($selected as $id)
            {
                $this->findModel($id)->delete();
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Увеличивает карму пользователя
     * @param $id
     * @return \yii\web\Response
     */
    public function actionUserKarmaUp($id)
    {
        $user = $this->findModel($id)->user;
        if ($user) {
            ++$user->karma;
            $user->save(false);
        }

        return $this->redirect(['/admin/comment']);
    }

    /**
     * Уменьшает карму пользователя
     * @param $id
     * @return \yii\web\Response
     */
    public function actionUserKarmaDown($id)
    {
        $user = $this->findModel($id)->user;
        if ($user) {
            --$user->karma;
            $user->save(false);
        }

        return $this->redirect(['/admin/comment']);
    }

    /**
     * Finds the Comment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Comment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Comment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}