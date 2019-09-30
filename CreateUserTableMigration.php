<?php

namespace denis303\user;

abstract class CreateUserTableMigration extends BaseCreateUserTableMigration
{

    const FIELD_PREFIX = 'user_';

    public $table = 'user';

    public function getFields()
    {
        return [
            static::FIELD_PREFIX . 'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
                'unsigned' => true
            ],
            static::FIELD_PREFIX . 'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            static::FIELD_PREFIX . 'email' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'unique' => true            
            ],
            static::FIELD_PREFIX . 'password_hash' => [
                'type' => 'VARCHAR',
                'constraint' => '60'
            ],
            static::FIELD_PREFIX . 'created_at' => [ 
                'type' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP'
            ]
        ];
    }

    public function up()
    {
        $this->forge->addField($this->getFields());

        $this->forge->addKey(static::FIELD_PREFIX . 'id', true);

        $this->beforeCreateTable();

        $this->forge->createTable($this->table, false, $this->tableOptions());

        $this->afterUp();
    }

    public function beforeCreateTable()
    {
    }

    public function down()
    {
        $this->beforeDown();

        $this->forge->dropTable($this->table);
    }

    public function afterUp()
    {
    }

    public function beforeDown()
    {
    }

}