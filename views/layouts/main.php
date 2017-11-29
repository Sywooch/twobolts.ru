<?php

/* @var $this \yii\web\View */
/* @var $context \app\controllers\BaseController */
/* @var $content string */

use app\components\UrlHelper;
use app\models\Car;
use app\models\Comment;
use yii\bootstrap\Modal;
use yii\helpers\BaseUrl;
use yii\helpers\Html;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);

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

$messageFile = Yii::getAlias('@app/messages') . '/' . Yii::$app->language . '/app.php';
$messages = include($messageFile);;
if (!is_array($messages)) {
    $messages = [];
}
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
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-77906508-1', 'auto');
        ga('send', 'pageview');

        var baseURL = '<?= BaseUrl::home(true); ?>',
            vkAuthUrl = '<?= $context->getSocialAuthUrl('vkontakte'); ?>',
            fbAuthUrl = '<?= $context->getSocialAuthUrl('facebook'); ?>',
            twAuthUrl = '<?= $context->getSocialAuthUrl('twitter'); ?>',
            googleAuthUrl = '<?= $context->getSocialAuthUrl('google'); ?>',

            defaultFoto = '<?= Car::getDefaultImage(); ?>',
            returnUrl = '<?= Yii::$app->getUser()->getReturnUrl(); ?>',
            commentsPerPage = <?= Comment::COMMENTS_PER_PAGE; ?>;
            isGuest = <?= Yii::$app->user->isGuest ? 'true' : 'false'; ?>;
            localizationMessages = <?= json_encode($messages)?>;
    </script>

    <?php $this->head() ?>
</head>
<body class="page-<?= $context->id .'-'.$context->action->id; ?>">
<?php $this->beginBody() ?>

<?php if (Yii::$app->session->getAllFlashes()): ?>
    <div class="alert alert-dismissible flash-message-wrapper" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php foreach (Yii::$app->session->getAllFlashes() as $key => $value): ?>
            <div class="flash-message-item">
                <h4><?= Yii::t('app', $key); ?></h4>
                <?= $value; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="container">
    <header class="header">
        <div class="logo">
            <p><?= Html::a(
                Html::img(UrlHelper::absolute('images/logo.png'), ['alt' => 'twobolts.ru']),
                UrlHelper::absolute()
                ); ?></p>
        </div>

        <div class="login-nav-menu">
            <div class="login-panel <?= !Yii::$app->user->isGuest ? 'authorized' : ''; ?>">
                <?= Yii::$app->user->isGuest ? $this->render('header_public') : $this->render('header_private'); ?>

                <div class="add_compare_btn">
                    <?= Html::a('+ <span>Сравни авто</span>', '/comparison/add'); ?>
                </div>
                <div class="clear"></div>
            </div>
            <div class="menu">
                <ul>
                    <li>
                        <a href="<?= BaseUrl::home(true) . 'comparison'; ?>" class="<?= $checkController('comparison') ? 'current-menu' : ''; ?>">
                            <?= Yii::t('app', 'Compares') . ($totalComparisons > 0 ? ' <sup>'.$totalComparisons.'</sup>' : ''); ?>
                        </a>
                    </li>

                    <li>
                        <a href="<?=BaseUrl::home(true) . 'catalog'; ?>" class="<?= $checkController('catalog') ? 'current-menu' : ''; ?>">
                            <?= Yii::t('app', 'Catalog') . ($totalCars > 0 ? ' <sup>'.$totalCars.'</sup>' : ''); ?>
                        </a>
                    </li>

                    <li>
                        <a href="<?= BaseUrl::home(true) . 'news'; ?>" class="<?= $checkController('news') ? 'current-menu' : ''; ?>">
                            <?= Yii::t('app', 'Auto News'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="clear"></div>

    </header> <!-- .header -->

    <?= $content ?>

    <div class="obo" style="text-align:center">
        <div class="likely likely-light likely-small">
            <div class="facebook">Поделиться</div>
            <div class="gplus">Плюсануть</div>
            <div class="vkontakte">Поделиться</div>
            <div class="twitter" data-via="twobolts_ru">Твитнуть</div>
        </div>

        <div class="clear"></div>
    </div>

    <footer class="footer">
        <p>© <?= date('Y') . ' ' . Yii::$app->params['siteTitle']; ?> </p>
    </footer>
</div>

<?php
Modal::begin([
    'id' => 'messageDlg',
    'header' => '<h4 class="modal-title"></h4>',
    'footer' => '<div class="btn-group">' .
        Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-orange', 'data-dismiss' => 'modal']) .
        '</div>'
]);
?>

<?php Modal::end(); ?>

<?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>
