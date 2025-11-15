<?php

namespace App\Services;

use App\Models\ItensLocacoesModel;
use App\Models\LocacoesModel;
use App\Exceptions\ItensLocacoesException;

class ItensLocacoesService
{
    private $itensLocacoesModel;
    private $locacoesModel;
    private $db;

    public function __construct()
    {
        $this->itensLocacoesModel = new ItensLocacoesModel();
        $this->locacoesModel = new LocacoesModel();

        $this->db = \Config\Database::connect();
    }

    public function listar(int $limite = 10, int $pagina = 1, array $filtros = [], ?string $data_inicio = null, ?string $data_fim = null): array
    {
        // Carrega os itens
        $registros = $this->itensLocacoesModel->listarSemPaginacao($filtros);
        $itens = $registros['registros'];

        $itensProcessados = [];


        // Adiciona dados de locacao
        foreach ($itens as $item) { // SEM &
            if (!isset($item['id'])) {
                $item['dados_locacao'] = [];
                $itensProcessados[] = $item;
                continue;
            }

            if ($item['status'] !== 'disponivel') {
                $item['dados_locacao'] = $this->locacoesModel->buscarLocacaoPorItemId($item['id']) ?? [];
            } else {
                $item['dados_locacao'] = [];
            }

            $itensProcessados[] = $item;
        }

        $itens = $itensProcessados;

        $registros = [
            'registros' => $itens,
        ];

        // ---------------------
        // AGRUPAMENTO POR CATEGORIA
        // ---------------------
        $agrupados = [];

        foreach ($itens as $item) {
            $categoria = $item['categoria'];

            if (!isset($agrupados[$categoria])) {
                $agrupados[$categoria] = [
                    'categoria' => $categoria,
                    'itens' => []
                ];
            }

            $agrupados[$categoria]['itens'][] = $item;
        }

        // // ---------------------
        // // ORDENAR CATEGORIAS POR ORDEM ALFABÉTICA (A → Z)
        // // ---------------------
        ksort($agrupados, SORT_NATURAL | SORT_FLAG_CASE);

        // ---------------------
        // ORDENAR ITENS DENTRO DE CADA CATEGORIA
        // ---------------------
        foreach ($agrupados as &$categoria) {
            usort($categoria['itens'], function ($a, $b) {

                $dataA = $a['dados_locacao']['data_fim'] ?? null;
                $dataB = $b['dados_locacao']['data_fim'] ?? null;

                // 1. Se ambos não têm locação → mantém ordem
                if (!$dataA && !$dataB)
                    return 0;

                // 2. Itens SEM locação vão para o final
                if (!$dataA)
                    return 1;
                if (!$dataB)
                    return -1;

                // 3. Ordenar por data_fim (a mais próxima primeiro)
                return strtotime($dataA) <=> strtotime($dataB);
            });

        }

        $registros['registros'] = array_values($agrupados);




        return $registros;
    }


    public function buscar(int $id): array
    {
        $registro = $this->itensLocacoesModel->buscarPorId($id);


        if (!$registro) {
            throw ItensLocacoesException::naoEncontrado();
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
            throw ItensLocacoesException::nomeObrigatorio();
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
            throw ItensLocacoesException::erroCriar(['Nenhum campo válido foi enviado.']);
        }

        // 🚀 Inicia transação
        $this->db->transStart();

        try {

            if (!$this->itensLocacoesModel->criar($dadosCriar)) {
                throw ItensLocacoesException::erroCriar($this->itensLocacoesModel->errors());
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw ItensLocacoesException::erroCriar();
            }

            // Retorna o registro recém criado
            return $this->buscar($this->itensLocacoesModel->getInsertID());

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }


    public function atualizar(int $id, array $dados): array
    {
        $registroExistente = $this->itensLocacoesModel->buscarPorId($id);

        if (!$registroExistente) {
            throw ItensLocacoesException::naoEncontrado();
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
            throw ItensLocacoesException::erroAtualizar(['Nenhum campo válido foi enviado para atualização.']);
        }

        // ✅ 3️⃣ Inicia transação
        $this->db->transStart();

        try {
            if (!$this->itensLocacoesModel->atualizar($id, $dadosAtualizar)) {
                throw ItensLocacoesException::erroAtualizar($this->itensLocacoesModel->errors());
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw ItensLocacoesException::erroAtualizar();
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
        if (!$this->itensLocacoesModel->buscarPorId($id)) {
            throw ItensLocacoesException::naoEncontrado();
        }

        // 2️⃣ Inicia transação
        $this->db->transStart();

        try {


            if (!$this->itensLocacoesModel->deletar($id)) {
                throw ItensLocacoesException::erroDeletar();
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw ItensLocacoesException::erroDeletar();
            }

            return true;

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }


}