<?php

namespace App\Services;

use App\Models\LocacoesModel;
use App\Models\ItensLocacoesModel;
use App\Models\AgendamentosModel;
use App\Models\FinanceiroModel;
use App\Models\ClienteModel;
use App\Exceptions\MessagesException;
use Config\Database;

class LocacoesService
{
    private LocacoesModel $model;
    private ItensLocacoesModel $itemModel;
    private AgendamentosModel $agendamentosModel;
    private FinanceiroModel $financeiroModel;
    private ClienteModel $clienteModel;
    private $db;

    public function __construct()
    {
        $this->model = new LocacoesModel();
        $this->itemModel = new ItensLocacoesModel();
        $this->agendamentosModel = new AgendamentosModel();
        $this->financeiroModel = new FinanceiroModel();
        $this->clienteModel = new ClienteModel();
        $this->db = Database::connect();
    }


    public function listar(array $params): array
    {
        $registro = isset($params['pagina'], $params['limite'])
            ? $this->model->listarComPaginacao($params)
            : $this->model->listarSemPaginacao($params);

        $itens = $registro['registros'];

        $itensProcessados = [];

        foreach ($itens as $item) {
            if (!isset($item['id'])) {
                $item['dados_locacao'] = [];
                $itensProcessados[] = $item;
                continue;
            }

            $item['dados_locacao'] = $this->model->buscarLocacaoPorItemId($item['locacao_item_id']) ?? [];
            $itensProcessados[] = $item;
        }

        $itens = $itensProcessados;
        $registro['registros'] = $itens;



        return $registro;

    }


    public function buscar(int $id): array
    {
        $registro = $this->model->buscarPorId($id);

        if (!$registro) {
            throw MessagesException::naoEncontrado($id);
        }

        return $registro;
    }


    public function criar(array $dados): array
    {
        $this->db->transStart();

        // 🔹 SE VEIO DE AGENDAMENTO
        if (isset($dados['agendamento_id'])) {

            $agendamento = $this->agendamentosModel->buscarPorId($dados['agendamento_id']);

            if (!$agendamento) {
                throw MessagesException::erroGenerico('Agendamento não encontrado.');
            }

            if ($agendamento['data_inicio'] > date('Y-m-d H:i:s')) {
                throw MessagesException::erroGenerico('Agendamento ainda não pode ser convertido em locação. Data de início é no futuro.');
            }

            $categoriaItem = $agendamento['categoria_item'] ?? null;

            $itemDisponivel = $this->
                buscarItemNaoLocadoPorCategoria($categoriaItem);

            if (!$itemDisponivel) {
                throw MessagesException::erroGenerico('Todos os itens estão locados.');
            }

            // monta dados da locação automaticamente
            $dadosCriar = [
                'locacao_item_id' => $itemDisponivel['id'],
                'cliente_id' => $agendamento['cliente_id'],
                'endereco_id' => $agendamento['endereco_id'],
                'data_inicio' => $agendamento['data_inicio'],
                'data_fim' => $agendamento['data_fim'],
                'preco_total' => $agendamento['preco_total'],
                'forma_pagamento' => $agendamento['forma_pagamento'],
                'observacoes' => $agendamento['observacoes'],
                'agendamento_id' => $agendamento['id'],
            ];

        } else {

            $this->validarCampoObrigatorio($dados, 'locacao_item_id');

            $permitidos = $this->model->allowedFields;
            $dadosCriar = $this->filtrarCamposPermitidos($dados, $permitidos);

            if (empty($dadosCriar)) {
                throw MessagesException::erroCriar(['Nenhum campo válido foi enviado.']);
            }

            $locacao_item_id = $dadosCriar['locacao_item_id'];

            if ($this->model->verificaSeJaEstaLocado($locacao_item_id)) {
                throw MessagesException::erroGenerico('Item já está locado.');
            }
        }

        // 🔹 CRIA LOCAÇÃO
        if (!$this->model->criar($dadosCriar)) {
            throw MessagesException::erroCriar($this->model->errors());
        }

        $locacao_item_id = $dadosCriar['locacao_item_id'];

        // 🔹 MUDA STATUS DO ITEM
        $this->itemModel->mudarStatusItem($locacao_item_id, 'locado');

        $id = $this->model->getInsertID();

        if (isset($agendamento)) {
            $this->agendamentosModel->deletar($agendamento['id']);
        }

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            throw MessagesException::erroAtualizar(['Erro na transação']);
        }

        return $this->buscar($id);
    }

    public function buscarItemNaoLocadoPorCategoria(string $categoria)
    {
        return $this->itemModel->where('categoria', $categoria)
            ->where('status', 'disponivel')
            ->first();
    }


    public function atualizar(int $id, array $dados): array
    {

        $registro = $this->model->buscarPorId($id)
            ?? throw MessagesException::naoEncontrado($id);

        $permitidos = $this->model->allowedFields;

        $lancarFinanceiro = $dados['lancar_financeiro'] ?? true;

        $cliente = $this->clienteModel->buscarPorId($registro['cliente_id']);
        $item = $this->itemModel->buscarPorId($registro['locacao_item_id']);

        $dadosAtualizar = $this->filtrarCamposPermitidos($dados, $permitidos);

        $status = $dadosAtualizar['status'] ?? null;


        if ($status === 'finalizado') {
            $locacao_item_id = $registro['locacao_item_id'];
            $this->itemModel->mudarStatusItem($locacao_item_id, 'disponivel');

            if ($lancarFinanceiro) {
                $this->financeiroModel->insert([
                    'descricao' => mb_strtolower("locacao {$cliente['nome']} {$item['categoria']}"),
                    'id_categoria' => '1',
                    'data_movimentacao' => date('Y-m-d'),
                    'valor' => $registro['preco_total'],
                    'tipo_movimentacao' => 'entrada',
                    'forma_pagamento' => $registro['forma_pagamento'],
                    'status' => 'concluido'
                ]);
            }

        }

        if (empty($dadosAtualizar)) {
            throw MessagesException::erroAtualizar(['Nenhum campo válido foi enviado.']);
        }

        if (!$this->model->atualizar($id, $dadosAtualizar)) {
            throw MessagesException::erroAtualizar($this->model->errors());
        }



        return $this->buscar($id);
    }

    public function deletar(int $id): bool
    {
        $this->db->transStart();

        $registro = $this->model->buscarPorId($id)
            ?? throw MessagesException::naoEncontrado($id);

        $locacao_item_id = $registro['locacao_item_id'];

        if (!$this->model->deletar($id)) {
            $this->db->transRollback();
            throw MessagesException::erroDeletar();
        }

        if (!$this->itemModel->mudarStatusItem($locacao_item_id, 'disponivel')) {
            $this->db->transRollback();
            throw MessagesException::erroDeletar();
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw MessagesException::erroDeletar();
        }

        return true;
    }

    private function validarCampoObrigatorio(array $dados, string $campo): void
    {
        if (empty($dados[$campo])) {
            throw MessagesException::campoObrigatorio($campo);
        }
    }

    private function filtrarCamposPermitidos(array $dados, array $permitidos): array
    {
        return array_intersect_key($dados, array_flip($permitidos));
    }
}

