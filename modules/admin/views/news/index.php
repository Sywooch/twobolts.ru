<?php
/* @var $this yii\web\View */
/* @var $searchModel app\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\IconHelper;
use app\components\UserColumn;
use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'News');
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'News');
?>
<div class="news-index">

    <p class="pull-right"><?= Html::a(IconHelper::show('add') . Yii::t('app/admin', 'Create News'), ['create'], ['class' => 'btn btn-success']) ?></p>

    <h1><?= Html::encode(Yii::t('app/admin', 'News')) ?></h1>

    <div class="clear"></div>

    <?= DynaGrid::widget([
        'gridOptions' => [
            'id' => 'w_admin_news',
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
                    'id' => 'w0_admin_news',
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
                            ['/admin/news'],
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
            'id' => 'admin_news'
        ],
        'columns' => [
            [
                'class' => 'kartik\grid\CheckboxColumn',
                'order' => DynaGrid::ORDER_FIX_LEFT
            ],
            [
                'attribute' => 'created',
                'content' => function ($model) {
                    /** @var \app\models\NewsSearch $model */
                    return Yii::$app->formatter->asDate($model->created, 'medium');
                },
                'width' => '200px'
            ],
            [
                'attribute' => 'title',
                'content' => function ($model) {
                    /** @var \app\models\NewsSearch $model */
                    return Html::a(
                        $model->title . IconHelper::show('external-link'),
                        $model->getUrl(),
                        [
                            'class' => 'icon-right',
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]
                    );
                },
            ],
            [
                'attribute' => 'content',
                'content' => function ($model) {
                    /** @var \app\models\NewsSearch $model */
                    return \yii\helpers\StringHelper::truncateWords(strip_tags($model->content), 30);
                },
                'width' => '50%'
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'user_id',
                'user_attribute' => 'user',
                'width' => '200px'
            ],
            // 'excerpt:ntext',
            // 'include_excerpt',
            // 'featured_image:ntext',
            // 'video:ntext',
            // 'gallery:ntext',
            // 'source:ntext',
            // 'featured_image_caption:ntext',
            // 'user_id',
            [
                'attribute' => 'num_views',
                'label' => IconHelper::show('eye'),
                'encodeLabel' => false,
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'label' => IconHelper::show('comments'),
                'content' => function ($model) {
                    /** @var \app\models\NewsSearch $model */
                    return $model->getCommentsCount();
                },
                'encodeLabel' => false,
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            // 'url_title:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'order' => DynaGrid::ORDER_FIX_RIGHT,
                'template' => '{update}{delete}',
                'contentOptions' => [
                    'class' => 'action-cell text-center',
                    'width' => '100px'
                ],
            ],
        ],
    ]); ?>
</div>

<?php
$js = <<<EOD
    admin.gridElem = $('#w_admin_news');
    admin.gridController = 'news';
EOD;

$this->registerJs($js, \yii\web\View::POS_READY);