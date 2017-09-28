<?php
namespace app\components\widgets;

use app\components\UrlHelper;
use app\models\User;
use yii\base\Widget;
use yii\bootstrap\Html;

class UserLink extends Widget
{
    /**
     * attributes for <a> tag
     * @var array
     */
    public $options = [];

    /**
     * Show user avatar
     * @var bool
     */
    public $showAvatar = true;

    /**
     * @var User
     */
    public $user;

    public $url = 'profile/';

    public $urlAttribute = 'username';

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $avatar = $this->showAvatar ? Html::img(User::getDefaultAvatar($this->user->avatar), ['width' => 16]) : '';

        return Html::a(
            $avatar . $this->user->username,
            UrlHelper::absolute($this->url . $this->user->{$this->urlAttribute}),
            $this->options
        );
    }
}