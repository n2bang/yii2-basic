<?php
namespace app\api\modules\v1\controllers;

use Yii;

use yii\filters\AccessControl;
use app\behaviours\Verbcheck;
use app\behaviours\Apiauth;
use app\components\BaseController;

use yii\helpers\Url;
use app\models\LoginForm;
use app\models\AccessTokens;

class ProjectController extends BaseController
{

    // We are using the regular web app modules:
    public $modelClass = 'app\models\Projects';

    public function actionCreate() {
    	#todo
    }
}