<?php

/* @var \app\components\widgets\ComparisonForm $comparison */
/* @var \app\models\ComparisonCriteria $item */
/* @var string $type */

?>

<div class="comparison-add-item-wrap">
	<div class="text-center fn-point-<?= $type; ?>-<?= $item->id; ?>">
		<?php $activePoint = $comparison->activeCriteriaPoint($item, $type); ?>

		<?php for ($i = 1; $i <= 10; ++$i): ?>
			<span class="comparison-add-point-handler <?= $activePoint == $i ? 'active' : ''; ?>"><?= $i; ?></span>
		<?php endfor; ?>
	</div>
</div>
