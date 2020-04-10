<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;
use app\models\Constant;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $alias;
    public $password;
    public $repassword;
    public $open_id;
    public $open_service;
    public $token;
    public $user_type;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username','password'],'required'],
            ['username', 'trim'],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' =>Yii::t('app', 'This username has already been taken')],
            ['username', 'string', 'min' => 6, 'max' => 12],
            ['username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u', 'message' =>  Yii::t('app', 'Username can contain only A-Za-z0-9_')],

            ['alias', 'trim'],
            ['alias', 'string', 'min' => 6, 'max' => 12],
            ['alias', 'unique', 'targetClass' => '\app\models\User', 'message' => Yii::t('app', 'This alias has already been taken')],
            ['alias', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u', 'message' =>  Yii::t('app', 'Alias can contain only A-Za-z0-9_')],

            ['password', 'string', 'min' => 6, 'max' => 20],
            ['repassword', 'compare', 'compareAttribute' => 'password', 'message' => Yii::t('app', 'Passwords don\'t match') ],
            ['alias', 'checkExist', 'on' => 'change-alias'],
            ['token', 'required', 'on' => 'signup-app'],
            [['username', 'user_type', 'alias'], 'safe'],
        ];
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['service'] = ['open_id'];
        $scenarios['update-alias'] = ['username', 'alias', 'open_id'];
        $scenarios['change-alias'] = ['alias'];
        $scenarios['signup-app'] = ['token'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'alias' => Yii::t('app', 'Alias'),
            'password' => Yii::t('app', 'Password'),
            'repassword' => Yii::t('app', 'Re-password'),
        ];
    }

    public function checkExist($attribute) {
        $user = Yii::$app->user->identity;
        if ($user && $this->alias != $user->alias) {
            $result = User::findOne(['alias' => $this->alias]);
            if (null != $result) {
                $this->addError($attribute, Yii::t('app', "This {$attribute} has already been taken"));
            }
        }
    }
}
