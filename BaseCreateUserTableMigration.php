<?php

namespace denis303\user;

abstract class BaseCreateUserTableMigration extends \App\Components\BaseMigration
{

    public $table = 'user';

    public $fieldPrefix = 'user_';

    public function getFields()
    {
        return [
            $this->fieldPrefix . 'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
                'unsigned' => true
            ],
            $this->fieldPrefix . 'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            $this->fieldPrefix . 'password_hash' => [
                'type' => 'VARCHAR',
                'constraint' => '60'
            ],
            $this->fieldPrefix . 'password_reset_token' => [
                'type' => 'VARCHAR',
                'constraint' => '32',
                'unique' => true,
                'null' => true
            ],
            $this->fieldPrefix . 'verification_token' => [
                'type' => 'VARCHAR',
                'constraint' => '32',
                'unique' => true,
                'null' => true
            ],
            $this->fieldPrefix . 'email' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'unique' => true            
            ],
            $this->fieldPrefix . 'status' => [
                'type' => 'TINYINT',
                'unsigned' => true,
                'default' => 10
            ],
            $this->fieldPrefix . 'created_at' => [ 
                'type' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP'
            ],
            $this->fieldPrefix . 'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ];
    }

	public function up()
	{
        $this->forge->addField($this->getFields());

        $this->forge->addKey($this->fieldPrefix . 'id', true);

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