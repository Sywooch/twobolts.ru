<?php
namespace app\components\widgets;

use app\components\ArrayHelper;
use cics\widgets\VideoEmbed;
use kartik\base\InputWidget;

class VideoInput extends InputWidget
{
    public $showVideo = false;

    public function init()
    {
        parent::init();
        $this->initClass();

        $input = $this->getInput('textInput');

        $video = '';
        if ($this->showVideo) {
            $video = VideoEmbed::widget([
                'url' => $this->value,
                'show_errors' => true
            ]);
        }

        echo $video . $input;
    }

    /**
     * Инициализируем bootstrap для текстового поля
     */
    public function initClass()
    {
        $class = ArrayHelper::getValue($this->options, 'class', '');
        $class = str_replace('form-control', '', $class);
        $class = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $class);

        $this->options['class'] = 'form-control ' . trim($class);
    }
}