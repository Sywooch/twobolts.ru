<?php
/** @var yii\web\View $this */
/** @var app\models\Car $car */
/** @var app\models\CarRequest $carRequest */
/** @var \app\models\Manufacturer[] $manufacturers */
/** @var \app\models\ComparisonCriteria[] $criteria */
/** @var array $comparisonData */

use app\components\widgets\ComparisonForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;

$this->title = Yii::t('app', 'New comparison') . ' — ' . Yii::$app->params['siteTitle'];
$this->registerMetaTag([
    'description' => Yii::t('app', 'New comparison')
]);

?>

<div class="comparison-add inside height-wrapper">
    <h1><?= Yii::t('app', 'New comparison'); ?></h1>

    <?= ComparisonForm::widget([
        'manufacturers' => $manufacturers,
        'criteria' => $criteria,
	    'requestData' => $comparisonData,
        'car' => $car,
        'carRequest' => $carRequest
    ]); ?>
</div>

<?php

$this->registerCssFile('/js/jquery/cropper/dist/cropper.min.css');
$this->registerJsFile('/js/jquery/jquery.ocupload-1.1.2.packed.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('/js/jquery/cropper/dist/cropper.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('/js/jquery/autosize/dist/autosize.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('/js/jquery/jquery.actual.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

Modal::begin([
    'id' => 'cropperDlg',
    'header' => '<h4 class="modal-title">' . Yii::t('app', 'New Image'). '</h4>',
    'footer' => '<div class="btn-group">' .
        Html::button(Yii::t('app', 'Continue'), ['class' => 'btn btn-orange', 'data-crop' => 'getData', 'data-option' => '']) .
        Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-orange', 'data-dismiss' => 'modal']) .
        '</div>'
]);
?>

<div class="img-container">
    <img src="" alt="" id="cropped_img">
</div>

<div class="docs-buttons">
    <!-- <h3 class="page-header">Toolbar:</h3> -->
    <div class="btn-group">
        <button class="btn btn-primary" data-crop="setDragMode" data-option="move" type="button" title="<?= Yii::t('app', 'Cropper Move Image'); ?>">
            <span class="fa fa-arrows"></span>
        </button>
        <button class="btn btn-primary" data-crop="setDragMode" data-option="crop" type="button" title="<?= Yii::t('app', 'Cropper Move Crop'); ?>">
            <span class="fa fa-crop"></span>
        </button>
        <button class="btn btn-primary" data-crop="zoom" data-option="0.1" type="button" title="<?= Yii::t('app', 'Cropper Zoom In'); ?>">
            <span class="fa fa-search-plus"></span>
        </button>
        <button class="btn btn-primary" data-crop="zoom" data-option="-0.1" type="button" title="<?= Yii::t('app', 'Cropper Zoom Out'); ?>">
            <span class="fa fa-search-minus"></span>
        </button>
    </div>
</div><!-- /.docs-buttons -->

<?php
Modal::end();
?>