<?php

namespace App\Services;

use App\Models\ClienteModel;        
use App\Exceptions\ClienteException;

class ClienteService
{
    private $clientesModel;
    private $db;

    public function __construct()
    {
        $this->clientesModel = new ClienteModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Lista todos os níveis com paginação e permissões
     */
    public function listar(int $limite = 10, int $pagina = 1, array $filtros = [], ?string $data_inicio = null, ?string $data_fim = null): array
    {
        $registros = $this->clientesModel->listarComPaginacao($limite, $pagina, $filtros, $data_inicio, $data_fim);


        return $registros;
    }

    /**
     * Busca um nível específico com suas permissões
     */
    public function buscar(int $id): array
    {
        $nivel = $this->clientesModel->buscarPorId($id);

        if (!$nivel) {
            throw ClienteException::naoEncontrado();
        }

        return $nivel;
    }

    /**
     * Cria um novo nível com todas as permissões (allow = 0)
     * 
     * AQUI é onde a ORQUESTRAÇÃO acontece:
     * 1. Valida
     * 2. Cria nível
     * 3. Vincula permissões
     * 4. Retorna nível completo
     */
    public function criar(array $dados): array
    {
        // 1️⃣ Validação de negócio
        if (empty($dados['nome'])) {
            throw ClienteException::nomeObrigatorio();
        }

        // 3️⃣ Inicia transação (garante atomicidade)
        $this->db->transStart();

        try {
            // 4️⃣ Cria o nível (Model apenas insere)
            $nivelId = $this->clientesModel->criar(['nome' => $dados['nome']]);

            if (!$nivelId) {
                throw ClienteException::erroCriar($this->clientesModel->errors());
            }

            

            // 6️⃣ Finaliza transação
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw ClienteException::erroCriar();
            }

            // 7️⃣ Retorna o nível completo com permissões
            return $this->buscar($nivelId);

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Atualiza um nível existente (nome e/ou permissões)
     */
    public function atualizar(int $id, array $dados): array
    {
        // 1️⃣ Validação
        if (empty($dados['nome'])) {
            throw ClienteException::nomeObrigatorio();
        }

        // 2️⃣ Verifica se existe
        $nivelExistente = $this->clientesModel->buscarPorId($id);
        if (!$nivelExistente) {
            throw ClienteException::naoEncontrado();
        }

        // 4️⃣ Inicia transação
        $this->db->transStart();

        try {
            // 5️⃣ Atualiza o nome
            if (!$this->clientesModel->atualizar($id, ['nome' => $dados['nome']])) {
                throw ClienteException::erroAtualizar($this->clientesModel->errors());
            }

        
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw ClienteException::erroAtualizar();
            }

            return $this->buscar($id);

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Deleta um nível e suas permissões
     */
    public function deletar(int $id): bool
    {
        // 1️⃣ Verifica se existe
        if (!$this->clientesModel->buscarPorId($id)) {
            throw ClienteException::naoEncontrado();
        }

        // 2️⃣ Inicia transação
        $this->db->transStart();

        try {
           

            // 4️⃣ Remove o nível
            if (!$this->clientesModel->deletar($id)) {
                throw ClienteException::erroDeletar();
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw ClienteException::erroDeletar();  
            }

            return true;

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

   
}