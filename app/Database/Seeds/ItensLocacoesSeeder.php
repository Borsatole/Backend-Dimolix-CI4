<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ItensLocacoesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'item' => 'Caçamba',
                'categoria' => 'Caçamba 5m²',
                'preco_diaria' => 100.00,
                'status' => 'disponivel',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'item' => 'Caçamba',
                'categoria' => 'Caçamba 5m²',
                'preco_diaria' => 100.00,
                'status' => 'disponivel',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'item' => 'Caçamba',
                'categoria' => 'Caçamba 5m²',
                'preco_diaria' => 100.00,
                'status' => 'disponivel',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'item' => 'Container',
                'categoria' => 'Container 3m²',
                'preco_diaria' => 200.00,
                'status' => 'disponivel',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'item' => 'Caçamba',
                'categoria' => 'Caçamba 4m²',
                'preco_diaria' => 150.00,
                'status' => 'disponivel',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'item' => 'Caçamba',
                'categoria' => 'Caçamba 4m²',
                'preco_diaria' => 150.00,
                'status' => 'disponivel',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'item' => 'Caçamba',
                'categoria' => 'Caçamba 4m²',
                'preco_diaria' => 150.00,
                'status' => 'disponivel',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'item' => 'Container',
                'categoria' => 'Container 3m²',
                'preco_diaria' => 200.00,
                'status' => 'disponivel',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'item' => 'Container',
                'categoria' => 'Container 3m²',
                'preco_diaria' => 200.00,
                'status' => 'disponivel',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'item' => 'Container',
                'categoria' => 'Container 4m²',
                'preco_diaria' => 200.00,
                'status' => 'disponivel',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('itens_locacoes')->insertBatch($data);
    }
}
