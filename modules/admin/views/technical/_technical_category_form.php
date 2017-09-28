<?php
/* @var \yii\web\View $this */
/* @var \app\models\TechnicalCategory $model */

use app\components\IconHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'action' => '/admin/technical/' . ($model->isNewRecord ? 'create-category' : 'update-category?id=' . $model->id),
    'options' => [
        'enctype' => 'multipart/form-data',
        'target' => 'form_frame',
        'id' => 'technical-category-form' . ($model->isNewRecord ? '-0' : '-' . $model->id),
        'class' => 'modal-form'
    ]
]); ?>

<?= $form->field($model, 'category_name')->textInput(['placeholder' => Yii::t('app/admin', 'Enter technical category name')]); ?>

<div class="modal-btn-group">
    <?= Html::submitButton($model->isNewRecord
        ? IconHelper::show('add') . Yii::t('app', 'Add')
        : IconHelper::show('save') . Yii::t('app', 'Save'),
        ['class' => $model->isNewRecord ? 'btn btn-success btn-submit-form' : 'btn btn-primary btn-submit-form']
    ) . ' ' . Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']); ?>
</div>

<div class="clearfix"></div>

<?php ActiveForm::end(); ?>