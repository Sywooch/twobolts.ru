<?php

/* @var \app\components\widgets\ComparisonForm $comparison */
/* @var string $type */

use app\components\ArrayHelper;
use app\components\IconHelper;
use app\models\Comparison;
use kartik\switchinput\SwitchInput;
use yii\helpers\Html;

?>

<div class="custom-select-wrap">
	<?= Html::dropDownList(
		'main_manufacturer',
		$comparison->{$type . 'ManufacturerId'},
		ArrayHelper::merge(
			['0' => Yii::t('app', 'Manufacturer')],
			ArrayHelper::map($comparison->manufacturers, 'id', 'name')
		),
		['class' => 'custom-select', 'id' => $type . '_manufacturer']
	); ?>
</div>

<div class="custom-select-wrap">
	<?= Html::dropDownList(
		'main_model',
		$comparison->{$type . 'ModelId'},
		ArrayHelper::merge(
			['0' => Yii::t('app', 'Model')],
			$comparison->{$type . 'Models'}
		),
		['class' => 'custom-select' . ($comparison->{$type . 'ManufacturerId'} ? '' : ' disabled'), 'id' => $type . '_model']
	); ?>
</div>

<?php if ($comparison->{$type . 'Photo'}): ?>
	<div id="<?= $type; ?>_photo"><?= Html::img($comparison->{$type . 'Photo'}); ?></div>
<?php else: ?>
	<div id="<?= $type; ?>_photo" style="display: none;"></div>
<?php endif; ?>

<div id="<?= $type; ?>_image_container" style="display: <?= $comparison->{$type . 'Photo'} ? 'block' : 'none' ?>;">
	<?= Html::button(IconHelper::show('upload') . Yii::t('app', 'Upload own image'), [
		'type' => 'button',
		'id' => $type . '_image',
		'class' => 'btn btn-default btn-noshadow',
		'style' => 'width: 190px; height: 34px;'
	]); ?>
</div>

<div class="custom-select-wrap">
	<?= Html::dropDownList(
		$type . '_engine',
		$comparison->{$type . 'EngineId'},
		ArrayHelper::merge(
			['0' => Yii::t('app', 'Engine')],
			$comparison->{$type . 'Engines'}
		),
		['class' => 'custom-select' . ($comparison->{$type . 'ModelId'} ? '' : ' disabled'), 'id' => $type . '_engine']
	); ?>
</div>

<div class="custom-select-wrap">
	<?= Html::dropDownList(
		'main_time',
		$comparison->{$type . 'Time'},
		ArrayHelper::merge(['0' => Yii::t('app', 'Comparison time')], Comparison::getComparisonTimes()),
		['class' => 'custom-select' . ($comparison->{$type . 'EngineId'} ? '' : ' disabled'), 'id' => $type . '_time']
	); ?>
</div>

<div class="switch-<?= $type; ?>-garage">
	<?= SwitchInput::widget([
		'name' => 'myGarage[]',
		'value' => $comparison->{$type . 'State'},
		'tristate' => true,
		'indeterminateToggle' => false,
		'options' => [
			'class' => 'switch-input',
			'data-value' => $type
		],
		'pluginOptions'=>[
			'indeterminate' => true,
			'size' => 'large',
			'handleWidth' => 250,
			'labelWidth' => 50,
			'onText' => Yii::t('app', 'In garage'),
			'offText' => Yii::t('app', 'Drive before')
		]
	]); ?>

	<div class="switch-input-clear"><i class="fa fa-times"></i> Сбросить</div>
</div>
