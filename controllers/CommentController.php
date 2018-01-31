<?php
namespace app\controllers;

use app\components\ArrayHelper;
use app\models\Comparison;
use app\models\Comment;
use app\models\News;
use app\models\Notification;
use app\models\UserCommentKarma;
use yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Response;

class CommentController extends BaseController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['add'],
                'rules' => [
                    [
                        'actions' => ['add'],
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
                    'add' => ['post']
                ],
            ],
        ];
    }

	/**
	 * @return array
	 */
    public function actionAdd()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $objectId = Yii::$app->request->post('objectId');
        $objectClass = Yii::$app->request->post('objectClass');
        $commentText = Yii::$app->request->post('commentText');

        $model = self::findModel($objectClass, $objectId);

        if ($model) {
            $comment = new Comment();
            $comment->object_id = $objectId;
            $comment->object = $objectClass;
            $comment->status = Comment::COMMENT_STATUS_APPROVED;
            $comment->text = $commentText;

            if ($comment->validate() && $comment->save()) {
            	if ($objectClass == Comparison::className()) {
            		$comparison = Comparison::findOne($objectId);

		            Notification::create(Notification::TYPE_NEW_COMMENT, $comparison->user_id, [
			            'url' => Html::a($comparison->getShortName(), $comparison->getUrl())
		            ]);
	            }

                return [
                    'status' => 'ok',
                    'comment' => $this->renderAjax('@app/views/_comment', ['model' => $comment, 'counter' => 0])
                ];
            }

            return [
                'error' => ArrayHelper::toString($comment->errors, '<br>')
            ];
        }

        return [
            'error' => ArrayHelper::toString($model->errors, '<br>')
        ];
    }

	/**
	 * @return array
	 */
    public function actionManageKarma()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $objectId = Yii::$app->request->post('objectId');
        $objectClass = Yii::$app->request->post('objectClass');
        $commentId = Yii::$app->request->post('commentId');
        $type = Yii::$app->request->post('type');

        $model = self::findModel($objectClass, $objectId);

        if ($model) {
            $karma = new UserCommentKarma();
            $karma->comparison_id = $model->id;
            $karma->comment_id = $commentId;
            $karma->user_id = Yii::$app->user->getId();
            $karma->{$type} = 1;
            $date = $type . '_date';
            $karma->{$date} = date('Y-m-d H:i:s');

            if ($karma->validate() && $karma->save()) {
            	$comment = Comment::findOne($commentId);
            	Notification::create($type, $comment->user_id);

                return [
                    'status' => 'ok'
                ];
            }

            return [
                'error' => ArrayHelper::toString($karma->errors, '<br>')
            ];
        }

        return [
            'error' => ArrayHelper::toString($model->errors, '<br>')
        ];
    }

    /**
     * @param string
     * @param int
     * @return null|Comparison|News
     */
    public static function findModel($class, $id)
    {
        /** @var Comparison|News $model */
        $model = call_user_func_array([$class, 'findOne'], [$id]);

        return $model;
    }
}