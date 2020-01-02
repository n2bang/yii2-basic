<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m191227_074548_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'user_type' => $this->string(15)->notNull()->defaultValue('USER'),
            'hash_password' => $this->string()->notNull(),
            'alias' => $this->string(255),
            'open_id' => $this->string(50),
            'open_service' => $this->string(50),
            'open_status' => $this->integer()->defaultValue(0),
            'auth_key' => $this->string(50),
            'activation_code' => $this->string(255),
            'status' => $this->string(15)->notNull()->defaultValue('ACTIVE'),
            'is_online' => $this->boolean()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
