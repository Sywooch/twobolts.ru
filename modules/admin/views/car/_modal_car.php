<?php
use app\components\IconHelper;
use yii\bootstrap\Modal;
use yii\helpers\Html;

Modal::begin([
    'id' => 'carTechDlg',
    'header' => '<h4 class="modal-title"></h4>',
    'size' => Modal::SIZE_LARGE,
    'footer' => '<div class="btn-group">' .
        Html::button(Yii::t('app/admin', 'Save'), ['class' => 'btn btn-orange btnEditCarTech']) .
        Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-orange', 'data-dismiss' => 'modal']) .
        '</div>'
]);
?>

<div id="car_form" style="display: none;">
    <input type="hidden" name="editCarId" id="editCarId">

    <h2 style="margin: 0 0 30px 0;"></h2>

    <label for="cloneList">Клонировать характеристики:</label>

    <div class="clone-wrapper">
        <select name="cloneList" class="manufacturers-list form-control" id="cloneList">
            <option value="0">--- Выберите источник ---</option>
        </select>

        <button type="button" id="cloneButton" class="btn btn-default btn-noshadow"><?= IconHelper::show('clone') . Yii::t('app/admin', 'Clone'); ?></button>
    </div>

    <div id="car-accordion-wrapper"></div>
</div>

<?php Modal::end(); ?>