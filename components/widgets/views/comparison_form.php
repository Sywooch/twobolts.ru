<?php

/* @var \app\components\widgets\ComparisonForm $comparison */
/* @var \yii\web\View $this */
/* @var app\models\CarRequest $carRequest */

use app\components\ArrayHelper;
use app\components\widgets\ComparisonForm;
use yii\helpers\Html;

?>

<div class="comparison-add-vertical-line">
	<div class="comparison-add-item-wrap">
		<?= $this->render('comparison_form_dropdown', [
            'comparison' => $comparison,
            'type' => ComparisonForm::TYPE_MAIN
        ]); ?>
	</div>

    <div class="comparison-add-item-wrap">
		<?= $this->render('comparison_form_dropdown', [
			'comparison' => $comparison,
			'type' => ComparisonForm::TYPE_COMPARE
		]); ?>
    </div>

    <div class="clear"></div>
</div>

<p class="start-compare-wrap">
    <a href="#" class="btn btn-orange btn-lg text-bold" id="start_compare"><?= Yii::t('app', 'Compare'); ?></a>
</p>

<div class="no-auto-catalog">
    <p><?= Yii::t('app', 'No auto form activate'); ?></p>

    <div style="display: <?= $comparison->carRequest->hasData() ? 'block' : 'none'; ?>;">
        <div class="flex-row">
			<?= Html::activeInput('text', $comparison->carRequest, 'manufacturer', [
                'class' => 'form-control', 'placeholder' => $comparison->carRequest->getAttributeLabel('manufacturer')
            ]); ?>

			<?= Html::activeInput('text', $comparison->carRequest, 'model', [
                'class' => 'form-control', 'placeholder' => $comparison->carRequest->getAttributeLabel('model')
            ]); ?>
        </div>

        <a href="#" class="fnSendCarRequest btn btn-default">Отправить</a>
    </div>
</div>

<div id="comparison_values" style="display: <?= $comparison->isComparable() ? 'block' : 'none'; ?>;">
    <h2>Мои оценки</h2>

    <div class="comparison-add-vertical-line">
        <div class="comparison-add-item-wrap comparison-add-item-main">
            <div class="comparison-add-item-main-name"><?= $comparison->carTitle(ComparisonForm::TYPE_MAIN); ?></div>
        </div>

        <div class="comparison-add-item-wrap comparison-add-item-compare">
            <div class="comparison-add-item-compare-name"><?= $comparison->carTitle(ComparisonForm::TYPE_COMPARE); ?></div>
        </div>

        <div class="clear"></div>

	    <?php foreach ($comparison->criteria as $item): ?>
            <h3 class="fn-criteria" data-id="<?= $item->id; ?>"><?= $item->name; ?></h3>

            <?= $this->render('comparison_form_criteria', [
                'comparison' => $comparison,
                'type' => ComparisonForm::TYPE_MAIN,
                'item' => $item
            ]); ?>

		    <?= $this->render('comparison_form_criteria', [
			    'comparison' => $comparison,
			    'type' => ComparisonForm::TYPE_COMPARE,
			    'item' => $item
		    ]); ?>

            <div class="clear"></div>

            <div class="comparison-add-item-criteria-comment">
			    <?= Html::textarea(
                    'criteria_comment_' . $item->id,
                    $comparison->activeCriteriaComment($item),
                    [
                        'class' => 'lineage small-comment',
                        'placeholder' => $item->placeholder ? $item->placeholder : 'Краткий комментарий',
                        'rows' => 1
			        ]
                ); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Резюме</h2>

    <div class="comparison-add-resume-wrap">
		<?= Html::textarea(
			'criteria_resume',
			ArrayHelper::getValue($comparison->requestData, 'comment'),
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