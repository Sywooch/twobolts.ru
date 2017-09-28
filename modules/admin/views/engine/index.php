<?php

use app\components\ArrayHelper;
use app\components\IconHelper;
use app\components\ImageColumn;
use app\models\Body;
use app\models\Manufacturer;
use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EngineSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Engines');
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'Engines');
?>
<div class="engine-index">

    <p class="pull-right"><?= Html::a(IconHelper::show('add') . Yii::t('app/admin', 'Create Engine'), ['create'], ['class' => 'btn btn-success']) ?></p>

    <h1><?= Html::encode(Yii::t('app/admin', 'Engines')) ?></h1>

    <div class="clear"></div>

    <?= DynaGrid::widget([
        'gridOptions' => [
            'id' => 'w_admin_engines',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'resizableColumns' => false,
            'striped' => true,
            'bordered' => true,
            'hover' => true,
            'export' => false,
            'pjax' => true,
            'pjaxSettings' => [
                'neverTimeout' => true,
                'options' => [
                    'id' => 'w0_admin_engines',
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
                            ['/admin/engine'],
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
        'showFilter' => true,
        'enableMultiSort' => false,
        'showPersonalize' => true,
        'allowThemeSetting' => false,
        'theme' => 'simple-bordered',
        'storage'=>'cookie',
        'options' =>  [
            'id' => 'admin_engines'
        ],
        'columns' => [
            [
                'class' => 'kartik\grid\CheckboxColumn',
                'order' => DynaGrid::ORDER_FIX_LEFT
            ],
            [
                'attribute' => 'engine_name',
                'width' => '30%'
            ],
            [
                'label' => Yii::t('app', 'Manufacturer'),
                'attribute' => 'manufacturer_id',
                'content' => function ($model) {
                    /** @var \app\models\Engine $model */
                    return $model->model->manufacturer->name;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Manufacturer::filterData(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ],
                'filterInputOptions' => [
                    'placeholder' => Yii::t('app/admin', 'All engines...')
                ],
                'format' => 'raw',
                'width' => '15%'
            ],
            [
                'attribute' => 'model_id',
                'content' => function ($model) {
                    /** @var \app\models\Engine $model */
                    return $model->model->name;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(\app\models\Model::filterData(null, $searchModel->manufacturer_id), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
                'filterInputOptions' => [
                    'placeholder' => Yii::t('app/admin', 'All engines...')
                ],
                'format' => 'raw',
                'width' => '15%'
            ],
            [
                'attribute' => 'url_title',
                'width' => '30%',
            ],
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
    admin.gridElem = $('#w_admin_engines');
    admin.gridController = 'engine';
EOD;

$this->registerJs($js, \yii\web\View::POS_READY);