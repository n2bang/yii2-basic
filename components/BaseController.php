<?php
namespace app\components;

use Yii;
use yii\web\Response;
use yii\web\Controller;


class BaseController extends Controller
{
    public $request;

    public $enableCsrfValidation = false;

    public $headers;

    public $baseUrl;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Allow-Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => []
            ]

        ];
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($action->id != 'docs') {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
        return parent::beforeAction($action);
    }

    public function init()
    {
        $this->request = json_decode(file_get_contents('php://input'), true);
        $this->baseUrl = Yii::getAlias('@webroot');

        if ($this->request &&! is_array($this->request)) {
            Yii::$app->api->sendFailedResponse(['Invalid Json']);
        }

    }

}   

