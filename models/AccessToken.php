<?php

namespace app\models;

use Yii;
use app\components\Common;

/**
 * This is the model class for table "{{%access_tokens}}".
 *
 * @property string $id
 * @property string $token
 * @property string $user_id
 * @property string $app_id
 * @property int $expired_at
 * @property int $updated_at
 * @property int $created_at
 * @property string $device_id
 */
class AccessToken extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%access_tokens}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token', 'user_id', 'expired_at'], 'required'],
            [['user_id', 'expired_at', 'updated_at', 'created_at'], 'integer'],
            [['token'], 'string', 'max' => 500],
            [['app_id', 'device_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Id'),
            'token' => Yii::t('app', 'Token'),
            'user_id' => Yii::t('app', 'User ID'),
            'app_id' => Yii::t('app', 'App ID'),
            'expired_at' => Yii::t('app', 'Expired At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_at' => Yii::t('app', 'Created At'),
            'device_id' => Yii::t('app', 'Device ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if($this->isNewRecord) {
            $this->created_at = time();
        }
        $this->updated_at = time();
        return parent::beforeSave($insert);
    }
}
