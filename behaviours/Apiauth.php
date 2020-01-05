<?php
namespace app\behaviours;
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

use Yii;
use yii\filters\auth\AuthMethod;

/**
 * QueryParamAuth is an action filter that supports the authentication based on the access token passed through a query parameter.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Apiauth extends AuthMethod
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'authorization';

    public $exclude = [];
    public $callback = [];


    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $headers = Yii::$app->getRequest()->getHeaders();

        $accessToken=NULL;
        
        if (isset($_GET[$this->tokenParam])) {
            $accessToken=$_GET[$this->tokenParam];
        } else {
            $accessToken = $headers->get($this->tokenParam);
        }

        if (empty($accessToken)) {

            if (isset($_GET[$this->tokenParam])) {
                $accessToken=$_GET[$this->tokenParam];
            } else {
                $accessToken = $headers->get('x-authorization');
            }
        }

        if (is_string($accessToken)) {

            $accessToken = str_replace("Bearer ", "", $accessToken);

            $identity = $user->loginByAccessToken($accessToken, get_class($this));

            if ($identity !== null) {
                return $identity;
            }
        }

        if ($accessToken !== null) {
            $this->handleFailure('Invalid Access token');
        }

        return null;
    }

    public function beforeAction($action)
    {
        if (in_array($action->id, $this->exclude) &&
            !isset($_GET[$this->tokenParam]))
        {
            return true;
        }

        if (in_array($action->id, $this->callback) &&
            !isset($_GET[$this->tokenParam]))
        {
            return true;
        }

        $response = $this->response ?: Yii::$app->getResponse();

        $identity = $this->authenticate(
            $this->user ?: Yii::$app->getUser(),
            $this->request ?: Yii::$app->getRequest(),
            $response
        );

        if ($identity !== null) {
            return true;
        } else {
            $this->challenge($response);
            $this->handleFailure($response);
        }
    }

    /**
     * @inheritdoc
     */
    public function handleFailure($response)
    {
        Yii::$app->api->sendFailedResponse('Invalid Access token', 403);
        //throw new UnauthorizedHttpException('You are requesting with an invalid credential.');
    }

}
