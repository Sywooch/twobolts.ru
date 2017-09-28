<?php

namespace app\components\widgets\assets;


use yii\web\AssetBundle;

class ComparisonListAsset extends AssetBundle
{
    public $sourcePath = '@app/components/widgets';

    public $css = [
        'css/comparison_list.css',
    ];

    public $js = [
        'js/comparison_list.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public function init()
    {
        parent::init();

        $this->publishOptions['forceCopy'] = true;
    }
}