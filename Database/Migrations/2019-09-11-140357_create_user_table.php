<?php

namespace denis303\user\Database\Migrations;

class CreateUserTable extends \App\Components\BaseMigration
{

    public $table = 'user';

	public function up()
	{
        $this->forge->addField([
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
                'unsigned' => true
            ],
            'user_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'user_password_hash' => [
                'type' => 'VARCHAR',
                'constraint' => '60'
            ],
            'user_password_reset_token' => [
                'type' => 'VARCHAR',
                'constraint' => '32',
                'unique' => true,
                'null' => true
            ],
            'user_verification_token' => [
                'type' => 'VARCHAR',
                'constraint' => '32',
                'unique' => true,
                'null' => true
            ],
            'user_email' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'unique' => true            
            ],
            'user_status' => [
                'type' => 'TINYINT',
                'unsigned' => true,
                'default' => 10
            ],
            'user_created_at' => [ 
                'type' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP'
            ],
            'user_updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('user_id', true);

        $this->forge->createTable($this->table, false, $this->tableOptions());
	}

	public function down()
	{
        $this->forge->dropTable($this->table);
	}

}