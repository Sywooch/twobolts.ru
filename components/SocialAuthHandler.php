<?php
namespace app\components;

use app\models\User;
use dosamigos\transliterator\TransliteratorHelper;
use Yii;
use yii\authclient\ClientInterface;
use yii\authclient\clients\Twitter;
use yii\base\Model;
use yii\helpers\BaseUrl;

/**
 * SocialAuthHandler handles successful authentication via Yii auth component
 */
class SocialAuthHandler
{
	/**
	 * @var ClientInterface|Twitter
	 */
	private $client;

	/**
	 * SocialAuthHandler constructor.
	 *
	 * @param ClientInterface $client
	 */
	public function __construct(ClientInterface $client)
	{
		$this->client = $client;
	}

	/**
	 * @return $this|mixed
	 */
	public function handle()
	{
		$attributes = $this->client->getUserAttributes();
		//echo '<pre>'.print_r($attributes, 2);exit;

		$nickname = $this->getUsername($attributes);
		$email = $this->getEmail($attributes);

		$id = ArrayHelper::getValue($attributes, 'id');

		if (Yii::$app->user->isGuest) {
			/** @var User $user */
			$user = User::find()->where([$this->client->getId() . '_id' => $id])->one();

			if ($user) {
				$this->updateUserInfo($user, $id);

				Yii::$app->user->login($user, 3600*24*30);
			} else {
				if ($email !== null && User::find()->where(['email' => $email])->exists()) {
					Yii::$app->getSession()->setFlash('error',
						Yii::t('app/error', "User with the same email as in {client} account already exists.", [
							'client' => $this->client->getTitle()
						])
					);
				} elseif ($nickname !== null && User::find()->where(['username' => $nickname])->exists()) {
					Yii::$app->getSession()->setFlash('error',
						Yii::t('app/error', "User with the same username as in {client} account already exists.", [
							'client' => $this->client->getTitle()
						])
					);
				} else {
					$user = new User();
					$user->scenario = Model::SCENARIO_DEFAULT;

					$user->username = $nickname;
					$user->email = is_null($email) ? '' : $email;
					$user->password = Yii::$app->security->generateRandomString(8);

					if ($user->register(!is_null($email))) {
						$this->updateUserInfo($user, $id);
					} else {
						Yii::$app->getSession()->setFlash('error',
							Yii::t('app', 'Unable to save {client} account: {errors}', [
								'client' => $this->client->getTitle(),
								'errors' => json_encode($user->stringifyErrors())
							])
						);
					}
				}
			}
		} else {
			/** @var User $user */
			/** @var User $registeredUser */
			$user = Yii::$app->user->identity;
			$registeredUser = User::find()->where([$this->client->getId() . '_id' => $id])->one();

			if ($registeredUser && $user->id != $registeredUser->id) {
				Yii::$app->getSession()->setFlash('error',
					Yii::t('app/error',
						'Unable to link {client} account. There is another user using it.',
						['client' => $this->client->getTitle()]
					)
				);
			} else {
				$this->updateUserInfo($user, $id);

				Yii::$app->getSession()->setFlash('success',
					Yii::t('app', 'Linked {client} account.', [
						'client' => $this->client->getTitle()
					])
				);
			}
		}

		$redirect = Yii::$app->session->get('referenceUrl', '/');

		return Yii::$app->response->redirect($redirect);
	}

	/**
	 * @param User $user
	 * @param $id
	 */
	private function updateUserInfo($user, $id)
	{
		$user->updateAttributes([
			$this->client->getId() . '_id' => $id,
			$this->client->getId() . '_token' => json_encode($this->client->accessToken->getParams())
		]);
	}

	/**
	 * @param $attributes
	 *
	 * @return mixed|null|string
	 */
	private function getUsername($attributes)
	{
		$callback = 'get' . ucfirst($this->client->getId()) . 'Name';

		return $this->{$callback}($attributes);
	}

	/**
	 * @param $attributes
	 *
	 * @return mixed|null|string
	 */
	private function getEmail($attributes)
	{
		$callback = 'get' . ucfirst($this->client->getId()) . 'Email';

		return $this->{$callback}($attributes);
	}

	/**
	 * @param $attributes
	 *
	 * @return mixed|null|string
	 */
	private function getVkontakteName($attributes)
	{
		$nickname = ArrayHelper::getValue($attributes, 'nickname');

		if ($nickname) {
			return TransliteratorHelper::process($nickname, '', 'en');
		}

		$firstName = ArrayHelper::getValue($attributes, 'first_name');
		$lastName = ArrayHelper::getValue($attributes, 'last_name');

		if ($firstName . $lastName) {
			return TransliteratorHelper::process($firstName . $lastName, '', 'en');
		}

		$screen_name = ArrayHelper::getValue($attributes, 'screen_name');

		if ($screen_name) {
			return TransliteratorHelper::process($screen_name, '', 'en');
		}

		return '';
	}

	/**
	 * @return null
	 */
	private function getVkontakteEmail()
	{
		return null;
	}

	/**
	 * @param $attributes
	 *
	 * @return mixed|null|string
	 */
	private function getFacebookName($attributes)
	{
		$name = ArrayHelper::getValue($attributes, 'name');

		if ($name) {
			return TransliteratorHelper::process($name, '', 'en');
		}

		return '';
	}

	/**
	 * @return null
	 */
	private function getFacebookEmail($attributes)
	{
		return ArrayHelper::getValue($attributes, 'email');
	}

	/**
	 * @param $attributes
	 *
	 * @return mixed|null|string
	 */
	private function getTwitterName($attributes)
	{
		$screen_name = ArrayHelper::getValue($attributes, 'screen_name');

		if ($screen_name) {
			return TransliteratorHelper::process($screen_name, '', 'en');
		}

		$name = ArrayHelper::getValue($attributes, 'name');

		if ($name) {
			return TransliteratorHelper::process($name, '', 'en');
		}

		return '';
	}

	/**
	 * @return null
	 */
	private function getTwitterEmail()
	{
		return null;
	}

	/**
	 * @param $attributes
	 *
	 * @return mixed|string
	 */
	private function getGoogleName($attributes)
	{
		$name = ArrayHelper::getValue($attributes, 'displayName');

		if ($name) {
			return TransliteratorHelper::process($name, '', 'en');
		}

		return '';
	}

	/**
	 * @param $attributes
	 *
	 * @return mixed|string
	 */
	private function getGoogleEmail($attributes)
	{
		$emails = ArrayHelper::getValue($attributes, 'emails');

		if ($emails) {
			if (is_array($emails)) {
				$email = ArrayHelper::getValue($emails, 0);

				return ArrayHelper::getValue($email, 'value');
			} else {
				return $emails;
			}
		}

		return '';
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	public static function getReturnUrl($name)
	{
		return BaseUrl::home(true) . 'social/auth?authclient=' . $name;
	}
}