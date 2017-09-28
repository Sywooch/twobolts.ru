<?php

use app\components\IconHelper;
use app\components\UserColumn;
use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $searchModel app\models\CommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app', 'Comments');
$this->params['breadcrumbs'][] = Yii::t('app', 'Comments');
?>
<div class="comment-index">

    <h1><?= Html::encode(Yii::t('app', 'Comments')) ?></h1>

    <?= DynaGrid::widget([
        'gridOptions' => [
            'id' => 'w_admin_comments',
            'dataProvider' => $dataProvider,
            'resizableColumns' => false,
            'striped' => true,
            'bordered' => true,
            'hover' => true,
            'export' => false,
            'pjax' => true,
            'pjaxSettings' => [
                'neverTimeout' => true,
                'options' => [
                    'id' => 'w0_admin_comments',
                ]
            ],
            'panel' => [
                'type' => GridView::TYPE_DEFAULT,
                'heading' => false,
                'before' => '{summary}',
                'after' => false,
            ],
            'toolbar' =>  [
                [
                    'content' => Html::a(
                        IconHelper::show('delete') . Yii::t('app/admin', 'Delete'),
                        ['#'],
                        [
                            'data-pjax' => 0,
                            'class' => 'btn btn-default deleteSelectedBtn',
                            'title' => Yii::t('app/admin', 'Delete selected')
                        ]
                    )
                ],
                [
                    'content' => Html::a(
                            IconHelper::show('refresh') . Yii::t('app/admin', 'Clear'),
                            ['/admin/comments'],
                            [
                                'data-pjax' => 0,
                                'class' => 'btn btn-default',
                                'title' => Yii::t('app/admin', 'Reset data')
                            ]
                        ) . '{dynagridFilter}{dynagridSort}{dynagrid}{toggleData}'
                ]
            ]
        ],
        'showSort' => true,
        'enableMultiSort' => false,
        'showPersonalize' => true,
        'allowThemeSetting' => false,
        'theme' => 'simple-bordered',
        'storage'=>'cookie',
        'options' =>  [
            'id' => 'admin_comments'
        ],
        'columns' => [
            [
                'class' => 'kartik\grid\CheckboxColumn',
                'order' => DynaGrid::ORDER_FIX_LEFT,
            ],
            [
                'attribute' => 'created',
                'content' => function ($model) {
                    /** @var \app\models\NewsSearch $model */
                    return Yii::$app->formatter->asDatetime($model->created, 'medium');
                },
                'width' => '15%'
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'user_id',
                'user_attribute' => 'user',
                'width' => '10%'
            ],
            [
                'attribute' => 'text',
                'content' => function ($model) {
                    /** @var \app\models\CommentSearch $model */
                    return strip_tags($model->text);
                },
                'width' => '45%'
            ],
            [
                'label' => Yii::t('app/admin', 'Comment Relation'),
                'content' => function ($model) {
                    /** @var \app\models\CommentSearch $model */
                    $object = $model->owner;
                    if ($object) {
                        $title = $object instanceof \app\models\Comparison ? $object->getFullName() : $object->title;

                        return Html::a($title . IconHelper::show('external-link'), $object->getUrl(), [
                            'class' => 'icon-right',
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    }

                    return '<span class="label label-danger">' . Yii::t('app/admin', 'Deleted') . '</span>';
                },
                'width' => '20%',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'order' => DynaGrid::ORDER_FIX_RIGHT,
                'buttons' => [
                    'user-karma-up' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-thumbs-up"></span>', $url, [
                            'title' => Yii::t('app', 'Increase user karma'),
                            'aria-label' => Yii::t('app', 'Increase user karma')
                        ]);
                    },
                    'user-karma-down' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-thumbs-down"></span>', $url, [
                            'title' => Yii::t('app', 'Decrease user karma'),
                            'aria-label' => Yii::t('app', 'Decrease user karma')
                        ]);
                    }
                ],
                'template' => '{user-karma-up}{user-karma-down}{delete}',
                'contentOptions' => [
                    'class' => 'action-cell text-center',
                    'style' => 'width: 100px'
                ],
            ],
        ],
    ]); ?>
</div>

<?php
$js = <<<EOD
    admin.gridElem = $('#w_admin_comments');
    admin.gridController = 'comment';
EOD;

$this->registerJs($js, \yii\web\View::POS_READY);