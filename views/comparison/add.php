<?php
/** @var yii\web\View $this */
/** @var app\models\Car $car */
/** @var app\models\CarRequest $carRequest */
/** @var \app\models\Manufacturer[] $manufacturers */
/** @var \app\models\ComparisonCriteria[] $criteria */

use app\components\ArrayHelper;
use app\components\IconHelper;
use app\models\Car;
use app\models\Comparison;
use app\models\Engine;
use app\models\Model;
use kartik\switchinput\SwitchInput;
use yii\bootstrap\Modal;
use yii\helpers\Html;

$this->title = Yii::t('app', 'New comparison') . ' — ' . Yii::$app->params['siteTitle'];
$this->registerMetaTag([
    'description' => Yii::t('app', 'New comparison')
]);
?>

<div class="comparison-add inside height-wrapper">
    <h1><?= Yii::t('app', 'New comparison'); ?></h1>

    <?php if ($criteria): ?>

        <div class="comparison-add-vertical-line">
            <div class="comparison-add-item-wrap">
                <div class="custom-select-wrap">
                    <?= Html::dropDownList(
                        'main_manufacturer',
                        $car ? $car->manufacturer_id : null,
                        ArrayHelper::merge(
                            ['0' => Yii::t('app', 'Manufacturer')],
                            ArrayHelper::map($manufacturers, 'id', 'name')
                        ),
                        ['class' => 'custom-select', 'id' => 'main_manufacturer']
                    ); ?>
                </div>

                <div class="custom-select-wrap">
                    <?= Html::dropDownList(
                        'main_model',
                        $car ? $car->model_id : null,
                        ArrayHelper::merge(
                            ['0' => Yii::t('app', 'Model')],
                            $car
                                ? ArrayHelper::map(
                                    $car->manufacturer->getModels(true)->all(),
                                    'id',
                                    function($model) {
                                        /** @var Model $model */
                                        return $model->getFullName(false);
                                    }
                                )
                                : []
                        ),
                        ['class' => 'custom-select' . ($car ? '' : ' disabled'), 'id' => 'main_model']
                    ); ?>
                </div>

                <?php if ($car): ?>
                    <div id="main_photo"><?= Html::img(Car::getDefaultImage($car->getImage())); ?></div>
                <?php else: ?>
                    <div id="main_photo" style="display:none;"></div>
                <?php endif; ?>

                <div id="main_image_container" style="display: <?= $car ? 'block' : 'none' ?>;">
                    <button type="button" id="main_image" class="btn btn-default btn-noshadow" style="width: 190px; height: 34px;">
                        <?= IconHelper::show('upload') . Yii::t('app', 'Upload own image')?>
                    </button>
                </div>

                <div class="custom-select-wrap">
                    <?= Html::dropDownList(
                        'main_engine',
                        $car ? $car->engine_id : null,
                        ArrayHelper::merge(
                            ['0' => Yii::t('app', 'Engine')],
                            $car
                                ? ArrayHelper::map(
                                $car->model->getEngines(true)->all(),
                                'id',
                                function($model) {
                                    /** @var Engine $model */
                                    return $model->getName();
                                }
                            )
                                : []
                        ),
                        ['class' => 'custom-select' . ($car ? '' : ' disabled'), 'id' => 'main_engine']
                    ); ?>
                </div>

                <div class="custom-select-wrap">
                    <?= Html::dropDownList(
                        'main_time',
                        null,
                        ArrayHelper::merge(['0' => Yii::t('app', 'Comparison time')], Comparison::getComparisonTimes()),
                        ['class' => 'custom-select' . ($car ? '' : ' disabled'), 'id' => 'main_time']
                    ); ?>
                </div>

                <div class="swith-main-garage">
                    <?= SwitchInput::widget([
                        'name' => 'myGarage[]',
                        'tristate' => true,
                        'indeterminateToggle' => false,
                        'options' => [
                            'class' => 'switch-input',
                            'data-value' => 'main'
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
            </div>

            <div class="comparison-add-item-wrap">
                <div class="custom-select-wrap">
                    <?= Html::dropDownList(
                        'compare_manufacturer',
                        null,
                        ArrayHelper::merge(
                            ['0' => Yii::t('app', 'Manufacturer')],
                            ArrayHelper::map($manufacturers, 'id', 'name')
                        ),
                        ['class' => 'custom-select', 'id' => 'compare_manufacturer']
                    ); ?>
                </div>

                <div class="custom-select-wrap">
                    <?= Html::dropDownList(
                        'compare_model',
                        null,
                        ['0' => Yii::t('app', 'Model')],
                        ['class' => 'custom-select disabled', 'id' => 'compare_model']
                    ); ?>
                </div>

                <div id="compare_photo" style="display: none;"></div>

                <div id="compare_image_container" style="display: none;">
                    <button type="button" id="compare_image" class="btn btn-default btn-noshadow" style="width: 190px; height: 34px;">
                        <?= IconHelper::show('upload') . Yii::t('app', 'Upload own image')?>
                    </button>
                </div>

                <div class="custom-select-wrap">
                    <?= Html::dropDownList(
                        'compare_engine',
                        null,
                        ['0' => Yii::t('app', 'Engine')],
                        ['class' => 'custom-select disabled', 'id' => 'compare_engine']
                    ); ?>
                </div>

                <div class="custom-select-wrap">
                    <?= Html::dropDownList(
                        'compare_time',
                        null,
                        ArrayHelper::merge(['0' => Yii::t('app', 'Comparison time')], Comparison::getComparisonTimes()),
                        ['class' => 'custom-select disabled', 'id' => 'compare_time']
                    ); ?>
                </div>

                <div class="swith-compare-garage">
                    <?= SwitchInput::widget([
                        'name' => 'myGarage[]',
                        'tristate' => true,
                        'indeterminateToggle' => false,
                        'options' => [
                            'class' => 'switch-input',
                            'data-value' => 'compare'
                        ],
                        'pluginOptions'=>[
                            'indeterminate' => true,
                            'size' => 'large',
                            'handleWidth' => 250,
                            'labelWidth' => 50,
                            'onText' => Yii::t('app', 'In garage'),
                            'offText' => Yii::t('app', 'Drive before')
                        ],
                    ]); ?>
                    <div class="switch-input-clear"><i class="fa fa-times"></i> <?= Yii::t('app', 'Reset'); ?></div>
                </div>
            </div>

            <div class="clear"></div>
        </div>

        <div class="clear"></div>

        <p class="start-compare-wrap">
            <a href="#" class="btn btn-orange btn-lg text-bold" id="start_compare"><?= Yii::t('app', 'Compare'); ?></a>
        </p>


        <div class="no-auto-catalog">
            <p><?= Yii::t('app', 'No auto form activate'); ?></p>

            <div>
                <div class="flex-row">
                    <?= Html::activeInput('text', $carRequest, 'manufacturer', ['class' => 'form-control', 'placeholder' => $carRequest->getAttributeLabel('manufacturer')]); ?>

                    <?= Html::activeInput('text', $carRequest, 'model', ['class' => 'form-control', 'placeholder' => $carRequest->getAttributeLabel('model')]); ?>
                </div>

                <a href="#" class="fnSendCarRequest btn btn-default">Отправить</a>
            </div>
        </div>

        <div id="comparison_values" style="display: none;">
            <h2>Мои оценки</h2>

            <div class="comparison-add-vertical-line">
                <div class="comparison-add-item-wrap comparison-add-item-main">
                    <div class="comparison-add-item-main-name"></div>
                </div>

                <div class="comparison-add-item-wrap comparison-add-item-compare">
                    <div class="comparison-add-item-compare-name"></div>
                </div>

                <div class="clear"></div>

                <?php foreach ($criteria as $item): ?>
                    <h3 class="fn-criteria" data-id="<?= $item->id; ?>"><?= $item->name; ?></h3>

                    <div class="comparison-add-item-wrap">
                        <div class="text-center fn-point-main-<?= $item->id; ?>">
                            <span class="comparison-add-point-handler">1</span>
                            <span class="comparison-add-point-handler">2</span>
                            <span class="comparison-add-point-handler">3</span>
                            <span class="comparison-add-point-handler">4</span>
                            <span class="comparison-add-point-handler">5</span>
                            <span class="comparison-add-point-handler">6</span>
                            <span class="comparison-add-point-handler">7</span>
                            <span class="comparison-add-point-handler">8</span>
                            <span class="comparison-add-point-handler">9</span>
                            <span class="comparison-add-point-handler">10</span>
                        </div>
                    </div>

                    <div class="comparison-add-item-wrap">
                        <div class="text-center fn-point-compare-<?= $item->id; ?>">
                            <span class="comparison-add-point-handler">1</span>
                            <span class="comparison-add-point-handler">2</span>
                            <span class="comparison-add-point-handler">3</span>
                            <span class="comparison-add-point-handler">4</span>
                            <span class="comparison-add-point-handler">5</span>
                            <span class="comparison-add-point-handler">6</span>
                            <span class="comparison-add-point-handler">7</span>
                            <span class="comparison-add-point-handler">8</span>
                            <span class="comparison-add-point-handler">9</span>
                            <span class="comparison-add-point-handler">10</span>
                        </div>
                    </div>

                    <div class="clear"></div>

                    <div class="comparison-add-item-criteria-comment">
                        <textarea name="criteria_comment_<?= $item->id; ?>" class="lineage small-comment" placeholder="<?= $item->placeholder ? $item->placeholder : 'Краткий комментарий'; ?>" rows="1"></textarea>
                    </div>
                <?php endforeach; ?>
            </div>

            <h2>Резюме</h2>

            <div class="comparison-add-resume-wrap">
                <?= Html::textarea(
                    'criteria_resume',
                    null,
                    [
                        'id' => 'criteria_resume',
                        'class' => 'lineage comparison-add-resume-comment',
                        'placeholder' => Yii::t('app', 'Comparison resume comment placeholder')
                    ]
                );?>
            </div>

            <p class="text-center" style="margin: 0 0 20px; padding: 20px 0;">
                <a href="#" class="btn btn-orange btn-lg text-bold" id="btn_add_compare">Готово!</a>
            </p>
        </div>

        <div class="clear"></div>

    <?php endif; ?>

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