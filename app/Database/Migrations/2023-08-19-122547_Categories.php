<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Categories extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 255,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ]
        ]);
    
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('names_table'); // Change 'names_table' if you want another name for the table
    }
    

    public function down()
    {
        //
    }
}
