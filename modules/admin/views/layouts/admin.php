<?php

/* @var $this \yii\web\View */
/* @var $context \app\modules\admin\controllers\DefaultController */
/* @var $content string */

use app\components\IconHelper;
use app\components\UrlHelper;
use app\models\CarRequest;
use app\modules\admin\assets\AdminAsset;
use yii\bootstrap\Modal;
use yii\helpers\BaseUrl;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

AdminAsset::register($this);

$context = $this->context;

$checkController = function ($route) {
    if (is_array($route)) {
        $result = false;
        foreach ($route as $url) {
            if ($url === $this->context->getUniqueId()) {
                $result = true;
            }
        }
        return $result;
    } else {
        return $route === $this->context->getUniqueId();
    }
};

$totalComparisons = 0; //Comparison::find()->count();
$totalCars = 0; //Car::find()->count();

$messageFile = Yii::getAlias('@app/messages') . '/' . Yii::$app->language . '/admin.php';
$messages = include($messageFile);
if (!is_array($messages)) {
    $messages = [];
}

/**
 * Необработанные запросы на новые машины
 */
$carRequests = CarRequest::find()->where(['status' => false])->count();

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <link rel="icon" type="image/png" href="<?= UrlHelper::absolute('favicon.png'); ?>" />
    <script type="text/javascript">
        var baseURL = '<?= BaseUrl::home(true); ?>',
            localizationMessages = <?= json_encode($messages)?>;
    </script>

    <?php $this->head() ?>
</head>
<body class="page-<?= $context->id .'-'.$context->action->id; ?>">

<?php $this->beginBody() ?>

<div class="container admin">

    <header>
        <?= Html::a(IconHelper::show('home') . Yii::t('app/admin', 'Go site'), UrlHelper::absolute('/'), ['class' => 'btn btn-default']); ?>

        <?= Html::a(IconHelper::show('setting') . Yii::t('app/admin', 'Go admin'), UrlHelper::absolute('admin'), ['class' => 'btn btn-default']); ?>

        <?= $carRequests ? Html::a('Запрос в каталог <span class="badge">' . $carRequests . '</span>', UrlHelper::absolute('admin/catalog/requests'), ['class' => 'btn btn-danger']) : ''; ?>

        <?= Html::a(IconHelper::show('exit') . Yii::t('app', 'Exit'), UrlHelper::absolute('admin/sign-out'), ['class' => 'btn btn-default pull-right']); ?>
    </header>

    <nav>
        <?= Html::a(IconHelper::show('setting') . Yii::t('app/admin', 'Settings'), UrlHelper::absolute('admin/settings'), ['class' => $checkController('admin/settings') ? 'active' : '']); ?>

        <?= Html::a(IconHelper::show('industry') . Yii::t('app/admin', 'Manufacturers'), UrlHelper::absolute('admin/manufacturer'), ['class' => $checkController('admin/manufacturer') ? 'active' : '']); ?>

        <?= Html::a(IconHelper::show('dashboard') . Yii::t('app/admin', 'Models'), UrlHelper::absolute('admin/model'), ['class' => $checkController('admin/model') ? 'active' : '']); ?>

        <?= Html::a(IconHelper::show('cubes') . Yii::t('app/admin', 'Bodies'), UrlHelper::absolute('admin/body'), ['class' => $checkController('admin/body') ? 'active' : '']); ?>

        <?= Html::a(IconHelper::show('flask') . Yii::t('app/admin', 'Engines'), UrlHelper::absolute('admin/engine'), ['class' => $checkController('admin/engine') ? 'active' : '']); ?>

        <?= Html::a(IconHelper::show('car') . Yii::t('app/admin', 'Catalog'), UrlHelper::absolute('admin/car'), ['class' => $checkController('admin/car') ? 'active' : '']); ?>

        <?= Html::a(IconHelper::show('comparison') . Yii::t('app/admin', 'Comparisons'), UrlHelper::absolute('admin/comparison'), ['class' => $checkController('admin/comparison') ? 'active' : '']); ?>

        <?= Html::a(IconHelper::show('news') . Yii::t('app/admin', 'News'), UrlHelper::absolute('admin/news'), ['class' => $checkController('admin/news') ? 'active' : '']); ?>

        <?= Html::a(IconHelper::show('comments') . Yii::t('app/admin', 'Comments'), UrlHelper::absolute('admin/comment'), ['class' => $checkController('admin/comment') ? 'active' : '']); ?>

        <?= Html::a(IconHelper::show('user') . Yii::t('app/admin', 'Users'), UrlHelper::absolute('admin/user'), ['class' => $checkController('admin/user') ? 'active' : '']); ?>
    </nav>

    <div class="admin-container">
        <?= Breadcrumbs::widget([
            'homeLink' => [
                'label' => Yii::t('app/admin', 'Go admin'),
                'url' => UrlHelper::absolute('admin')
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>

	    <?php if (Yii::$app->session->getAllFlashes()): ?>
            <div class="alert alert-dismissible flash-message-wrapper alert-summary default-summary" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			    <?php foreach (Yii::$app->session->getAllFlashes() as $key => $value): ?>
                    <div class="flash-message-item">
                        <h4><?= Yii::t('app', $key); ?></h4>
					    <?= $value; ?>
                    </div>
			    <?php endforeach; ?>
            </div>
	    <?php endif; ?>

        <?= $content ?>
    </div>
</div>

<?php Modal::begin([
    'id' => 'messageDlg',
    'header' => '<h4 class="modal-title"></h4>',
    'footer' => '<div class="btn-group">' .
        Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) .
        '</div>'
]); ?>

<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'promptDlg',
    'size' => 'modal-md',
    'header' => '<h4 class="modal-title">' . Yii::t('app/admin', 'Please confirm action') . '</h4>',
    'footer' => '<div class="modal-btn-group">' .
        Html::button('<i class="fa fa-check"></i>' . Yii::t('app/admin', 'Confirm'), ['class' => 'btn btn-success btn-modal-confirm']) .
        Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) .
        '</div>'
]); ?>

<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'formDlg',
    'size' => 'modal-md',
    'header' => '<h3 class="modal-title"></h3>'
]); ?>

<div class="form-container"></div>

<?php Modal::end(); ?>

<iframe name="form_frame" style="display: none;"></iframe>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
