<?php
namespace app\api\modules\v1;

class Module extends \yii\base\Module
{
	public $controllerNamespace = 'app\api\modules\v1\controllers';
	public $defaultRoute = 'default';
	
    public function init()
    {
        parent::init();

        // ...  other initialization code ...
    }
}