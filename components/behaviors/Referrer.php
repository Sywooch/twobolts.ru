<?php
namespace app\components\behaviors;

use app\components\ArrayHelper;
use Yii;
use yii\base\ActionEvent;
use yii\base\Behavior;
use yii\log\Logger;
use yii\web\Controller;

class Referrer extends Behavior
{
    public $key = 'index_referrer';

    public $actions = [];

    /**
     * Declares event handlers for the [[owner]]'s events.
     * @return array events (array keys) and the corresponding event handler methods (array values).
     */
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction',
            Controller::EVENT_AFTER_ACTION => 'afterAction'
        ];
    }

    public function beforeAction($event)
    {

    }

    /**
     * @param ActionEvent $event
     */
    public function afterAction($event)
    {
        $action = $event->action->id;

        if (isset($this->actions[$action])) {
            $url = parse_url(Yii::$app->request->referrer);
            $queryString = ArrayHelper::getValue($url, 'query');

            Yii::$app->session->set($this->key, $url['path'] . ($queryString ? '?' . $queryString : ''));
        }
    }

    /**
     * @param $key
     * @param string $default
     * @return mixed
     */
    public static function getReferrer($key, $default = '')
    {
        return Yii::$app->session->get($key, $default);
    }
}