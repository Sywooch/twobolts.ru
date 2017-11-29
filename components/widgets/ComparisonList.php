<?php
namespace app\components\widgets;

use app\components\widgets\assets\ComparisonListAsset;
use app\models\Comparison;
use yii\base\Widget;
use yii\data\Pagination;

class ComparisonList extends Widget
{
    const ITEMS_PER_PAGE = 12;
    
    /**
     * Создавать подгрузку остальных данных или нет
     * @var bool
     */
    public $loadMore = false;
    
    /**
     * Показывать имя пользователя или нет
     * @var bool
     */
    public $showUser = true;

    /**
     * Показывать дату или нет
     * @var bool
     */
    public $showDate = false;

    /**
     * Показывать рейтинг или нет
     * @var bool
     */
    public $showRating = true;

    /**
     * Показывать количество комментариев или нет
     * @var bool
     */
    public $showComments = true;
    
    /**
     * Показывать аватар пользователя или нет.
     * Зависит от $showUser
     * @var
     */
    public $showAvatar = null;

    /**
     * Массив данных для отображения
     * @var Comparison[]
     */
    public $items;

    /**
     * Общее количество данных
     * Используется для постраничности
     * @var int
     */
    public $itemsCount;

    /**
     * Параметры для передачи в JS
     */
    public $controllerAction = '';
    public $_params = '';
    public $_pageNum = 1;
    public $_sorting = 'date';
    public $_options = [];

	/**
	 * Init
	 */
    public function init()
    {
        parent::init();
        
        if ($this->showUser === true && $this->showAvatar === null) {
            $this->showAvatar = true;
        }
    }

	/**
	 * @return string
	 */
    public function run()
    {
        ComparisonListAsset::register($this->getView());
        
        return $this->render('comparison_list');
    }
}