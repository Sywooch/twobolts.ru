<?php
/* @var \yii\web\View $this */
/* @var \app\models\ComparisonCriteria|\mongosoft\file\UploadBehavior $model */

use app\components\IconHelper;
use app\components\ImageHelper;
use kartik\file\FileInput;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\JsExpression;

$initialPreview = $model->icon ? [ImageHelper::getImageFile($model, 'icon')] : [];
$initialCaption = $model->icon ? basename(Yii::getAlias('@webroot') . ImageHelper::getImageFile($model, 'icon')) : '';
$initialPreviewConfig = $model->icon ? [
    [
        'caption' => $model->icon,
        'size' => filesize(Yii::getAlias('@webroot') . ImageHelper::getImageFile($model, 'icon')),
        'key' => $model->icon,
        'width' => '32px'
    ]
] : [];

?>

<?php $form = ActiveForm::begin([
    'action' => '/admin/criteria/' . ($model->isNewRecord ? 'create' : 'update?id=' . $model->id),
    'options' => [
        'enctype' => 'multipart/form-data',
        'target' => 'form_frame',
        'id' => 'criteria-form' . ($model->isNewRecord ? '-0' : '-' . $model->id),
        'class' => 'modal-form'
    ]
]); ?>

<?= $form->field($model, 'name')->textInput(); ?>

<?= $form->field($model, 'placeholder')->textInput(); ?>

<?= $form->field($model, 'show_on_home')->checkbox(); ?>

<?= $form->field($model, 'icon')->widget(FileInput::className(), [
    'options' => [
        'multiple' => false,
        'accept' => 'image/*'
    ],
    'pluginOptions' => [
        'showClose' => false,
        'showUpload' => false,
        'showRemove' => true,
        'initialPreview' => $initialPreview,
        'initialPreviewAsData' => true,
        'initialCaption' => $initialCaption,
        'initialPreviewConfig' => $initialPreviewConfig,
        'initialPreviewShowDelete' => false,
        'overwriteInitial' => true,
        'maxFileSize' => 2800
    ],
    'pluginEvents' => [
        'filecleared' => new JsExpression('function(event) { $(".criteria-icon-hidden").val(""); }')
    ]
]); ?>

<?= $form->field($model, 'icon')->label(false)->hiddenInput(['class' => 'criteria-icon-hidden']); ?>

<div class="pull-right modal-btn-group">
    <?= Html::submitButton($model->isNewRecord
        ? IconHelper::show('add') . Yii::t('app', 'Add')
        : IconHelper::show('save') . Yii::t('app', 'Save'),
        ['class' => $model->isNewRecord ? 'btn btn-success btn-submit-form' : 'btn btn-primary btn-submit-form']
    ) . ' ' . Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']); ?>
</div>

<div class="clearfix"></div>

<?php ActiveForm::end(); ?>