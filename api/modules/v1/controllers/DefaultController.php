<?php
namespace app\api\modules\v1\controllers;

use Yii;

use yii\filters\AccessControl;
use app\behaviours\Verbcheck;
use app\behaviours\Apiauth;
use app\components\BaseController;
use yii\helpers\Url;

use app\models\User;
use app\models\AccessToken;
use app\models\LoginForm;
use app\models\SignupForm;
use app\models\Constant;

/**
 * @SWG\Info(title="API", version="0.1")
 */
/**
 *  @SWG\Swagger(
 *      schemes={"http", "https"},
 *      produces={"application/json"},
 *      consumes={"application/x-www-form-urlencoded"},
 *      basePath="/api/v1",
 *  )
 */

class DefaultController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [
            'apiauth' => [
                'class' => Apiauth::className(),
                'exclude' => ['index', 'docs', 'json-schema', 'accesstoken', 'refresh-access-token', 'register'],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['*'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => Verbcheck::className(),
                'actions' => [
                    'refresh-access-token' => ['POST'],
                    'logout' => ['GET'],
                    'accesstoken' => ['POST'],
                    'register' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'docs' => [
                'class' => 'yii2mod\swagger\SwaggerUIRenderer',
                'restUrl' => Url::to(['default/json-schema']),
            ],
            'json-schema' => [
                'class' => 'yii2mod\swagger\OpenAPIRenderer',
                // Тhe list of directories that contains the swagger annotations.
                'scanDir' => [
                    Yii::getAlias('@app/api/modules/v1/controllers'),
                ]
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex() {
        echo 'Version1 Gata API'; exit;
    }

    /**
     * @SWG\Post(
     *     path="/accesstoken",
     *     summary="Login to the application",
     *     produces={"application/json"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     tags={"Authorize"},
     *     description="Login to app for get Token access",
     *     @SWG\Parameter(
     *         name="username",
     *         in="formData",
     *         type="string",
     *         description="Username",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         type="string",
     *         description="Password",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     )
     * )
     */
    public function actionAccesstoken()
    {
        $model = new LoginForm();

        $model->attributes = Yii::$app->request->post();

        if ($model->login()) {
            $access_token = Yii::$app->api->createAccesstoken(Yii::$app->user->identity['id']);
            $user = Yii::$app->user->identity;
            $user['is_online'] = 1;
            $user->save();

            $data = [];
            $data['access_token'] = $access_token->token;
            $data['token_type'] = "Bearer";
            $data['expires_in'] = Yii::$app->params['expiresIn'];
            /*
            $historyLogin = new HistoryLogin();
            $historyLogin->user_id = Yii::$app->user->identity['id'];
            $historyLogin->ip = @$_SERVER['REMOTE_ADDR'];
            $historyLogin->created_at = time();
            $historyLogin->save();
            */
            Yii::$app->api->sendSuccessResponse($data);
        } else {
            Yii::$app->api->sendFailedResponse($model->errors);
        }
    }

    /**
     * @SWG\Get(
     *     path="/logout",
     *     summary="Logout application",
     *     description="Logout application",
     *     produces={"application/json"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     tags={"Users"},
     *     @SWG\Parameter(
     *          type="string",
     *          name="Authorization",
     *          in="header",
     *          description="Bearer + token",
     *          required=true
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     )
     * )
     */
    public function actionLogout()
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        $access_token = $headers->get('authorization');

        if (!$access_token) {
            $access_token = Yii::$app->getRequest()->getQueryParam('x-authorization');
        }

        $access_token = str_replace("Bearer ", "", $access_token);

        $model = AccessToken::findOne(['token' => $access_token]);

        if ($model->delete()) {
            $user = Yii::$app->user->identity;
            $user['is_online'] = 0;
            $user->save();

            Yii::$app->api->sendSuccessResponse('LoggedOutSuccessfully');

        } else {
            Yii::$app->api->sendFailedResponse("InvalidRequest");
        }
    }

    /**
     * @SWG\Post(
     *     path="/refresh-access-token",
     *     summary="Refresh access token",
     *     description="Refresh access token",
     *     produces={"application/json"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     tags={"Register"},
     *     @SWG\Parameter(
     *          name="token",
     *          in="path",
     *          type="string",
     *          description="Token",
     *          required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     )
     * )
     */
    public function actionRefreshAccessToken()
    {
        $token = Yii::$app->request->post('token');

        if(empty($token)) {
            Yii::$app->api->sendFailedResponse('TokenEmpty');
        }

        $accesstoken = AccessToken::findOne(['token' => $token]);
        if ($accesstoken) {
            if ($accesstoken->expires_at < time()) {
                Yii::$app->api->sendFailedResponse('Access token expired', 403);
            }
            $data = [];
            $access_token = Yii::$app->api->refreshAccesstoken($token);
            if (is_object($access_token)) {
                $data['access_token'] = $access_token->token;
                $data['token_type'] = "Bearer";
                $data['expires_in'] = Yii::$app->params['expiresIn'];
            }
            Yii::$app->api->sendSuccessResponse($data);
        } else {
            Yii::$app->api->sendFailedResponse('TokenEmpty');
        }

    }

    /**
     * @SWG\Post(
     *   path="/register",
     *   summary="Register a new user",
     *   produces={"application/json"},
     *   consumes={"application/x-www-form-urlencoded"},
     *   tags={"Register"},
     *   @SWG\Parameter(
     *      name="username",
     *      in="formData",
     *      type="string",
     *      description="Username",
     *      required=true,
     *   ),
     *   @SWG\Parameter(
     *      name="password",
     *      in="formData",
     *      type="string",
     *      description="Password",
     *      required=true,
     *   ),
     *   @SWG\Parameter(
     *      name="alias",
     *      in="formData",
     *      type="string",
     *      description="Alias",
     *      required=true,
     *   ),
     *   @SWG\Parameter(
     *      name="user_type",
     *      in="formData",
     *      type="string",
     *      description="ADMIN | USER",
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="OK",
     *   ),
     *   @SWG\Response(
     *      response="default",
     *      description="Bad Request",
     *   )
     * )
     */

    public function actionRegister()
    {
        $model = new SignupForm();
        $post = Yii::$app->request->post();
        $model->attributes = $post;
        if (!$model->validate()) {
        	$errors = $model->getFirstErrors();
            $message = reset($errors);
            Yii::$app->api->sendFailedResponse($message);
        }
        
        $user = new User();
        $user->username = $model->username;
        $user->alias = !empty($model->alias) ? $model->alias : $model->username;
        $user->status = Constant::STATUS_ACTIVE;
        $user->user_type = isset($post['user_type']) ? $model->user_type : Constant::TYPE_USER;
        $user->setPassword($model->password);
        $user->generateAuthKey();
        $user->generateActivationCode();

        if ($user->save()) {
            $data = $user->attributes;
            unset($data['auth_key']);
            unset($data['hash_password']);
            unset($data['activation_code']);

            Yii::$app->api->sendSuccessResponse($data);
        }
    }

}
