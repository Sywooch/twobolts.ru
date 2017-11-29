<?php
/** @var yii\web\View $this */
/** @var \app\models\Comparison[] $comparisons */
/** @var \app\models\Comparison[] $favorites */
/** @var \app\models\UserCar[] $before */
/** @var \app\models\UserCar[] $garage */
/** @var int $comparisonsCount */
/** @var int $favoritesCount */
/** @var User $user */

use app\components\IconHelper;
use app\components\TimeZoneHelper;
use app\models\User;
use kartik\checkbox\CheckboxX;
use kartik\widgets\Select2;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\JsExpression;

$this->title = Yii::t('app', 'User') . ' ' . $user->username . ' — ' . Yii::$app->params['siteTitle'];

?>

<div class="profile-index inside height-wrapper">
    <div class="body-content">
        <h1><?= Yii::t('app', 'User profile'); ?></h1>

        <div class="my-profile-edit">
            <div class="profile-left-section">
                <div class="my_avatar">
                    <div class="current-avatar">
                        <?= Html::img(User::getDefaultAvatar($user->avatar), ['width' => 100]); ?>
                    </div>

                    <div id="avatar_container">
                        <button type="button" id="avatar_image" class="btn btn-default" ><?= IconHelper::show('upload') . ' ' . Yii::t('app', 'Upload'); ?></button>
                    </div>
                </div>

                <div class="my_info">
                    <p><i class="fa fa-user"></i> <?= $user->username; ?></p>

                    <p><i class="fa fa-envelope"></i> <?= $user->email ? $user->email : Yii::t('app', 'No Email'); ?></p>

                    <a href="#" class="edit_profile_link" id="edit_email" style="margin-bottom: 4px;"><?= IconHelper::show('edit'); ?>Сменить электронную почту</a>

                    <?php if ($user->password): ?>
                        <a href="#" class="edit_profile_link" id="edit_password"><?= IconHelper::show('password'); ?>Сменить пароль</a>
                    <?php endif; ?>

                    <?php if ($user->email): ?>
                        <p class="info-disclaimer">
                            <i class="fa fa-info-circle"></i><br>
                            Ваш адрес электронной почты виден только вам, и необходим только в случае восстановления пароля.
                        </p>

                        <?php if ($user->profile): ?>
                            <div class="profile-notification">
                                <?= CheckboxX::widget([
                                    'name' => 'profile_notification',
                                    'value' => $user->profile->notification,
                                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                                    'options' => [
                                        'id' => 'profile_notification'
                                    ],
                                    'pluginOptions' => [
                                        'threeState' => false
                                    ],
	                                'pluginEvents' => [
		                                'change' => new JsExpression('function() { User.updateProfileNotification(); }')
                                    ]
                                ]); ?>

                                <label class="cbx-label" for="profile_notification"><?= $user->profile->getAttributeLabel('notification'); ?></label>
                            </div>
                        <?php endif; ?>
                    <?php endif ?>
                </div>

                <div class="clear"></div>
            </div>

            <div class="profile-right-section">
                <div class="profile-social">
                    <?= $user->getSocialButton('vkontakte'); ?>

                    <?= $user->getSocialButton('facebook'); ?>

                    <?= $user->getSocialButton('twitter'); ?>

                    <?= $user->getSocialButton('google'); ?>

                    <p class="info-disclaimer">Здесь вы можете привязать свой профиль из соцсети, для того чтобы использовать его при авторизации на нашем сайте. Просто кликните на иконку сети и авторизируйте наше приложение.</p>
                </div>
            </div>

            <div class="clear"></div>
        </div>
    </div>

    <div class="clear"></div>

    <?= $this->render('_info', [
        'isOwn' => true,
        'user' => $user,
        'comparisons' => $comparisons,
        'comparisonsCount' => $comparisonsCount,
        'favorites' => $favorites,
        'favoritesCount' => $favoritesCount,
        'before' => $before,
        'garage' => $garage
    ]) ?>
</div>

<?php
Modal::begin([
    'id' => 'emailDlg',
    'header' => '<h4 class="modal-title">' . Yii::t('app', 'Edit Email'). '</h4>',
    'footer' => '<div class="btn-group">' .
        Html::button(Yii::t('app', 'Continue'), ['class' => 'btn btn-orange btn-update-email']) .
        Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-orange', 'data-dismiss' => 'modal']) .
        '</div>'
]);
?>

<?php if ($user->email): ?>
    <p><strong><i class="fa fa-exclamation-triangle"></i> Внимание!</strong></p>
    <p>На текущий адрес электронной почты будет выслано письмо с инструкцией для подключения нового адреса. Будьте внимательны при выполнении данной процедуры. Если у Вас возникнут вопросы, свяжитесь со службой поддержки. </p>
    <p>&nbsp;</p>
    <p><strong>Текущий адрес</strong>: <?= $user->email; ?></p>
    <?= Html::hiddenInput(
        'email',
        $user->email,
        [
            'id' => 'email'
        ]
    ); ?>
<?php else: ?>
    <p><strong><i class="fa fa-exclamation-triangle"></i> Внимание!</strong></p>
    <p>На указанный адрес электронной почты будет выслано письмо с инструкцией для подключения нового адреса. Будьте внимательны при выполнении данной процедуры. Если у Вас возникнут вопросы, свяжитесь со службой поддержки. </p>
    <p>&nbsp;</p>
    <?= Html::textInput(
        'email',
        null,
        [
            'id' => 'email',
            'class' => 'form-control',
            'placeholder' => Yii::t('app', 'Enter Email')
        ]
    ); ?>
<?php endif; ?>

<?php
Modal::end();

