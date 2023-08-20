<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Articles extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'author_id' => [
                'type' => 'INT',
                'constraint' => 255,
                'null' => false
            ],
            'content' => [
                'type' => 'TEXT',
                'null' => false
            ],
            'image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 255,
                'null' => false
            ],
            'views' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'default' => 0
            ],
            'published_on' => [
                'type' => 'TIMESTAMP',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP'
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('articles');
    }


    public function down()
    {
        //
    }
}
