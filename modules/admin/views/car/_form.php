<?php
/* @var $this yii\web\View */
/* @var $model app\models\Car */
/* @var $form yii\widgets\ActiveForm */
/** @var TechnicalCategory[] $technicalOptions */

use app\components\ArrayHelper;
use app\components\IconHelper;
use app\models\Manufacturer;
use app\models\Model;
use app\models\TechnicalCategory;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$cssDisplay = $model->isNewRecord ? 'none' : 'block';

$cloneData = ['--- Выберите источник ---'];
$cloneCars = $model->getClones(false);

if ($cloneCars) {
	foreach ($cloneCars as $car)
	{
		$cloneData[$car->id] = $car->getFullName();
	}
}

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
                    <i class="fa fa-car"></i> <?= Yii::t('app/admin', $model->isNewRecord ? Yii::t('app/admin', 'Create Car') : Yii::t('app/admin', 'Update Car')); ?>
                </div>

                <div class="panel-body">
                    <div class="form-group car-manufacturer-wrapper">
                        <?= $form->field($model, 'manufacturer_id')->widget(Select2::className(), [
	                        'data' => ArrayHelper::map(Manufacturer::filterData(), 'id', 'name'),
	                        'options' => [
		                        'placeholder' => Yii::t('app/admin', 'Search for manufacturer...'),
		                        'class' => 'car-manufacturer-selector',
		                        'id' => 'manufacturer_id',
		                        'data-action' => '',
                                'disabled' => $model->isNewRecord ? false : true
	                        ],
	                        'pluginOptions' => [
		                        'allowClear' => true
	                        ]
                        ]); ?>
                    </div>

                    <div class="form-group car-model-wrapper">
                        <?= $form->field($model, 'model_id')->widget(Select2::className(), [
                            'data' => $model->isNewRecord ? [] : ArrayHelper::map(Model::filterData(null, $model->model->manufacturer_id), 'id', 'name'),
                            'options' => [
                                'placeholder' => Yii::t('app/admin', 'Search for model...'),
                                'class' => 'car-model-selector manufacturer-target form-control',
                                'disabled' => $model->isNewRecord ? false : true
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]); ?>
                    </div>

                    <?php if ($model->isNewRecord): ?>
                        <?= $form->field($model, 'engine_id')->hiddenInput(); ?>

                        <div class="car-engine-wrapper">
                            <?php if ($model->isNewRecord): ?>
                                <?= Yii::t('app/admin', 'Select manufacturer and model to see existing engines...'); ?>
                            <?php else: ?>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
	                    <?= $form->field($model, 'engine_id')->textInput([
                            'value' => $model->engine->getName(),
		                    'disabled' => true,
                        ]); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= Yii::t('app/admin', 'Technical options'); ?>
                </div>

                <div class="panel-body">
                    <div class="form-group car-clone-wrapper">
                        <?= Select2::widget([
                            'name' => 'cloneList',
                            'data' => $cloneData,
                            'options' => [
                                'id' => 'cloneList'
                            ],
                            'addon' => [
                                'append' => [
                                    'content' => Html::button(IconHelper::show('clone') . Yii::t('app/admin', 'Clone'), [
                                        'class' => 'btn btn-default',
                                        'title' => Yii::t('app/admin', 'Clone'),
                                        'data-toggle' => 'tooltip',
                                        'id' => 'cloneButton'
                                    ]),
                                    'asButton' => true
                                ]
                            ]
                        ]); ?>
                    </div>

                    <div class="car-options-wrapper">
                        <?= $this->render('_tech_tabs', [
                            'model' => $model,
                            'technicalOptions' => $technicalOptions
                        ])?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group fixed-bottom-toolbar">
        <?= Html::hiddenInput('isNew', $model->isNewRecord ? '1' : '0', ['id' => 'isNew']); ?>

        <?= Html::submitButton($model->isNewRecord ?
            IconHelper::show('add') . Yii::t('app/admin', 'Create Car') :
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
                    'data-callback' => '/admin/car/delete?id=' . $model->id
                ]);
        ?>

        <?= Html::a('<i class="fa fa-undo"></i>' . Yii::t('app/admin', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-default']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>