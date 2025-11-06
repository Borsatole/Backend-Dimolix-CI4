<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;


class LocacoesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'cliente_id' => 1,
                'locacao_item_id' => 1,
                'endereco_id' => 1,
                'data_inicio' => '2025-11-01',
                'data_fim' => '2025-11-05',
                'preco_total' => 100.00,
                'forma_pagamento' => 'debito',
                'observacoes' => 'Sem observações',
                'status' => 'ativo',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'cliente_id' => 2,
                'locacao_item_id' => 2,
                'endereco_id' => 2,
                'data_inicio' => '2025-11-02',
                'data_fim' => '2025-11-06',
                'preco_total' => 150.00,
                'forma_pagamento' => 'credito',
                'observacoes' => '',
                'status' => 'ativo',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'cliente_id' => 1,
                'locacao_item_id' => 1,
                'endereco_id' => 1,
                'data_inicio' => '2025-11-02',
                'data_fim' => '2025-11-06',
                'preco_total' => 150.00,
                'forma_pagamento' => 'credito',
                'observacoes' => '',
                'status' => 'finalizado',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('locacoes')->insertBatch($data);
    }
}
