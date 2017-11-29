<?php
/* @var $this yii\web\View */
/* @var $model app\models\News|\mongosoft\file\UploadBehavior */
/* @var $form yii\widgets\ActiveForm */
/* @var $manufacturers \app\models\Manufacturer[] */
/* @var $newsModels array */

use app\components\ArrayHelper;
use app\components\IconHelper;
use app\components\StringHelper;
use app\components\UrlHelper;
use app\components\widgets\VideoInput;
use dosamigos\tinymce\TinyMce;
use kartik\file\FileInput;
use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

$initialPreview = $model->featured_image ? [$model->getFeaturedImage()] : [];
$initialCaption = $model->featured_image ? basename(Yii::getAlias('@webroot') . $model->featured_image) : '';

$initialPreviewConfig = $model->featured_image ? [
    ['caption' => $model->featured_image, 'size' => filesize($model->getFeaturedImage(true)), 'key' => $model->featured_image]
] : [];

$gallery = $model->gallery ? json_decode($model->gallery, true) : [];
$galleryInitialPreview = $galleryInitialPreviewConfig = [];

foreach ($gallery as $item)
{
    $galleryInitialPreview[] = $item;
    $galleryInitialPreviewConfig[] = ['caption' => basename(Yii::getAlias('@webroot') . $item), 'size' => filesize(Yii::getAlias('@webroot') . $item), 'key' => $item];
}

$newsModels = [];
foreach ($model->manufacturers as $item)
{
    $newsModels[] = [
        'manufacturer_id' => $item->id,
        'model_id' => null,
        'name' => $item->name
    ];
}
foreach ($model->models as $item)
{
    $newsModels[] = [
        'manufacturer_id' => null,
        'model_id' => $item->id,
        'name' => $item->getFullName()
    ];
}
?>

