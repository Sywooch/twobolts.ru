<?php
/* @var $this yii\web\View */
/* @var $searchModel app\models\ComparisonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\ArrayHelper;
use app\components\IconHelper;
use app\components\UserColumn;
use app\models\Comparison;
use app\models\Manufacturer;
use app\models\Model;
use app\models\User;
use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Comparisons');
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'Comparisons');

$manufacturerId = ArrayHelper::getValue(ArrayHelper::getValue(Yii::$app->request->queryParams, 'ComparisonSearch', []), 'manufacturer');
$modelId = ArrayHelper::getValue(ArrayHelper::getValue(Yii::$app->request->queryParams, 'ComparisonSearch', []), 'model');
?>

<div class="comparison-index">

    <h1><?= Html::encode(Yii::t('app/admin', 'Comparisons')) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= DynaGrid::widget([
        'gridOptions' => [
            'id' => 'w_admin_comparison',
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
                    'id' => 'w0_admin_comparison',
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
                        IconHelper::show('rating') . Yii::t('app/admin', 'Recalculate rating'),
                        ['#'],
                        [
                            'data-pjax' => 0,
                            'class' => 'btn btn-default recalculateRatingBtn',
                            'title' => Yii::t('app/admin', 'Recalculate rating')
                        ]
                    ) . Html::a(
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
                        ['/admin/comparison'],
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
            'id' => 'admin_comparison'
        ],
        'columns' => [
            [
                'class' => 'kartik\grid\CheckboxColumn',
                'order' => DynaGrid::ORDER_FIX_LEFT
            ],
            [
                'attribute' => 'date',
                'content' => function ($model) {
                    /** @var \app\models\ComparisonSearch $model */
                    return Yii::$app->formatter->asDatetime($model->date, 'medium');
                },
                'filterType' => GridView::FILTER_DATE_RANGE,
                'format' => 'raw',
                'filterWidgetOptions' => [
                    'hideInput' => false,
                    'presetDropdown' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'DD-MM-YYYY'
                        ],
                        'opens' => 'left'
                    ]
                ],
                'width' => '200px'
            ],
            [
                'label' => Yii::t('app', 'Comparison'),
                'content' => function ($model) {
                    /** @var \app\models\ComparisonSearch $model */
                    return Html::a(
                        $model->getFullName() . IconHelper::show('external-link'),
                        $model->getUrl(),
                        [
                            'class' => 'icon-right',
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]
                    );
                },
                'filter' => Html::dropDownList(
                    'ComparisonSearch[manufacturer]',
                    $manufacturerId,
                    ArrayHelper::merge(
                        ['' => Yii::t('app/admin', 'All manufacturers...')],
                        ArrayHelper::map(Manufacturer::filterData('comparisons'), 'id', 'name')
                    ),
                    [
                        'class' => 'form-control grid-filter-select',
                        'id' => 'comparison_manufacturer'
                    ]
                ) . ($manufacturerId ? Html::dropDownList(
                        'ComparisonSearch[model]',
                        $modelId,
                        ArrayHelper::merge(
                            ['' => Yii::t('app/admin', 'All models...')],
                            ArrayHelper::map(Model::filterData('comparisons', $manufacturerId), 'id', 'name')
                        ),
                        [
                            'class' => 'form-control grid-filter-select',
                            'id' => 'comparison_model'
                        ]
                    ) : ''),
                'width' => '50%'
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'user_id',
                'user_attribute' => 'user',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(
                    User::filterData('comparisons'),
                    'username',
                    'username'
                ),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
                'filterInputOptions' => [
                    'placeholder' => Yii::t('app/admin', 'All users...')
                ],
                'format' => 'raw',
                'width' => '250px'
            ],
            [
                'attribute' => 'active',
                'content' => function($model) {
                    /** @var \app\models\ComparisonSearch $model */
                    return '<span class="label label-lg label-' .
                    ($model->active ? 'success' : 'danger') . '">' .
                    Comparison::$statusLabels[$model->active] .
                    '</span>';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => Comparison::$statusLabels,
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
                'filterInputOptions' => [
                    'placeholder' => Yii::t('app/admin', 'All comparisons...')
                ],
                'format' => 'raw',
                'width' => '150px',
                'contentOptions' => function($model) {
                    return [
                        'class' => 'text-center status-'.$model->id
                    ];
                }
            ],
            [
                'attribute' => 'rating',
                'filter' => false,
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            // 'comment:ntext',
            // 'main_foto:ntext',
            // 'compare_foto:ntext',
            // 'main_time',
            // 'compare_time',
            // 'show_on_home',
            // 'url_title:ntext',
            // 'active',
            // 'rating',
            // 'views',
            [
                'class' => 'yii\grid\ActionColumn',
                'order' => DynaGrid::ORDER_FIX_RIGHT,
                'template' => '{moderate}{home}{delete}',
                'buttons' => [
                    'moderate' => function ($url, $model, $key) {
                        /** @var \app\models\ComparisonSearch $model */
                        $icon = $model->active ? 'ban-circle' : 'ok';
                        $action = $model->active ? 'ban' : 'activate';
                        return Html::a(
                            '<span class="glyphicon glyphicon-' . $icon . '"></span>',
                            [
                                $action, 'id' => $model->id
                            ],
                            [
                                'title' => Yii::t('app/admin', $action),
                                'aria-label' => Yii::t('app/admin', $action),
                                'data-method' => 'post',
                                'data-pjax' => 1
                            ]
                        );
                    },
                    'home' => function ($url, $model, $key) {
                        /** @var \app\models\ComparisonSearch $model */
                        $icon = $model->show_on_home ? 'ban-circle' : 'ok';
                        $action = $model->active ? 'ban' : 'activate';
                        return Html::a(
                            '<span class="glyphicon glyphicon-home"></span>',
                            [
                               'home', 'id' => $model->id
                            ],
                            [
                                'class' => $model->show_on_home ? 'active' : '',
                                'title' => Yii::t('app/admin', 'Show on home'),
                                'aria-label' => Yii::t('app/admin', 'Show on home'),
                                'data-method' => 'post',
                                'data-pjax' => 0
                            ]
                        );
                    },
                ],
                'contentOptions' => [
                    'class' => 'action-cell text-center',
                    'width' => '150px'
                ],
            ],
        ],
    ]); ?>
</div>

<?php
$js = <<<EOD
    admin.gridElem = $('#w_admin_comparison');
    admin.gridController = 'comparison';

    $('body').on('click', '.recalculateRatingBtn', function(e) {
        e.preventDefault();
        var selected = $('#w_admin_comparison').yiiGridView('getSelectedRows');
        
        if (selected.length == 0) {
            admin.showMessage(localizationMessages["Nothing is selected"], localizationMessages["error"]);
            return false;
        } else if (confirm(localizationMessages["Are you sure recalculate rating selected"])) {        
            $.post(
                "/admin/comparison/recalculate-rating", 
                {
                    selected : selected
                },
                function () {
                    window.location.href = window.location;
                }
            );
        }
    });
EOD;

$this->registerJs($js, \yii\web\View::POS_READY);