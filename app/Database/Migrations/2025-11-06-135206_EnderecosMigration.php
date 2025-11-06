<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnderecosMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'cliente_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'cep' => [
                'type' => 'VARCHAR',
                'constraint' => 9,
            ],
            'logradouro' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'numero' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'complemento' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'bairro' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'cidade' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'estado' => [
                'type' => 'VARCHAR',
                'constraint' => 2,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('cliente_id', 'clientes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('enderecos');
    }

    public function down()
    {
        $this->forge->dropTable('enderecos');
    }
}
