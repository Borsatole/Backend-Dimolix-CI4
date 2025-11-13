<?php

namespace App\Services;

use App\Models\EnderecosModel;
use App\Exceptions\EnderecoException;

class EnderecoService
{
    private $enderecosModel;
    private $db;

    public function __construct()
    {
        $this->enderecosModel = new EnderecosModel();

        $this->db = \Config\Database::connect();
    }


    public function listar(int $limite = 10, int $pagina = 1, array $filtros = [], ?string $data_inicio = null, ?string $data_fim = null): array
    {
        // Obtém os registros paginados (enderecos)
        $registros = $this->enderecosModel->listarComPaginacao($limite, $pagina, $filtros, $data_inicio, $data_fim);

        // Verifica se o resultado contém a chave 'registros' (caso seu método retorne com paginação)
        if (isset($registros['registros']) && is_array($registros['registros'])) {
            foreach ($registros['registros'] as &$cliente) {
                // Busca os endereços desse cliente
                $enderecos = $this->enderecosModel->buscarPorCliente($cliente['id']);
                // Adiciona o campo no cliente
                $cliente['enderecos'] = $enderecos ?? [];
            }
        }

        return $registros;
    }

    public function buscar(int $id): array
    {
        $registro = $this->enderecosModel->buscarPorId($id);
        $enderecos = $this->enderecosModel->buscarPorCliente($id);
        $registro['enderecos'] = $enderecos ?? [];
        
        if (!$registro) {
            throw EnderecoException::naoEncontrado();
        }

        return $registro;
    }

    public function criar(array $dados): array
    {
        // 1️⃣ Validação de negócio
        if (empty($dados['cliente_id'])) {
            throw EnderecoException::idClienteObrigatorio();    
        }

        // 3️⃣ Inicia transação (garante atomicidade)
        $this->db->transStart();

        try {
            $dadosEndereco = [
                'cliente_id' => $dados['cliente_id'] ?? null,
                'cep' => $dados['cep'] ?? null,
                'logradouro' => $dados['logradouro'] ?? null,
                'complemento' => $dados['complemento'] ?? null,
                'bairro' => $dados['bairro'] ?? null,
                'numero' => $dados['numero'] ?? null,
                'cidade' => $dados['cidade'] ?? null,
                'estado' => $dados['estado'] ?? null,
                
            ];

            $id = $this->enderecosModel->criar($dadosEndereco);

            if (!$id) {
                throw EnderecoException::erroCriar($this->enderecosModel->errors());
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw EnderecoException::erroCriar();
            }

            return $this->buscar($id);

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    public function atualizar(int $id, array $dados): array
    {
       
        // 2️⃣ Verifica se existe
        $enderecoExistente = $this->enderecosModel->buscarPorId($id);
        if (!$enderecoExistente) {
            throw EnderecoException::naoEncontrado();   
        }

        // 4️⃣ Inicia transação
        $this->db->transStart();

        try {
            $dadosEndereco = [
                'cep' => $dados['cep'] ?? null,
                'logradouro' => $dados['logradouro'] ?? null,
                'complemento' => $dados['complemento'] ?? null,
                'bairro' => $dados['bairro'] ?? null,
                'numero' => $dados['numero'] ?? null,
                'cidade' => $dados['cidade'] ?? null,
                'estado' => $dados['estado'] ?? null,
                
            ];

            if (!$this->enderecosModel->atualizar($id, $dadosEndereco)) {
                throw EnderecoException::erroAtualizar($this->enderecosModel->errors());
            }

        
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw EnderecoException::erroAtualizar();
            }

            return $this->buscar($id);

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    public function deletar(int $id): bool
    {
        // 1️⃣ Verifica se existe
        if (!$this->enderecosModel->buscarPorId($id)) {
            throw EnderecoException::naoEncontrado();
        }

        // 2️⃣ Inicia transação
        $this->db->transStart();

        try {
           

            // 4️⃣ Remove o nível
            if (!$this->enderecosModel->deletar($id)) {
                throw EnderecoException::erroDeletar();
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw EnderecoException::erroDeletar();  
            }

            return true;

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

   
}