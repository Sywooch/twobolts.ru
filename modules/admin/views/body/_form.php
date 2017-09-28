<?php
/* @var $this yii\web\View */
/* @var $model app\models\Body */
/* @var $form yii\widgets\ActiveForm */

use app\components\IconHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="body-form">

    <?php $form = ActiveForm::begin([
        'action' => '/admin/body/' . ($model->isNewRecord ? 'create' : 'update?id=' . $model->body_id),
        'options' => [
            'enctype' => 'multipart/form-data',
            'target' => 'form_frame',
            'id' => 'body-form' . ($model->isNewRecord ? '-0' : '-' . $model->body_id),
            'class' => 'modal-form'
        ]
    ]); ?>

    <?= $form->field($model, 'body_name')->textInput(); ?>

    <div class="pull-right modal-btn-group">
        <?= Html::submitButton($model->isNewRecord
            ? IconHelper::show('add') . Yii::t('app/admin', 'Create Model')
            : IconHelper::show('save') . Yii::t('app', 'Save'),
            ['class' => $model->isNewRecord ? 'btn btn-success btn-submit-form' : 'btn btn-primary btn-submit-form']
        ); ?>

        <?= Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']); ?>
    </div>

    <div class="clearfix"></div>

    <?php ActiveForm::end(); ?>

</div>