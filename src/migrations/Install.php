<?php

namespace bertoost\mailerchain\migrations;

use craft\db\Migration;

class Install extends Migration
{
    public function safeUp(): bool
    {
        $this->createTable('{{%mailerchain}}', [
            'id' => $this->primaryKey(),
            'transportType' => $this->string()->notNull(),
            'transportSettings' => $this->longText()->null(),
            'transportClass' => $this->string()->notNull(),
            'sent' => $this->integer()->defaultValue(0),
            'testSuccess' => $this->boolean()->defaultValue(false),
            'ranking' => $this->integer()->defaultValue(0),
            // defaults
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->addForeignKey(null, '{{%mailerchain}}', 'id', '{{%elements}}', 'id', 'CASCADE');

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropForeignKeyIfExists('{{%mailerchain}}', 'id');
        $this->dropTableIfExists('{{%mailerchain}}');

        return true;
    }
}
