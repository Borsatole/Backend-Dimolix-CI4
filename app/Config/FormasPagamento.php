<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class FormasPagamento extends BaseConfig
{
    public array $formas = [
        'Pix',
        'Dinheiro',
        'Boleto',
        'Cartão de Crédito',
        'Cartão de Débito'
    ];
}
