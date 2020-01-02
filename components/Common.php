<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\WebSocketClient;

class Common extends Component 
{
    public $baseUrl;
    public $themeUrl;

    /**
     * init common component
     * @return [type] [description]
     */
    public function init(){
        parent::init();
        $this->baseUrl = Url::base(true);
        $this->jsGlobal();
        // $this->registerCKFinder();
    }

    public static function d($arr)
    {
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
        die;
    }

    public static function randomStringId($length = 10)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public static function formatBalance($amount = 0) {
        return '<span>'.number_format($amount, 0, '.', ',').' '.Yii::t('app', 'chip').'</span>';
    }

    public static function formatDate($date, $onlyDate = false)
    {
        $format = $onlyDate ? 'd/m/Y' : 'd/m/Y H:i';
        if(!is_numeric($date)) {
            return date($format, strtotime($date));
        }else {
            return date($format, $date);
        }
    }

    public static function formatNumber($amount, $decimal = 2)
    {
        return number_format($amount, $decimal, '.', ',');
    }

    /**
     * register js global
     * @return [type] [description]
     */
    public function jsGlobal()
    {
        $options = [
            "baseUrl" => $this->baseUrl,
        ];
        Yii::$app->view->registerJs("var js = " . json_encode($options) . ";", yii\web\View::POS_HEAD, 'my-options');
    }

    public static function getTimeDifference($datetime, $full = false)
    {
        if (empty($datetime)) {
            return '';
        }
        $now = new \DateTime;
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . Yii::t('app', $v . ($diff->$k > 1 ? 's' : ''));
            } else {
                unset($string[$k]);
            }
        }
        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ' . Yii::t('app', 'ago') : Yii::t('app', 'just now');

    }

    function notify($data) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        // curl_setopt($curl, CURLOPT_VERBOSE, 1);
        // curl_setopt($curl, CURLOPT_CAINFO, '/var/www/localhost.crt');

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));

        curl_setopt($curl, CURLOPT_URL, SOCKET_SERVER . '/notify');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/x-www-form-urlencoded',
            )
        );

        $result = curl_exec($curl);

        curl_close($curl);
    }

    public static function gen_uuidv4() {
        if (function_exists('com_create_guid') === true)
        return trim(com_create_guid(), '{}');

        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function encryptDecrypt($action, $string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'Basic123!@#';
        $secret_iv = 'Basic123!@#IV';
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }
}
