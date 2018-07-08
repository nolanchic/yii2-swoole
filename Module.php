<?php
namespace nolanchic\swoole;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'nolanchic\swoole\controllers';
    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }
}
