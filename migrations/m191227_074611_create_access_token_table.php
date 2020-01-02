<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%access_tokens}}`.
 */
class m191227_074611_create_access_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%access_tokens}}', [
            'id' => $this->primaryKey(),
            'token' => $this->string(500)->notNull(),
            'user_id' => $this->integer()->notNull(),
            'app_id' => $this->string(255)->notNull(),
            'expired_at' => $this->integer()->notNull(),
            'device_id' => $this->string(255),
            'updated_at' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%access_tokens}}');
    }
}
