<?php
/** @var User $user */

use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Recover password') . ' — ' . Yii::$app->params['siteTitle'];
?>

<div class="inside height-wrapper">
    <div class="body-content">
        <h1><?= Yii::t('app', 'Recover password'); ?></h1>

        <?php if ($user): ?>
            <div class="row">
                <div class="col-md-6">
                    <?php $form = ActiveForm::begin([
                        'id' => 'recover-form',
                        'action' => '/recover-password?hash='.$user->new_password_key,
                        'options' => ['name' => 'recover-form'],
                    ]); ?>

                    <?= $form->field($user, 'new_password_key')->label(false)->hiddenInput(); ?>

                    <?= $form->field($user, 'password')->passwordInput(); ?>

                    <?= $form->field($user, 'confirmPassword')->passwordInput(); ?>

                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Recover'), ['class' => 'btn btn-primary btn-lg', 'name' => 'recover-button']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger"><?= Yii::t('app', 'Password reset key expired'); ?></div>
        <?php endif; ?>
    </div>
</div>

