<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\PaginacaoTrait;
use App\Traits\CrudTrait;

class CategoriaFinanceiroModel extends Model
{
    use PaginacaoTrait;
    use CrudTrait;
    protected $table = 'financeiro_categorias';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'categoria_item',
        'tipo'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id' => 'int',
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id' => 'permit_empty|is_natural_no_zero',
        'categoria_item' => 'required|string|max_length[255]|is_unique[financeiro_categorias.categoria_item,id,{id}]',
        'tipo' => 'required|in_list[entrada,saida]',
    ];
    protected $validationMessages = [
        'categoria_item' => [
            'required' => 'O campo {field} é obrigatório.',
            'string' => 'O campo {field} deve ser uma string.',
            'max_length' => 'O campo {field} deve ter no máximo {param} caracteres.',
            'is_unique' => 'O campo {field} deve ser exclusivo.',
        ],
        'tipo' => [
            'required' => 'O campo {field} é obrigatório.',
            'in_list' => 'O campo {field} deve ser "entrada" ou "saida".',
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
}
