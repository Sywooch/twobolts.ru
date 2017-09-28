<?php

use app\components\IconHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Manufacturer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="manufacturer-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-industry"></i> <?= Yii::t('app/admin', $model->isNewRecord ? Yii::t('app/admin', 'Create Manufacturer') : Yii::t('app/admin', 'Update Manufacturer')); ?>
                </div>

                <div class="panel-body">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class' => 'manufacturer-source form-control']); ?>

                    <?= $form->field($model, 'url_title', [
                        'template' => '{label}<div class="input-group">{input}<span class="input-group-btn">' .
                            '<span class="btn btn-default get-url-link" data-source=".manufacturer-source" data-target=".manufacturer-target">' .
                            '<i class="fa fa-link"></i>Сформировать...' .
                            '</span>' .
                            '</span></div>{hint}{error}'
                    ])->textInput(['class' => 'manufacturer-target form-control']); ?>

                    <?= $form->field($model, 'is_popular')->checkbox(); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group fixed-bottom-toolbar">
        <?= Html::hiddenInput('isNew', $model->isNewRecord ? '1' : '0', ['id' => 'isNew']); ?>

        <?= Html::submitButton($model->isNewRecord ?
            IconHelper::show('add') . Yii::t('app/admin', 'Create Manufacturer') :
            IconHelper::show('save') . Yii::t('app', 'Save'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ); ?>

        <?= $model->isNewRecord
            ? ''
            : Html::a(
                '<i class="fa fa-trash"></i>' . Yii::t('app/admin', 'Delete'),
                '#',
                [
                    'class' => 'btn btn-danger',
                    'data-confirmation' => Yii::t('app/admin', 'Are you sure to delete?'),
                    'data-callback' => '/admin/manufacturer/delete?id=' . $model->id
                ]);
        ?>

        <?= Html::a('<i class="fa fa-undo"></i>' . Yii::t('app/admin', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-default']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>