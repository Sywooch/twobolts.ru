<?php
/* @var \yii\web\View $this */
/* @var \app\models\TechnicalOption $model */
/* @var \app\models\TechnicalCategory[] $categories */

use app\components\ArrayHelper;
use app\components\IconHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'action' => '/admin/technical/' . ($model->isNewRecord ? 'create-option' : 'update-option?id=' . $model->id),
    'options' => [
        'enctype' => 'multipart/form-data',
        'target' => 'form_frame',
        'id' => 'technical-option-form' . ($model->isNewRecord ? '-0' : '-' . $model->id),
        'class' => 'modal-form'
    ]
]); ?>

<?= $form->field($model, 'tech_category_id')->dropDownList(ArrayHelper::merge(['' => Yii::t('app/admin', 'Enter technical category')], ArrayHelper::map($categories, 'id', 'category_name'))); ?>

<?= $form->field($model, 'option_name')->textInput(['placeholder' => Yii::t('app/admin', 'Enter technical option name')]); ?>

<?= $form->field($model, 'option_units')->textInput(['placeholder' => Yii::t('app/admin', 'Enter technical option units')]); ?>

    <p><i>Пример: мм, м/2, см<?= htmlentities('<'); ?>sup<?= htmlentities('>'); ?>3<?= htmlentities('<'); ?>/sup<?= htmlentities('>'); ?></i></p>

<div class="modal-btn-group">
    <?= Html::submitButton($model->isNewRecord
        ? IconHelper::show('add') . Yii::t('app', 'Add')
        : IconHelper::show('save') . Yii::t('app', 'Save'),
        ['class' => $model->isNewRecord ? 'btn btn-success btn-submit-form' : 'btn btn-primary btn-submit-form']
    ) . ' ' . Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']); ?>
</div>

<div class="clearfix"></div>

<?php ActiveForm::end(); ?>