<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ItensLocacoesMigration extends Migration
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
            'item' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
            ],
            'categoria' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
            ],
            'preco_diaria' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'status' => [
                'type'       => "ENUM('disponivel','locado','indisponivel')",
                'default'    => 'disponivel',
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('itens_locacoes');
    }

    public function down()
    {
        $this->forge->dropTable('itens_locacoes');
    }
}
