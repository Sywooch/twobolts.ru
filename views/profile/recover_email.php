<?php
/** @var yii\web\View $this */
/** @var User $user */

use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('app', 'User') . ' ' . $user->username . ' — ' . Yii::$app->params['siteTitle'];
$user->password = '';

?>

<div class="profile-index inside height-wrapper">
    <div class="body-content">
        <h1><?= Yii::t('app', 'Recover email'); ?></h1>

        <div class="row">
            <div class="col-md-6">
                <?php $form = ActiveForm::begin([
                    'id' => 'recover-email-form',
                    'action' => '/profile/recover-email?hash='.$user->new_email_key,
                    'options' => ['name' => 'recover-email-form'],
                ]); ?>

                <?= $form->field($user, 'new_email_key')->label(false)->hiddenInput(); ?>

                <?= $form->field($user, 'password')->passwordInput(); ?>

                <?= $form->field($user, 'email')->textInput(); ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary btn-lg', 'name' => 'recover-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
