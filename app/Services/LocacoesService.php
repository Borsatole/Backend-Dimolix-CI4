<?php

namespace App\Services;

use App\Models\LocacoesModel;
use App\Exceptions\LocacoesException;

class LocacoesService
{
    private $locacoesModel;
    private $db;

    public function __construct()
    {
        $this->locacoesModel = new LocacoesModel();

        $this->db = \Config\Database::connect();
    }

    public function listar(int $limite = 10, int $pagina = 1, array $filtros = [], ?string $data_inicio = null, ?string $data_fim = null): array
    {
        // Carrega os itens
        $registros = $this->locacoesModel->listarComPaginacao(
            $limite,
            $pagina,
            $filtros
        );

        return $registros;
    }


    public function buscar(int $id): array
    {
        $registro = $this->locacoesModel->buscarPorId($id);


        if (!$registro) {
            throw LocacoesException::naoEncontrado();
        }

        if ($registro['status'] !== 'disponivel') {
            $locacao = $this->locacoesModel->buscarLocacaoPorItemId($id);

            $registro['dados_locacao'] = $locacao ?? [];
        }

        return $registro;
    }


    public function criar(array $dados): array
    {
        // 🔍 Valida campo obrigatório
        if (empty($dados['item'])) {
            throw LocacoesException::nomeObrigatorio();
        }

        // Campos permitidos para criação
        $camposPermitidos = [
            'item',
            'categoria',
            'preco_diaria',
            'status'
        ];

        $dadosCriar = [];

        // 🔄 Filtra apenas os campos permitidos
        foreach ($camposPermitidos as $campo) {
            if (isset($dados[$campo])) {
                $dadosCriar[$campo] = $dados[$campo];
            }
        }

        // 🔍 Se não tiver nenhum dado válido (não deve acontecer porque item é obrigatório)
        if (empty($dadosCriar)) {
            throw LocacoesException::erroCriar(['Nenhum campo válido foi enviado.']);
        }

        // 🚀 Inicia transação
        $this->db->transStart();

        try {

            if (!$this->locacoesModel->criar($dadosCriar)) {
                throw LocacoesException::erroCriar($this->locacoesModel->errors());
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw LocacoesException::erroCriar();
            }

            // Retorna o registro recém criado
            return $this->buscar($this->locacoesModel->getInsertID());

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }


    public function atualizar(int $id, array $dados): array
    {
        $registroExistente = $this->locacoesModel->buscarPorId($id);

        if (!$registroExistente) {
            throw LocacoesException::naoEncontrado();
        }

        $dadosAtualizar = [];

        $camposPermitidos = [
            'item',
            'categoria',
            'preco_diaria',
            'status'
        ];

        foreach ($camposPermitidos as $campo) {
            if (array_key_exists($campo, $dados) && $dados[$campo] !== null) {
                $dadosAtualizar[$campo] = $dados[$campo];
            }
        }

        // Se não houver nada para atualizar
        if (empty($dadosAtualizar)) {
            throw LocacoesException::erroAtualizar(['Nenhum campo válido foi enviado para atualização.']);
        }

        // ✅ 3️⃣ Inicia transação
        $this->db->transStart();

        try {
            if (!$this->locacoesModel->atualizar($id, $dadosAtualizar)) {
                throw LocacoesException::erroAtualizar($this->locacoesModel->errors());
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw LocacoesException::erroAtualizar();
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
        if (!$this->locacoesModel->buscarPorId($id)) {
            throw LocacoesException::naoEncontrado();
        }

        // 2️⃣ Inicia transação
        $this->db->transStart();

        try {


            if (!$this->locacoesModel->deletar($id)) {
                throw LocacoesException::erroDeletar();
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw LocacoesException::erroDeletar();
            }

            return true;

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }


}