Modal::begin([
    'id' => 'passwordDlg',
    'header' => '<h4 class="modal-title">' . Yii::t('app', 'Edit Password'). '</h4>',
    'footer' => '<div class="btn-group">' .
        Html::button(Yii::t('app', 'Continue'), ['class' => 'btn btn-orange btn-update-password']) .
        Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-orange', 'data-dismiss' => 'modal']) .
        '</div>'
]);
?>

<p><strong><?= IconHelper::show('attention'); ?> <?= Yii::t('app', 'Attention')?></strong></p>
<p><?= Yii::t('app', 'Changing password requires a knowing of current password'); ?></p>
<p>&nbsp;</p>
<p class="text-muted"><?= IconHelper::show('info'); ?> <?= Yii::t('app', 'After successfully changing of password you need to re-authorize'); ?></p>
<p>&nbsp;</p>

<div class="form-group">
    <?= Html::label(Yii::t('app', 'Current Password'), 'password')?>
    <?= Html::passwordInput(
        'password',
        null,
        [
            'id' => 'password',
            'class' => 'form-control',
            'placeholder' => Yii::t('app', 'Enter Current Password')
        ]
    ); ?>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('app', 'New Password'), 'newPassword')?>
    <?= Html::passwordInput(
        'newPassword',
        null,
        [
            'id' => 'newPassword',
            'class' => 'form-control',
            'placeholder' => Yii::t('app', 'Enter New Password')
        ]
    ); ?>
</div>

    <div class="form-group">
        <?= Html::label(Yii::t('app', 'Confirm New Password'), 'confirmPassword')?>
        <?= Html::passwordInput(
            'confirmPassword',
            null,
            [
                'id' => 'confirmPassword',
                'class' => 'form-control',
                'placeholder' => Yii::t('app', 'Confirm New Password')
            ]
        ); ?>
    </div>

<?php
Modal::end();

Modal::begin([
    'id' => 'avatarCropperDlg',
    'header' => '<h4 class="modal-title">' . Yii::t('app', 'New Avatar Image'). '</h4>',
    'footer' => '<div class="btn-group">' .
        Html::button(Yii::t('app', 'Continue'), ['class' => 'btn btn-orange', 'data-crop' => 'getData', 'data-option' => '']) .
        Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-orange', 'data-dismiss' => 'modal']) .
        '</div>'
]);
?>

    <div class="img-container">
        <img src="" alt="" id="cropped_img">
    </div>

    <div class="docs-buttons">
        <!-- <h3 class="page-header">Toolbar:</h3> -->
        <div class="btn-group">
            <button class="btn btn-primary" data-crop="setDragMode" data-option="move" type="button" title="<?= Yii::t('app', 'Cropper Move Image'); ?>">
                <span class="fa fa-arrows"></span>
            </button>
            <button class="btn btn-primary" data-crop="setDragMode" data-option="crop" type="button" title="<?= Yii::t('app', 'Cropper Move Crop'); ?>">
                <span class="fa fa-crop"></span>
            </button>
            <button class="btn btn-primary" data-crop="zoom" data-option="0.1" type="button" title="<?= Yii::t('app', 'Cropper Zoom In'); ?>">
                <span class="fa fa-search-plus"></span>
            </button>
            <button class="btn btn-primary" data-crop="zoom" data-option="-0.1" type="button" title="<?= Yii::t('app', 'Cropper Zoom Out'); ?>">
                <span class="fa fa-search-minus"></span>
            </button>
        </div>
    </div><!-- /.docs-buttons -->
<?php
Modal::end();

Modal::begin([
    'id' => 'profileDlg',
    'header' => '<h4 class="modal-title">' . Yii::t('app', 'Edit Profile') . '</h4>',
    'footer' => '<div class="btn-group">' .
        Html::button(Yii::t('app', 'Continue'), ['class' => 'btn btn-orange btn-update-profile']) .
        Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-orange', 'data-dismiss' => 'modal']) .
        '</div>'
]);
?>

    <div class="form-group">
        <?= Html::label(Yii::t('app', 'Country'), 'country') ?>
        <?= Html::textInput(
            'country',
            $user->profile->country,
            [
                'id' => 'country',
                'class' => 'form-control',
                'placeholder' => Yii::t('app', 'Enter Country')
            ]
        ); ?>
    </div>

    <div class="form-group">
        <?= Html::label(Yii::t('app', 'City'), 'city') ?>
        <?= Html::textInput(
            'city',
            $user->profile->city,
            [
                'id' => 'city',
                'class' => 'form-control',
                'placeholder' => Yii::t('app', 'Enter City')
            ]
        ); ?>
    </div>

    <div class="form-group">
		<?= Html::label(Yii::t('app', 'Timezone'), 'timezone') ?>
		<?= Select2::widget([
            'name' => 'timezone',
			'value' => $user->timezone,
			'data' => TimeZoneHelper::timeZones(),
			'options' => [
				'id' => 'timezone',
			],
            'pluginOptions' => [
	            'allowClear' => false,
	            'language' => Yii::$app->language,
	            'placeholder' => Yii::t('app', 'Find a time zone...'),
            ]
		]); ?>
    </div>

    <div class="form-group">
        <?= Html::label(Yii::t('app', 'About self'), 'about') ?>
        <?= Html::textarea(
            'about',
            $user->profile->about,
            [
                'id' => 'about',
                'class' => 'form-control',
                'rows' => 6,
                'placeholder' => Yii::t('app', 'Enter about self')
            ]
        ); ?>
    </div>

<?php
Modal::end();

$this->registerCssFile('/js/jquery/cropper/dist/cropper.min.css');
$this->registerJsFile('/js/jquery/jquery.ocupload-1.1.2.packed.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('/js/jquery/cropper/dist/cropper.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>