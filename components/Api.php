<?php
namespace app\components;

use Yii;
use yii\base\Component;
use app\models\User;
use app\models\AccessToken;

/**
 * Class for common API functions
 */
class Api extends Component
{

    public function sendFailedResponse($message, $code = 400)
    {
        $this->setHeader($code);

        Yii::$app->response->data  = ['status' => 0, 'error_code' => $code, 'message' => $message];

        Yii::$app->end();
    }

    public function sendSuccessResponse($data = false, $additional_info = false)
    {

        $this->setHeader(200);

        $response = [];
        $response['status'] = 1;

        if (is_array($data))
            $response['data'] = $data;

        if ($additional_info) {
            $response = array_merge($response, $additional_info);
        }
        
        Yii::$app->response->data  = $response;

        Yii::$app->end();

    }

    protected function setHeader($status)
    {

        $text = $this->_getStatusCodeMessage($status);

        Yii::$app->response->setStatusCode($status, $text);

        $status_header = 'HTTP/1.1 ' . $status . ' ' . $text;
        $content_type = "application/json; charset=utf-8";

        header($status_header);
        header('Content-type: ' . $content_type);
        header('X-Powered-By: ' . Yii::$app->params['XPoweredBy']);
        header('Access-Control-Allow-Origin:*');

    }

    protected function _getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

    public function createAccesstoken($user_id)
    {

        $model = new AccessToken();

        $user = User::findIdentity($user_id);

        $model->token = $user->getJWT();

        $model->expired_at = time() + Yii::$app->params['expiresIn'];

        $model->user_id = $user_id;

        $model->created_at = time();

        $model->updated_at = time();

        $model->save(false);

        return ($model);

    }

    public function refreshAccesstoken($token)
    {
        $access_token = AccessToken::findOne(['token' => $token]);
        if ($access_token) {
            $user_id = $access_token->user_id;
            $access_token->delete();
            $new_access_token = $this->createAccesstoken($user_id);
            return ($new_access_token);
        } else {
            Yii::$app->api->sendFailedResponse("Invalid Access token2", 403);
        }
    }
}
