<?php

return [
    1 => [ // Administrador
        ['id' => 1, 'nome' => 'Dashboard', 'rota' => '/', 'icone' => 'dashboard'],
        ['id' => 7, 'nome' => 'Demandas', 'rota' => '/demandas', 'icone' => 'demandas'],
        ['id' => 2, 'nome' => 'Locacoes', 'rota' => '/estoque', 'icone' => 'estoque'],
        ['id' => 3, 'nome' => 'Clientes', 'rota' => '/clientes', 'icone' => 'clientes'],
        [
            'id' => 4,
            'nome' => 'Financeiro',
            'rota' => '/financeiro',
            'icone' => 'financeiro',
            'submenu' => [
                [
                    'id' => 41,
                    'nome' => 'Visão geral',
                    'rota' => '/financeiro',
                ],
                [
                    'id' => 42,
                    'nome' => 'Contas a pagar',
                    'rota' => '/financeiro/pagar',
                ],
                [
                    'id' => 43,
                    'nome' => 'Contas a receber',
                    'rota' => '/financeiro/receber',
                ],
                [
                    'id' => 44,
                    'nome' => 'Categorias',
                    'rota' => '/financeiro/categorias',
                    'icone' => 'categorias',
                ],
                [
                    'id' => 45,
                    'nome' => 'Contas Fixas',
                    'rota' => '/financeiro/contas-fixas',
                    'icone' => 'contasfixas',
                ],
            ],
        ],
        // ['id' => 6, 'nome' => 'Níveis de Usuários', 'rota' => '/acesso-niveis', 'icone' => 'permissoes'],



    ],

    2 => [ // Padrão
        ['id' => 1, 'nome' => 'Dashboard', 'rota' => '/', 'icone' => 'dashboard'],
        ['id' => 7, 'nome' => 'Demandas', 'rota' => '/demandas', 'icone' => 'demandas'],
        ['id' => 2, 'nome' => 'Locacoes', 'rota' => '/estoque', 'icone' => 'estoque'],
        ['id' => 3, 'nome' => 'Clientes', 'rota' => '/clientes', 'icone' => 'clientes'],
    ],
];
