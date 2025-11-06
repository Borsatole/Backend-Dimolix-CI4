<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EnderecosSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'cliente_id'   => 6063, // certifique-se que existe o cliente ID 1
                'cep'          => '12345-678',
                'logradouro'   => 'Rua das Flores',
                'numero'       => '100',
                'complemento'  => 'Casa 1',
                'bairro'       => 'Centro',
                'cidade'       => 'São Paulo',
                'estado'       => 'SP',
            ],
            [
                'cliente_id'   => 6064, // certifique-se que existe o cliente ID 2
                'cep'          => '98765-432',
                'logradouro'   => 'Avenida Brasil',
                'numero'       => '200',
                'complemento'  => 'Bloco B, Apto 302',
                'bairro'       => 'Jardim América',
                'cidade'       => 'Rio de Janeiro',
                'estado'       => 'RJ',
            ],
        ];

        // Insere todos de uma vez
        $this->db->table('enderecos')->insertBatch($data);
    }
}
