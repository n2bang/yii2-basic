<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\Constant;
use app\components\UserJwt;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{

    use UserJwt;
    public $authKey;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['user_type', 'in', 'range' => [Constant::TYPE_ADMIN, Constant::TYPE_USER]],
            ['status', 'in', 'range' => [Constant::STATUS_ACTIVE, Constant::STATUS_DEACTIVE]],
            [['username', 'alias', 'hash_password'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if($this->isNewRecord) {
            $this->created_at = time();
            $this->updated_at = time();
        }

        $this->updated_at = time();

        return parent::beforeSave($insert);
    }
    
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => Constant::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $access_token = AccessTokens::findOne(['token' => $token]);
        if (!$access_token)
            return false;

        if ($access_token->expired_at < time()) {
            Yii::$app->api->sendFailedResponse('Access token expired', 403);
        }

        return static::findOne(['id' => $access_token->user_id]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => Constant::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->hash_password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->hash_password = Yii::$app->security->generatePasswordHash($password);
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generateActivationCode()
    {
        $this->activation_code = Yii::$app->security->generateRandomString() . '_' . time();
    }
}
