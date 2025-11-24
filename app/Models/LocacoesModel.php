<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\PaginacaoTrait;
use App\Traits\CrudTrait;

class LocacoesModel extends Model
{
    use PaginacaoTrait, CrudTrait;
    protected $table = 'locacoes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'cliente_id',
        'locacao_item_id',
        'endereco_id',
        'data_inicio',
        'data_fim',
        'data_retirada',
        'preco_total',
        'forma_pagamento',
        'observacoes',
        'status',
    ];


    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id' => 'int',
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'cliente_id' => 'required|is_natural_no_zero',
        'locacao_item_id' => 'required|is_natural_no_zero',
        'endereco_id' => 'required|is_natural_no_zero',
        'preco_total' => 'required|numeric',
        'forma_pagamento' => 'in_list[dinheiro,credito,debito,pix,outro]',
    ];
    protected $validationMessages = [
        'cliente_id' => [
            'required' => 'Cliente é obrigatório'
        ],
        'locacao_item_id' => [
            'required' => 'Item de locação é obrigatório'
        ],
        'endereco_id' => [
            'required' => 'Endereço é obrigatório',
            'numeric' => 'Endereço deve ser um número válido'
        ],
        'forma_pagamento' => [
            'in_list' => 'Forma de pagamento deve ser um de: dinheiro, credito, debito, pix, outro'
        ],
        'data_inicio' => [
            'required' => 'Data de início é obrigatória'
        ],
        'data_fim' => [
            'required' => 'Data de fim é obrigatória'
        ],
        'preco_total' => [
            'required' => 'Preço total é obrigatório',
            'numeric' => 'Preço total deve ser um número válido'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];


    public function buscarLocacaoPorItemId(int $itemId): ?array
    {
        return $this->select('
            locacoes.id AS locacao_id,
            locacoes.cliente_id,
            locacoes.locacao_item_id,
            locacoes.endereco_id,
            locacoes.data_inicio,
            locacoes.data_fim,
            locacoes.data_retirada,
            locacoes.preco_total,
            locacoes.forma_pagamento,
            locacoes.observacoes,
            locacoes.status,
            locacoes.created_at,
            locacoes.updated_at,

            enderecos.cep,
            enderecos.logradouro,
            enderecos.numero,
            enderecos.complemento,
            enderecos.bairro,
            enderecos.cidade,
            enderecos.estado,

            clientes.nome AS cliente_nome,
            clientes.telefone AS cliente_telefone,
            itens_locacoes.item AS item_nome,
            itens_locacoes.categoria AS item_categoria,
        ')
            ->join('enderecos', 'enderecos.id = locacoes.endereco_id')
            ->join('clientes', 'clientes.id = locacoes.cliente_id')
            ->join('itens_locacoes', 'itens_locacoes.id = locacoes.locacao_item_id')
            ->where('locacoes.locacao_item_id', $itemId)
            ->where('locacoes.status', 'ativo')
            ->first();
    }


    public function verificaSeJaEstaLocado(int $itemId): ?array
    {
        return $this->select('locacoes.*')
            ->where('locacoes.locacao_item_id', $itemId)
            ->where('locacoes.status', 'ativo')
            ->first();
    }


}
