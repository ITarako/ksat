<?php

use yii\db\Migration;

/**
 * Class m180525_041315_accountAndOperations
 */
class m180525_041315_accountAndOperations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%account}}', [
            'id' => $this->primaryKey(),
            'value' => $this->integer()->notNull()->defaultValue(0),
            'id_user' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%operations}}', [
            'id' => $this->primaryKey(),
            'value' => $this->integer()->notNull(),
            'created_at' => 'timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'id_sender' => $this->integer(),
            'id_receiver' => $this->integer()->notNull(),
            'id_creator' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-account-id_user',
            'account',
            'id_user',
            'user',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-operations-id_sender',
            'operations',
            'id_sender',
            'account',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-operations-id_receiver',
            'operations',
            'id_receiver',
            'account',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-operations-id_creator',
            'operations',
            'id_creator',
            'account',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%operations}}');
        $this->dropTable('{{%account}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "accountAndOperations cannot be reverted.\n";

        return false;
    }
    */
}
