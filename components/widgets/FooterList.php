<?php
namespace app\components\widgets;

use app\models\Comparison;
use app\models\News;
use app\models\User;
use yii\base\Widget;

class FooterList extends Widget
{
    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $news = News::find()->orderBy(['created' => SORT_DESC])->limit(3)->all();
        
        return $this->render('footer_list', [
            'topComparisons' => Comparison::getTopComparisons(),
            'activeUsers' => User::getActiveUsers(),
            'lastNews' => $news
        ]);
    }
}