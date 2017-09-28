<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Model */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="model-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'manufacturer_id')->textInput() ?>

    <?= $form->field($model, 'body_id')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'image')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url_title')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'is_popular')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
