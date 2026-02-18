<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\PaginacaoTrait;
use App\Traits\CrudTrait;

class FinanceiroModel extends Model
{
    use PaginacaoTrait;
    use CrudTrait;

    protected $table = 'financeiro';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'descricao',
        'id_categoria',
        'data_movimentacao',
        'valor',
        'tipo_movimentacao',
        'forma_pagamento',
        'observacoes',
        'status',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id' => 'int',
        'id_categoria' => 'int',
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
        'descricao' => 'required|string|max_length[255]',
        'id_categoria' => 'required|integer',
        'data_movimentacao' => 'required|valid_date',
        'valor' => 'required|decimal',
        'tipo_movimentacao' => 'required|in_list[entrada,saida]',
        'forma_pagamento' => 'required|string|max_length[255]',
        'observacoes' => 'permit_empty|string',
        'status' => 'required|in_list[pendente,concluido,cancelado]',
    ];
    protected $validationMessages = [
        'descricao' => [
            'required' => 'O campo "Descrição" é obrigatório.',
            'max_length' => 'O campo "Descrição" deve ter no máximo 255 caracteres.',
        ],
        'id_categoria' => [
            'required' => 'O campo "Categoria" é obrigatório.',
            'integer' => 'O campo "Categoria" deve ser um número inteiro.',
        ],
        'data_movimentacao' => [
            'required' => 'O campo "Data de Movimentação" é obrigatório.',
            'valid_date' => 'O campo "Data de Movimentação" deve ser uma data válida.',
        ],
        'valor' => [
            'required' => 'O campo "Valor" é obrigatório.',
            'decimal' => 'O campo "Valor" deve ser um número decimal.',
        ],
        'tipo_movimentacao' => [
            'required' => 'O campo "Tipo de Movimentação" é obrigatório.',
            'in_list' => 'O campo "Tipo de Movimentação" deve ser "entrada" ou "saida".',
        ],
        'forma_pagamento' => [
            'required' => 'O campo "Forma de Pagamento" é obrigatório.',
            'max_length' => 'O campo "Forma de Pagamento" deve ter no máximo 255 caracteres.',
        ],
        'observacoes' => [
            'string' => 'O campo "Observações" deve ser uma string.',
        ],
        'status' => [
            'required' => 'O campo "Status" é obrigatório.',
            'in_list' => 'O campo "Status" deve ser "pendente", "concluido" ou "cancelado".',
        ],
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



    public function listarComCategoria()
    {
        return $this->select('financeiro.*, 
        financeiro_categorias.categoria_item as categoria')
            ->join(
                'financeiro_categorias',
                'financeiro_categorias.id = financeiro.id_categoria',
                'left'
            );
    }





}


