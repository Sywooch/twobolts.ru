<?php
/** @var \yii\web\View $this */
/** @var string $title */
/** @var string $metaDescription */
/** @var \app\models\Manufacturer $manufacturer */
/** @var array $models */
/** @var string $sorting */

use app\models\Car;
use app\models\Model;
use yii\helpers\BaseUrl;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $title . ' — ' . Yii::$app->params['siteTitle'];
$this->registerMetaTag([
    'description' => $metaDescription
]);
?>

<div class="catalog-manufacturer inside height-wrapper">
    <h1><?= $title; ?></h1>

    <div class="catalog-sort" data-get="catalog" data-manufacturer="<?= $manufacturer->id; ?>">
        <a href="#" class="<?= $sorting == 'name' ? 'active' : ''; ?>" data-sorting="name"><?= Yii::t('app', 'Alphabetical'); ?></a>
        <a href="#" class="<?= $sorting == 'rating' ? 'active' : ''; ?>" data-sorting="rating"><?= Yii::t('app', 'By rating')?></a>
    </div>

    <div class="catalog-models">
        <div class="catalog-models-wrapper">
        <?php
        if ($manufacturer) {
            echo $this->render('manufacturer_list', ['models' => $models]);
        } else {
            Html::tag('h3', Yii::t('app', 'Empty catalog'));
        }
        ?>
        </div>

        <div class="clear"></div>
    </div>

    <div class="clear"></div>
</div>
