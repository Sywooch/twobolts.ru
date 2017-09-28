<?php
/* @var $this yii\web\View */
/* @var $model app\models\Engine */
/* @var $form yii\widgets\ActiveForm */


use app\components\ArrayHelper;
use app\components\IconHelper;
use app\models\Manufacturer;
use app\models\Model;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$cssDisplay = $model->isNewRecord ? 'none' : 'block';

?>

<div class="model-form">

    <?php $form = ActiveForm::begin([
        'enableClientValidation' => false,
        'enableAjaxValidation' => false,
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-flask"></i> <?= Yii::t('app/admin', $model->isNewRecord ? Yii::t('app/admin', 'Create Engine') : Yii::t('app/admin', 'Update Engine')); ?>
                </div>

                <div class="panel-body">
                    <div class="form-group manufacturer-wrapper">
                        <?= Html::label(Yii::t('app', 'Manufacturer'), 'manufacturer_id');?>

                        <?= Select2::widget([
                            'name' => 'manufacturer_id',
                            'value' => $model->isNewRecord ? '' : $model->model->manufacturer_id,
                            'data' => ArrayHelper::map(Manufacturer::filterData(), 'id', 'name'),
                            'options' => [
                                'placeholder' => Yii::t('app/admin', 'Search for manufacturer...'),
                                'class' => 'engine-source manufacturer-selector',
                                'id' => 'manufacturer_id',
                                'data-action' => ''
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ]
                        ]); ?>
                    </div>

                    <div class="model-wrapper">
                        <?= $form->field($model, 'model_id')->widget(Select2::className(), [
                            'data' => $model->isNewRecord ? [] : ArrayHelper::map(Model::filterData(null, $model->model->manufacturer_id), 'id', 'name'),
                            'options' => [
                                'placeholder' => Yii::t('app/admin', 'Search for model...'),
                                'class' => 'engine-source manufacturer-target form-control',
                            ],
                            'disabled' => $model->isNewRecord ? true : false,
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]); ?>
                    </div>

                    <?= $form->field($model, 'engine_name')->textInput(['class' => 'engine-source form-control']); ?>

                    <?= $form->field($model, 'url_title', [
                        'template' => '{label}<div class="input-group">{input}<span class="input-group-btn">' .
                            '<span class="btn btn-default get-url-link" data-source=".engine-source" data-target=".engine-target" ' .
                            ($model->isNewRecord ? 'disabled' : '') . '>' .
                            '<i class="fa fa-link"></i>Сформировать...' .
                            '</span>' .
                            '</span></div>{hint}{error}'
                    ])->textInput([
                        'class' => 'engine-target form-control',
                        'disabled' => $model->isNewRecord ? true : false
                    ]); ?>

                    <p class="text-muted" style="margin-top: -10px; margin-bottom: 0;"><em>Ссылка формируется двумя способами:</em></p>

                    <ol class="text-muted" style="padding-left: 20px; margin: 0;">
                        <li><em>Вручную, нажав кнопку "Сформировать";</em></li>
                        <li><em>Автоматически, если поле ссылки пустое или выбрана опция копирования двигателей из существующих.</em></li>
                    </ol>

                    <input type="hidden" name="manufacturer_name" id="manufacturer_name">

                    <input type="hidden" name="model_name" id="model_name">
                </div>
            </div>
        </div>

        <div class="col-md-3" style="display: <?= $model->isNewRecord ? 'block' : 'none'; ?>;">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= Yii::t('app/admin', 'Existing engines')?>
                    <span class="badge existing-engines-count" style="display: <?= $cssDisplay; ?>;"></span>
                </div>

                <div class="panel-body">
                    <div class="existing-engines-toggle" style="display: <?= $cssDisplay; ?>;">
                        <div class="btn-group btn-group-sm" role="group" aria-label="..." style="margin-bottom: 10px;">
                            <button class="btn btn-default" id="engines_select_all" data-checked="false">
                                <i class="fa fa-toggle-on" aria-hidden="true"></i><?= Yii::t('app/admin', 'Select all'); ?>
                            </button>

                            <button class="btn btn-default" id="engines_show_selected" data-show="all">
                                <i class="fa fa-eye" aria-hidden="true"></i><?= Yii::t('app/admin', 'Show selected'); ?>
                            </button>
                        </div>
                    </div>

                    <div class="existing-engines">
                        <?php if ($model->isNewRecord): ?>
                            <?= Yii::t('app/admin', 'Select manufacturer to see existing engines...'); ?>
                        <?php else: ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group fixed-bottom-toolbar">
        <?= Html::hiddenInput('isNew', $model->isNewRecord ? '1' : '0', ['id' => 'isNew']); ?>

        <?= Html::submitButton($model->isNewRecord ?
            IconHelper::show('add') . Yii::t('app/admin', 'Create Engine') :
            IconHelper::show('save') . Yii::t('app', 'Save'),
            [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                'id' => 'btnSaveEngine'
            ]
        ); ?>

        <?= $model->isNewRecord
            ? ''
            : Html::a(
                '<i class="fa fa-trash"></i>' . Yii::t('app/admin', 'Delete'),
                '#',
                [
                    'class' => 'btn btn-danger',
                    'data-confirmation' => Yii::t('app/admin', 'Are you sure to delete?'),
                    'data-callback' => '/admin/engine/delete?id=' . $model->id
                ]);
        ?>

        <?= Html::a('<i class="fa fa-undo"></i>' . Yii::t('app/admin', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-default']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>