<div class="news-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data'
        ]
    ]); ?>

    <div class="row">
        <div class="col-md-9">
            <?= $form->field($model, 'title')->textInput(); ?>

            <div id="edit-slug-box" class="form-group field-url_title-title">
                <strong><?= $model->getAttributeLabel('url_title'); ?>: </strong>

                <span id="sample-permalink" tabindex="-1">
                    <?= UrlHelper::home(true); ?>news/<span id="editable-post-name"><?= StringHelper::ellipsize($model->getUrl(false), 30, .5); ?></span>
			    </span>

                <span id="edit-slug-buttons">
				    <button type="button" id="editPermalink" class="btn btn-sm btn-default"><?= Yii::t('app/admin', 'Change'); ?></button>
                    <button type="button" id="getPermalink" class="btn btn-sm btn-default"><?= Yii::t('app/admin', 'Get'); ?></button>
				    <button type="button" id="savePermalink" class="btn btn-sm btn-default"><?= Yii::t('app/admin', 'Save'); ?></button>
				    <button type="button" id="cancelPermalink" class="btn btn-sm btn-link"><?= Yii::t('app/admin', 'Cancel'); ?></button>
			    </span>

                <span id="editable-post-name-full"><?= $model->getUrl(false); ?></span>

                <?= $form->field($model, 'url_title')->label(false)->hiddenInput(); ?>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading"><?= Html::activeLabel($model, 'excerpt'); ?></div>

                <div class="panel-body">
                    <?= $form->field($model, 'excerpt')->label(false)->textarea(['rows' => 4]); ?>

                    <?= $form->field($model, 'include_excerpt')->checkbox(); ?>

                    <p class="text-muted"><?= Yii::t('app/admin', 'Include Excerpt Help'); ?></p>
                </div>
            </div>

            <?= $form->field($model, 'content')->widget(TinyMce::className(), [
                'options' => ['rows' => 16],
                'language' => 'ru',
                'clientOptions' => [
                    'plugins' => [
                        "advlist autolink lists link image charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "media table contextmenu paste"
                    ],
                    'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                    'automatic_uploads' => true,
                    'images_upload_url' => '',
                    'file_browser_callback' => new JsExpression('function(field, url, type, win) { admin.editorBrowserCallback(field, url, type, win); }'),
                    'file_picker_callback' => new JsExpression('function(cb, value, meta) { admin.editorPickerCallback(cb, value, meta); }')
                ]
            ]); ?>

            <div class="panel panel-default">
                <div class="panel-heading"><?= Html::activeLabel($model, 'gallery'); ?></div>

                <div class="panel-body">
                    <?php foreach ($gallery as $item): ?>
                        <?= Html::hiddenInput('gallery[]', $item); ?>
                    <?php endforeach; ?>

                    <?= FileInput::widget([
                        'name' => 'Upload[imageFile][]',
                        'options' => [
                            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'showClose' => false,
                            'showRemove' => false,
                            'previewFileType' => 'any',
                            'initialPreview' => $galleryInitialPreview,
                            'initialPreviewAsData' => true,
                            'initialCaption' => '',
                            'initialPreviewConfig' => $galleryInitialPreviewConfig,
                            'overwriteInitial' => false,
                            'maxFileSize' => 2800,
	                        'uploadAsync' => false,
                            'uploadUrl' => '/admin/news/upload-gallery',
                            'deleteUrl' => '/admin/news/delete-gallery-image?id=' . $model->id
                        ],
                        'pluginEvents' => [
                            'fileuploaded' => new JsExpression('function(event, data, previewId, index) { admin.news.galleryItemUploaded(data); }'),
                            'filebatchuploadsuccess' => new JsExpression('function(event, data, previewId, index) { admin.news.galleryUploaded(data); }'),
                            'filedeleted' => new JsExpression('function(event, key) { admin.news.galleryItemRemoved(key); }'),
                            'filesorted' => new JsExpression('function(event, params) { admin.news.gallerySorted(params); }')
                        ]
                    ]); ?>

                    <div class="alert alert-info" style="margin-top: 20px; margin-bottom: 0;">
                        <h4><i class="fa fa-exclamation-triangle"></i> Внимание!</h4>
                        <p>После добавления новых изображений <strong>ОБЯЗАТЕЛЬНО</strong> загрузите их, воспользовавшись кнопкой "Загрузка".</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="panel panel-default" style="margin-top: 25px;">
                <div class="panel-heading"><?= Html::activeLabel($model, 'created'); ?></div>

                <div class="panel-body">
                    <?= $form->field($model, 'created')->label(false)->widget(DatePicker::className(), [
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        'convertFormat' => false,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd'
                        ]
                    ]); ?>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading"><?= Html::activeLabel($model, 'featured_image'); ?></div>

                <div class="panel-body">
                    <?= $form->field($model, 'featured_image')->label(false)->widget(FileInput::className(), [
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
                            'deleteUrl' => '/admin/news/delete-featured-image/?id=' . $model->id,
                        ]
                    ]); ?>

                    <?= $form->field($model, 'featured_image')->label(false)->hiddenInput(); ?>

                    <?= $form->field($model, 'featured_image_caption')->textarea(['rows' => 4]) ?>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading"><?= Html::activeLabel($model, 'video'); ?></div>

                <div class="panel-body">
                    <?= $form->field($model, 'video')->label(false)->widget(VideoInput::className(), [
                            'showVideo' => true
                    ]); ?>

                    <p class="text-muted">Для вставки видео скопируйте ссылку со страницы видео-сервиса и вставьте ее сюда.</p>
                </div>
            </div>

            <?= $form->field($model, 'source')->textInput(['placeholder' => 'Ссылка должна начинаться с http://']) ?>

            <div class="panel panel-default">
                <div class="panel-heading">Выберите модель</div>

                <div class="panel-body">
                    <div class="loader-holder">
                        <?= Html::dropDownList(
                            'manufacturers_list',
                            null,
                            ArrayHelper::merge(
                                ['--- Выберите производителя ---'],
                                ArrayHelper::map($manufacturers, 'id', 'name')
                            ), ['class' => 'form-control news-manufacturers-list', 'id' => 'manufacturerAdding']
                        ); ?>
                    </div>

                    <?= Html::dropDownList(
                        'models_list',
                        null,
                        ['--- Выберите модель ---'],
                        ['class' => 'form-control news-models-list', 'id' => 'modelAdding']
                    ); ?>

                    <?= Html::button(
                        IconHelper::show('add') . Yii::t('app', 'Add'),
                        ['class' =>'btn btn-default btn-sm', 'id' => 'addNewsModel']
                    ); ?>

                    <div class="news-models">
                        <?php if ($newsModels): ?>
                            <?php foreach ($newsModels as $item): ?>
                                <?php $attr = $item['model_id'] ? 'data-model="' . $item['model_id'] . '"' : 'data-manufacturer="' . $item['manufacturer_id'] . '"'; ?>
                                <?php $type = $item['model_id'] ? 'model' : 'manufacturer'; ?>
                                <?php $value = $item['model_id'] ? $item['model_id'] : $item['manufacturer_id']; ?>

                                <?= Html::hiddenInput('news_models_ids[]', $value, ['data-type' => $type]); ?>
                                <?= Html::hiddenInput('news_models_types[]', $type, ['data-value' => $value]); ?>

                                <span class="news-model-item" <?= $attr; ?> data-type="<?= $type; ?>"><i class="fa fa-times-circle"></i><?= $item['name']; ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <span class="news-models-empty" style="display: <?= $newsModels ? 'none' : 'block'; ?>;">Не выбрано ни одной модели</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group fixed-bottom-toolbar">
        <?= Html::hiddenInput('isNew', $model->isNewRecord ? '1' : '0', ['id' => 'isNew']); ?>

        <?= Html::submitButton($model->isNewRecord ?
            IconHelper::show('add') . Yii::t('app', 'Create News') :
            IconHelper::show('save') . Yii::t('app', 'Save'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ); ?>

        <?= $model->isNewRecord
            ? ''
            : Html::a(
                '<i class="fa fa-trash"></i>Удалить',
                '#',
                [
                    'class' => 'btn btn-danger',
                    'data-confirmation' => Yii::t('app/admin', 'Are you sure to delete?'),
                    'data-callback' => '/admin/news/delete?id=' . $model->id
                ]) .
                ' ' . Html::a('<i class="fa fa-window-restore"></i>Открыть', '/news/' . $model->id, ['target' => '_blank', 'class' => 'btn btn-warning']);
        ?>

        <?= Html::a('<i class="fa fa-undo"></i>Отменить', Yii::$app->request->referrer, ['class' => 'btn btn-default']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>