<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $profile \app\models\UserProfile */

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Create User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/admin', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'Create User');
?>
<div class="user-create">

	<h1><?= Yii::t('app/admin', 'Create User'); ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
		'profile' => $profile
	]); ?>

</div>