<?php
/* @var $this yii\web\View */
/* @var $model app\models\Model|\app\components\behaviors\UploadBehavior */
/* @var $form yii\widgets\ActiveForm */

use app\components\ArrayHelper;
use app\components\IconHelper;
use app\components\ImageHelper;
use app\models\Body;
use app\models\Manufacturer;
use kartik\widgets\FileInput;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$initialPreview = $model->image ? [$model->getImage()] : [];
$initialCaption = $model->image ? basename(Yii::getAlias('@webroot') . $model->image) : '';

$initialPreviewConfig = $model->image ? [
    ['caption' => $model->image, 'size' => filesize(ImageHelper::getImageFile($model, 'image', true)), 'key' => $model->image]
] : [];

?>

<div class="model-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data'
        ]
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-dashboard"></i> <?= Yii::t('app/admin', $model->isNewRecord ? Yii::t('app/admin', 'Create Model') : Yii::t('app/admin', 'Update Model')); ?>
                </div>

                <div class="panel-body">
                    <?= $form->field($model, 'manufacturer_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(Manufacturer::filterData(), 'id', 'name'),
                        'options' => [
                            'class' => 'model-source'
                        ]
                    ]); ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class' => 'model-source form-control']); ?>

                    <?= $form->field($model, 'body_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(Body::filterData(), 'body_id', 'body_name'),
                        'options' => [
                            'class' => 'model-source'
                        ]
                    ]); ?>

                    <?= $form->field($model, 'url_title', [
                        'template' => '{label}<div class="input-group">{input}<span class="input-group-btn">' .
                            '<span class="btn btn-default get-url-link" data-source=".model-source" data-target=".model-target">' .
                            '<i class="fa fa-link"></i>Сформировать...' .
                            '</span>' .
                            '</span></div>{hint}{error}'
                    ])->textInput(['class' => 'model-target form-control']); ?>

                    <?= $form->field($model, 'is_popular')->checkbox(); ?>

                    <?= Html::activeLabel($model, 'image'); ?>

                    <?= $form->field($model, 'image')->label(false)->widget(FileInput::className(), [
                        'options' => [
                            'multiple' => false,
                            'accept' => 'image/*'
                        ],
                        'pluginOptions' => [
                            'showClose' => false,
                            'showUpload' => false,
                            'showRemove' => false,
                            'initialPreview' => $initialPreview,
                            'initialPreviewAsData' => true,
                            'initialCaption' => $initialCaption,
                            'initialPreviewConfig' => $initialPreviewConfig,
                            'initialPreviewShowDelete' => true,
                            'overwriteInitial' => true,
                            'maxFileSize' => 2800,
                            'deleteUrl' => '/admin/model/delete-image/?id=' . $model->id,
                        ]
                    ]); ?>

                    <?= $form->field($model, 'image')->label(false)->hiddenInput(); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group fixed-bottom-toolbar">
        <?= Html::hiddenInput('isNew', $model->isNewRecord ? '1' : '0', ['id' => 'isNew']); ?>

        <?= Html::submitButton($model->isNewRecord ?
            IconHelper::show('add') . Yii::t('app/admin', 'Create Model') :
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
                    'data-callback' => '/admin/model/delete?id=' . $model->id
                ]);
        ?>

        <?= Html::a('<i class="fa fa-undo"></i>' . Yii::t('app/admin', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-default']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>