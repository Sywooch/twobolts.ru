<?php

use app\components\IconHelper;
use app\components\ImageColumn;
use app\models\User;
use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Users');
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'Users');

?>

<div class="user-index">

	<p class="pull-right"><?= Html::a(IconHelper::show('add') . Yii::t('app/admin', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?></p>

	<h1><?= Html::encode(Yii::t('app/admin', 'Users')) ?></h1>

	<div class="clear"></div>

	<?= DynaGrid::widget([
		'gridOptions' => [
			'id' => 'w_admin_users',
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
					'id' => 'w0_admin_users',
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
							['/admin/user'],
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
			'id' => 'admin_users'
		],
		'columns' => [
			[
				'class' => 'kartik\grid\CheckboxColumn',
				'order' => DynaGrid::ORDER_FIX_LEFT
			],
			'username',
			'email:email',
			[
                'attribute' => 'activated',
                'content' => function ($model) {
	                /** @var \app\models\UserSearch $model */
	                return $model->activated ? '<i class="fa fa-check-square-o fa-2x text-success"></i>' : '';
                },
                'contentOptions' => [
	                'class' => 'text-center'
                ],
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ['1' => Yii::t('app/admin', 'Activated'), '-1' => Yii::t('app/admin', 'Not activated')],
                'filterWidgetOptions' => [
	                'pluginOptions' => [
		                'allowClear' => true
	                ],
                ],
                'filterInputOptions' => [
	                'placeholder' => Yii::t('app/admin', 'All users...')
                ],
                'format' => 'raw',
            ],
			[
				'attribute' => 'banned',
				'content' => function ($model) {
					/** @var \app\models\UserSearch $model */
					return $model->banned ? '<i class="fa fa-ban fa-2x text-danger"></i>' : '';
				},
				'contentOptions' => [
					'class' => 'text-center'
				],
				'filterType' => GridView::FILTER_SELECT2,
				'filter' => ['1' => Yii::t('app/admin', 'Banned'), '-1' => Yii::t('app/admin', 'Active')],
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'allowClear' => true
					],
				],
				'filterInputOptions' => [
					'placeholder' => Yii::t('app/admin', 'All users...')
				],
				'format' => 'raw',
			],
			// 'ban_reason',
			// 'new_password_key',
			// 'new_password_requested',
			// 'new_email:email',
			// 'new_email_key:email',
			// 'last_ip',
			// 'last_login',
			[
				'attribute' => 'created',
				'format' => ['date', 'dd.MM.yyyy'],
				'filterType' => GridView::FILTER_DATE_RANGE,
				'filterWidgetOptions' => [
					'language' => Yii::$app->language,
					'presetDropdown' => true,
					'hideInput' => true,
					'useWithAddon' => false,
					'pluginOptions' => [
						'autoUpdateInput' => true,
						'locale' => [
							'format' => 'DD.MM.YYYY'
						],
						'opens' => 'left',
					],
				],
            ],
			// 'modified',
			[
                'attribute' => 'role',
                'content' => function ($model) {
	                /** @var \app\models\UserSearch $model */
	                return $model->getRoleName();
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => User::getRoles(),
                'filterWidgetOptions' => [
	                'pluginOptions' => [
		                'allowClear' => true
	                ],
                ],
                'filterInputOptions' => [
	                'placeholder' => Yii::t('app/admin', 'All users...')
                ],
                'format' => 'raw',
            ],
			[
				'class' => ImageColumn::className(),
				'attribute' => 'avatar',
				'contentOptions' => [
					'class' => 'text-center'
				],
				'filterType' => GridView::FILTER_SELECT2,
				'filter' => ['1' => Yii::t('app/admin', 'Have image'), '-1' => Yii::t('app/admin', 'No image')],
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'allowClear' => true
					],
				],
				'filterInputOptions' => [
					'placeholder' => Yii::t('app/admin', 'All users...')
				],
				'format' => 'raw',
			],
			// 'timezone',
			// 'karma',
			// 'vkontakte_id',
			// 'vkontakte_token:ntext',
			// 'odnoklassniki_id',
			// 'odnoklassniki_token:ntext',
			// 'facebook_id',
			// 'facebook_token:ntext',
			// 'twitter_id',
			// 'twitter_token:ntext',
			// 'google_id',
			// 'google_token:ntext',
			// 'avatar:ntext',
			// 'uploaded_avatar:ntext',
			// 'hash:ntext',
			// 'hash_created',

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
    admin.gridElem = $('#w_admin_users');
    admin.gridController = 'user';
EOD;

$this->registerJs($js, \yii\web\View::POS_READY);