<?php
/* @var \app\models\CarRequest[] $models */
/* @var \yii\data\ActiveDataProvider $dataProvider */

use yii\bootstrap\Html;
use yii\widgets\ListView;

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Car requests');
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'Car requests');

?>

<div class="car-request-index">

	<h1><?= Html::encode(Yii::t('app/admin', 'Car requests')); ?></h1>

	<div class="row">
		<div class="col-lg-6">
			<?= ListView::widget([
				'dataProvider' => $dataProvider,
				'options' => [
					'class' => 'list-group',
				],
				'itemOptions' => [
					'class' => 'list-group-item'
				],
				'itemView' => function ($model, $key, $index, $widget) {
					/** @var \app\models\CarRequest $model */
					return $model->manufacturer . ' ' . $model->model . '<br><em>' . Yii::t('app/admin', 'User') . ': <strong>' . $model->user->username . '</strong></em>' .
					       '<span class="action-cell">' .
					       Html::a('<i class="glyphicon glyphicon-send"></i>', '#', ['class' => 'btn-approve-request']) .
					       Html::a('<i class="glyphicon glyphicon-trash"></i>', '#', ['class' => 'btn-delete-request']) .
					       '</span>';
				},
				'layout' => "{items}",
			]); ?>
		</div>
	</div>

</div>
