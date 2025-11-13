<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\PaginacaoTrait;
use App\Traits\CrudTrait;

class ClienteModel extends Model
{
    use PaginacaoTrait;
    use CrudTrait;

    protected $table = 'clientes';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected array $casts = [
        'id' => 'int',
        
    ];

    protected $allowedFields = [
        'nome',
        'razao_social',
        'telefone',
        'celular',
        'email',
        'observacao',
        'created_at',
        'updated_at'
    ];

    // timestamps automáticos (opcional)
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // validações (opcional)
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete = ['deletarEnderecos'];


    // Callback para deletar endereços associados ao cliente na tabela enderecos
    protected function deletarEnderecos(array $data)
    {
        $enderecosModel = new \App\Models\EnderecosModel();

        $id = $data['id'] ?? null;

        if ($id !== null) {
            $clienteId = is_array($id) ? $id[0] : $id;
            $enderecosModel->deletarPorCliente($clienteId);
        }

        return $data;
    }






}


