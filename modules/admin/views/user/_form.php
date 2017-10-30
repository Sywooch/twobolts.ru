<?php

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $profile \app\models\UserProfile */
/* @var $form yii\widgets\ActiveForm */

use app\components\ArrayHelper;
use app\components\IconHelper;
use app\components\TimeZoneHelper;
use app\models\Country;
use app\models\User;
use kartik\checkbox\CheckboxX;
use kartik\widgets\FileInput;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$initialPreview = $model->avatar ? [User::getDefaultAvatar($model->avatar)] : [];
$initialCaption = $model->avatar ? $model->avatar : '';

$initialPreviewConfig = $model->avatar ? [
	['caption' => $model->avatar, 'size' => filesize(Yii::getAlias('@webroot') . '/uploads/avatars/' . $model->avatar), 'key' => $model->avatar]
] : [];


?>

<div class="user-form">

	<?php $form = ActiveForm::begin([
		'options' => ['autocomplete' => 'off']
	]); ?>

	<div class="row">
		<div class="col-md-9">
			<div class="panel panel-default">
				<div class="panel-heading"><strong><?= Yii::t('app/admin', 'Registration Info'); ?></strong></div>

				<div class="panel-body">
					<div class="row">
						<div class="col-md-6">
							<?= $form->field($model, 'username', [
								'template' => '{label}<div class="input-group">{input}<span class="input-group-btn">' .
								              '<button class="btn btn-warning btn-test-username" type="button">' .
								              '<i class="fa fa-crosshairs"></i>' .
								              Yii::t('app/admin', 'Test username') . '</button></span></div>{error}{hint}'
							])->textInput([
								'maxlength' => true,
								'autocomplete' => 'off'
							]); ?>

							<div class="clearfix"></div>
						</div>

						<div class="clearfix"></div>

						<div class="col-md-6">
							<?= $form->field($model, 'newPassword')->textInput([
								'maxlength' => true,
								'autocomplete' => 'new-password'
							]); ?>

                            <div class="form-group">
                                <button class="btn btn-default btn-random-password" type="button" role="button">
                                    <i class="fa fa-asterisk"></i><?= Yii::t('app/admin', 'Generate password')?>
                                </button>
                            </div>

                            <div class="form-group">
                                <?= $form->field($model, 'sendNewPassword')->label(false)->widget(CheckboxX::className(), [
	                                'autoLabel' => true,
	                                'labelSettings' => [
		                                'label' => Yii::t('app/admin', 'Send new password'),
		                                'position' => CheckboxX::LABEL_RIGHT
	                                ],
	                                'pluginOptions' => [
		                                'threeState' => false
	                                ]
                                ]); ?>
                            </div>
						</div>

                        <div class="col-md-6">
	                        <?= $form->field($model, 'email', [
		                        'template' => '{label}<div class="input-group">{input}<span class="input-group-btn">' .
		                                      '<button class="btn btn-default btn-test-email" type="button">' .
		                                      '<i class="fa fa-crosshairs"></i>' .
		                                      Yii::t('app/admin', 'Test email') . '</button></span></div>{error}{hint}'
	                        ])->textInput(['maxlength' => true]); ?>
                        </div>
					</div>
				</div>
			</div>

            <div class="panel panel-default">
                <div class="panel-heading"><strong><?= Yii::t('app/admin', 'Profile Info'); ?></strong></div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <?= $form->field($profile, 'first_name')->textInput(); ?>

                            <?= $form->field($profile, 'country')->widget(Select2::className(), [
                                'data' => ArrayHelper::map(Country::filterData(), 'name', 'name'),
	                            'pluginOptions' => [
		                            'allowClear' => true,
		                            'language' => Yii::$app->language,
		                            'placeholder' => Yii::t('app', 'Find a country...'),
	                            ]
                            ]); ?>

                            <div></div>
                        </div>

                        <div class="col-lg-6">
	                        <?= $form->field($profile, 'last_name')->textInput(); ?>

	                        <?= $form->field($model, 'timezone')->widget(Select2::className(), [
		                        'data' => TimeZoneHelper::timeZones(),
		                        'pluginOptions' => [
			                        'allowClear' => true,
			                        'language' => Yii::$app->language,
			                        'placeholder' => Yii::t('app', 'Find a time zone...'),
		                        ]
	                        ]); ?>
                        </div>
                    </div>

                    <div><?= Html::activeLabel($model, 'avatar'); ?></div>

	                <?= $form->field($model, 'avatar')->label(false)->widget(FileInput::className(), [
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
			                'deleteUrl' => '/admin/user/delete-avatar/?id=' . $model->id,
		                ]
	                ]); ?>

	                <?= $form->field($model, 'avatar')->label(false)->hiddenInput(); ?>

                    <div class="row">
                        <div class="col-lg-6">
	                        <?= $form->field($model, 'karma')->textInput() ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default panel-social">
                <div class="panel-heading"><strong><?= Yii::t('app/admin', 'Social Networks'); ?></strong></div>

                <div class="panel-body">
                    <i class="fa fa-vk fa-fw <?= $model->vkontakte_id? 'active' : ''; ?>"></i>

                    <i class="fa fa-facebook fa-fw <?= $model->facebook_id? 'active' : ''; ?>"></i>

                    <i class="fa fa-twitter fa-fw <?= $model->twitter_id? 'active' : ''; ?>"></i>

                    <i class="fa fa-google fa-fw <?= $model->google_id? 'active' : ''; ?>"></i>
                </div>
            </div>
		</div>

        <div class="col-lg-3">
            <div class="panel panel-default">
                <div class="panel-heading"><?= Html::activeLabel($model, 'role'); ?></div>

                <div class="panel-body">
	                <?= $form->field($model, 'role')->label(false)->widget(Select2::className(), [
                        'data' => User::getRoles()
                    ]); ?>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading"><strong><?= Yii::t('app/admin', 'Activity'); ?></strong></div>

                <div class="panel-body">
                    <?= $form->field($model, 'created')->textInput([
	                    'readonly' => true
                    ]); ?>

                    <?= $form->field($model, 'modified')->textInput([
	                    'readonly' => true
                    ]); ?>

                    <?= $form->field($model, 'last_ip')->textInput([
	                    'readonly' => true
                    ]); ?>

                    <?= $form->field($model, 'last_login')->textInput([
	                    'readonly' => true
                    ]); ?>

                    <?= $form->field($model, 'activated')->label(false)->widget(CheckboxX::className(), [
	                    'autoLabel' => true,
	                    'labelSettings' => [
		                    'label' => $model->getAttributeLabel('activated'),
		                    'position' => CheckboxX::LABEL_RIGHT
	                    ],
	                    'pluginOptions' => [
                            'threeState' => false
                        ]
                    ]); ?>

                    <?= $form->field($model, 'banned')->label(false)->widget(CheckboxX::className(), [
	                    'autoLabel' => true,
	                    'labelSettings' => [
		                    'label' => Yii::t('app/admin', 'Ban'),
		                    'position' => CheckboxX::LABEL_RIGHT
	                    ],
	                    'pluginOptions' => [
		                    'threeState' => false
	                    ]
                    ]); ?>

                    <?= $form->field($model, 'ban_reason')->textarea(['maxlength' => true]); ?>
                </div>
            </div>
        </div>
	</div>

	<?= $form->field($model, 'id')->label(false)->hiddenInput(); ?>

    <div class="form-group fixed-bottom-toolbar">
		<?= Html::hiddenInput('isNew', $model->isNewRecord ? '1' : '0', ['id' => 'isNew']); ?>

		<?= Html::submitButton($model->isNewRecord ?
			IconHelper::show('add') . Yii::t('app/admin', 'Create User') :
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
					'data-callback' => '/admin/user/delete?id=' . $model->id
				]) .
			  ' ' . Html::a('<i class="fa fa-window-restore"></i>Посмотреть профиль', '/profile/' . $model->username, ['target' => '_blank', 'class' => 'btn btn-warning']);
		?>

		<?= Html::a('<i class="fa fa-undo"></i>Отменить', Yii::$app->request->referrer, ['class' => 'btn btn-default']); ?>
    </div>

	<?php ActiveForm::end(); ?>

</div>