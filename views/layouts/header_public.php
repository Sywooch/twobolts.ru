<?php
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="login-link">
    <a href="#" id="authOpen">Вход на сайт</a>

    <span style="margin-right:10px;">или авторизуйтесь одной из сетей</span>
    <span class="login-icon-wrapper login-icon-vk">
        <i class="loginVK social-login fa fa-vk"></i>
    </span>
    <span class="login-icon-wrapper login-icon-facebook">
        <i class="loginFB social-login fa fa-facebook"></i>
    </span>
    <span class="login-icon-wrapper login-icon-twitter">
        <i class="loginTW social-login fa fa-twitter"></i>
    </span>
    <span class="login-icon-wrapper login-icon-google">
        <i class="loginGoogle social-login fa fa-google"></i>
    </span>
</div>

<?php
Modal::begin([
    'id' => 'authDlg',
    'header' => '<h4 class="modal-title">' . Yii::t('app', 'Authorization'). '</h4>',
    'footer' => '<div class="signinBtnGroup btn-group">' .
        Html::button(Yii::t('app', 'Sign In'), ['class' => 'btn btn-orange btnSigin']) .
        Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-orange', 'data-dismiss' => 'modal']) .
        '</div>' .
        '<div class="resetBtnGroup btn-group">' .
        Html::button(Yii::t('app', 'Recover'), ['class' => 'btn btn-orange btnRecover']) .
        Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-orange', 'data-dismiss' => 'modal']) .
        '</div>' .
        '<div class="signupBtnGroup btn-group">' .
        Html::button(Yii::t('app', 'Sign Up'), ['class' => 'btn btn-orange btnSignup']) .
        Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-orange', 'data-dismiss' => 'modal']) .
        '</div>'
]);
?>

    <div class="auth-sign-form">
        <?= Html::textInput(
            'signin_login',
            null,
            [
                'id' => 'signin_login',
                'class' => 'form-control',
                'placeholder' => Yii::t('app', 'Enter Username')
            ]
        ); ?>
        <p class="fnErrorMsg">Логин должен быть не меньше 3 символов.</p>

        <?= Html::passwordInput(
            'signin_password',
            null,
            [
                'id' => 'signin_password',
                'class' => 'form-control',
                'placeholder' => Yii::t('app', 'Enter Password')
            ]
        ); ?>
        <p class="fnErrorMsg">Пароль должен быть не меньше 4 символов.</p>

        <div class="checkbox">
            <label for='signin_remeber'>
                <?= Html::checkbox('signin_remeber', false, ['id' => 'signin_remeber', 'value' => 1]); ?>
                Запомнить меня
            </label>
        </div>

        <span id="signin_lost_pass" class="fnAuthAction">Забыли пароль?</span> | <span id="signin_register" class="fnAuthAction">Регистрация</span>

        <div class="clear"></div>
    </div>

    <div class="auth-register-form" style="display:none;">
        <?= Html::textInput(
            'register_login',
            null,
            [
                'id' => 'register_login',
                'class' => 'form-control',
                'placeholder' => Yii::t('app', 'Enter Username')
            ]
        ); ?>
        <p class="fnErrorMsg">Логин должен быть не меньше 3 символов.</p>

        <?= Html::textInput(
            'register_email',
            null,
            [
                'id' => 'register_email',
                'class' => 'form-control',
                'placeholder' => Yii::t('app', 'Enter Email')
            ]
        ); ?>
        <p class="fnErrorMsg">Неверная электронная почта.</p>

        <?= Html::passwordInput(
            'register_password',
            null,
            [
                'id' => 'register_password',
                'class' => 'form-control',
                'placeholder' => Yii::t('app', 'Enter Password')
            ]
        ); ?>
        <p class="fnErrorMsg">Пароль должен быть не меньше 4 символов.</p>

        <?= Html::passwordInput(
            'register_confirm_password',
            null,
            [
                'id' => 'register_confirm_password',
                'class' => 'form-control',
                'placeholder' => Yii::t('app', 'Confirm Password')
            ]
        ); ?>
        <p class="fnErrorMsg">Пароли не совпадают.</p>

        <span id="register_signin" class="fnAuthAction">Войти на сайт</span> | <span id="register_lost_pass" class="fnAuthAction">Забыли пароль?</span>

        <div class="clear"></div>
    </div>

    <div class="auth-reset-form" style="display:none;">
        <h4>Введите адрес электронной почты, который указан в Вашем личном профиле. На данный адрес будет выслана инструкция по восстановлению пароля.</h4>

        <?= Html::textInput(
            'lost_pass_email',
            null,
            [
                'id' => 'lost_pass_email',
                'class' => 'form-control',
                'placeholder' => Yii::t('app', 'Enter Email')
            ]
        ); ?>
        <p class="fnErrorMsg">Неправильный адрес электронной почты.</p>

        <div style="margin: 10px 0;"></div>
        
        <span id="lost_pass_signin" class="fnAuthAction">Войти на сайт</span> | <span id="lost_pass_register" class="fnAuthAction">Регистрация</span>

        <div class="clear"></div>
    </div>

    <p style="margin: 15px 0 5px;">Или авторизуйтесь с помощью одной из социальных сетей</p>

    <div id="login_social_buttons">
        <span class="login-icon-wrapper login-icon-vk">
            <i class="loginVK social-login fa fa-vk"></i>
        </span>
        <span class="login-icon-wrapper login-icon-facebook">
            <i class="loginFB social-login fa fa-facebook"></i>
        </span>
        <span class="login-icon-wrapper login-icon-twitter">
            <i class="loginTW social-login fa fa-twitter"></i>
        </span>
        <span class="login-icon-wrapper login-icon-google">
            <i class="loginGoogle social-login fa fa-google"></i>
        </span>
    </div>

<?php
Modal::end();
?>