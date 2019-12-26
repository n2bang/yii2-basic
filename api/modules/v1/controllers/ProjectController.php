<?php
namespace app\api\modules\v1\controllers;

use yii\rest\ActiveController;
use Yii;

class ProjectController extends ActiveController
{
    // We are using the regular web app modules:
    public $modelClass = 'app\models\Projects';

    public function actionRegister() {
    	#todo
    }
}