<?php

namespace bertoost\mailerchain\migrations;

use craft\db\Migration;

class m230218_133502_add_test_success_field extends Migration
{
    public function safeUp(): bool
    {
        $this->addColumn('{{%mailerchain}}', 'testSuccess', $this->boolean()->defaultValue(false)->after('sent'));

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropColumn('{{%mailerchain}}', 'testSuccess');

        return false;
    }
}
