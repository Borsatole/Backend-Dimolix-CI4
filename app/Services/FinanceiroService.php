<?php

namespace App\Services;

use App\Models\FinanceiroModel;
use App\Models\CategoriaFinanceiroModel;

use App\Exceptions\MessagesException;
use Config\Database;
helper('financeiro');


class FinanceiroService
{

    private FinanceiroModel $model;
    private CategoriaFinanceiroModel $categoriaModel;

    private $db;

    public function __construct()
    {
        $this->model = new FinanceiroModel();

        $this->categoriaModel = new CategoriaFinanceiroModel();

        $this->db = Database::connect();
    }

    // private function buscarFormasPagamento(): array
    // {
    //     $FormasDePagamento = include(APPPATH . 'Config/FormasPagamento.php');
    //     return $FormasDePagamento;
    // }


    public function listar(array $params): array
    {
        $campoData = 'data_movimentacao';

        $categorias = $this->categoriaModel
            ->where('tipo', 'entrada')
            ->listar();

        $categorias_saida = $this->categoriaModel
            ->where('tipo', 'saida')
            ->listar();


        $registrosAno = $this->model
            ->where('status', 'concluido')
            ->where('YEAR(data_movimentacao)', date('Y', strtotime($params['data_minima'])))
            ->listar();



        $config = config('FormasPagamento');
        $formasPagamento = $config->formas;

        // TABELA (com filtros)
        $registro = isset($params['pagina'], $params['limite'])
            ? $this->model->listarComCategoria()->listarComPaginacao($params, $campoData)
            : $this->model->listarComCategoria()->listarSemPaginacao($params, $campoData);


        // STATS (somente periodo)
        $paramsStats = [
            'data_minima' => $params['data_minima'] ?? null,
            'data_maxima' => $params['data_maxima'] ?? null,
        ];

        $statsRegistros = $this->model
            ->listarComCategoria()
            ->listarSemPaginacao($paramsStats, $campoData);

        $soma = $this->somarTotal($statsRegistros['registros']);

        $somarTotalPorCategoria = $this->somarTotalPorCategoria($statsRegistros['registros']);

        $somarTotalAnoSelecionado = $this->somarTotalAnoSelecionado($registrosAno);




        $registro['totalPorPeriodo'] = $soma;
        $registro['totalPorCategoria'] = $somarTotalPorCategoria;
        $registro['totalAnoSelecionado'] = $somarTotalAnoSelecionado;
        $registro['categorias'] = $categorias;
        $registro['categorias_saida'] = $categorias_saida;
        $registro['formasPagamento'] = $formasPagamento;


        return $registro;
    }


    private function somarTotal(array $registros): array
    {

        $Entradas = 0;
        $NumeroEntradas = 0;
        $Saidas = 0;
        $NumeroSaidas = 0;

        foreach ($registros as $value) {

            if ($value['status'] !== 'concluido') {
                continue;
            }

            if ($value['tipo_movimentacao'] === 'entrada') {
                $Entradas += $value['valor'];
                $NumeroEntradas++;
            } else {
                $Saidas += $value['valor'];
                $NumeroSaidas++;
            }
        }


        $lucro = $Entradas - $Saidas;
        return [
            'Entradas' => $Entradas,
            'NumerodeEntradas' => $NumeroEntradas,
            'Saidas' => $Saidas,
            'NumerodeSaidas' => $NumeroSaidas,
            'Lucro' => $lucro
        ];

    }

    private function somarTotalPorCategoria(array $registros): array
    {
        $entradas = [];
        $saidas = [];

        foreach ($registros as $value) {

            if ($value['status'] !== 'concluido') {
                continue;
            }

            $categoria = $value['categoria'];
            $valor = (float) $value['valor'];

            if ($value['tipo_movimentacao'] === 'entrada') {

                if (!isset($entradas[$categoria])) {
                    $entradas[$categoria] = 0;
                }

                $entradas[$categoria] += $valor;

            } else {

                if (!isset($saidas[$categoria])) {
                    $saidas[$categoria] = 0;
                }

                $saidas[$categoria] += $valor;
            }
        }

        // 🔥 ordenar do maior para o menor
        arsort($entradas);
        arsort($saidas);

        return [
            'entradas' => $entradas,
            'saidas' => $saidas
        ];
    }


    private function somarTotalAnoSelecionado(array $registros): array
    {
        $nomesMeses = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        ];

        $meses = [];

        for ($i = 1; $i <= 12; $i++) {
            $meses[$i] = [
                'mes' => $nomesMeses[$i],
                'entrada' => 0,
                'saida' => 0
            ];
        }

        foreach ($registros as $value) {

            if ($value['status'] !== 'concluido') {
                continue;
            }

            $mes = (int) date('n', strtotime($value['data_movimentacao']));
            $valor = (float) $value['valor'];

            if ($value['tipo_movimentacao'] === 'entrada') {
                $meses[$mes]['entrada'] += $valor;
            } else {
                $meses[$mes]['saida'] += $valor;
            }
        }

        // remove índices numéricos e retorna array normal
        return array_values($meses);
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

        $permitidos = $this->model->allowedFields;
        $dadosCriar = $this->filtrarCamposPermitidos($dados, $permitidos);

        $dadosCriar['status'] ?? $dadosCriar['status'] = 'pendente';
        $dadosCriar['data_movimentacao'] ?? $dadosCriar['data_movimentacao'] = date('Y-m-d');

        $this->validarCampoObrigatorio($dadosCriar, 'data_movimentacao');
        $this->validarCampoObrigatorio($dadosCriar, 'tipo_movimentacao');
        $this->validarCampoObrigatorio($dadosCriar, 'valor');

        if (empty($dadosCriar)) {
            throw MessagesException::erroCriar(['Nenhum campo válido foi enviado.']);
        }

        if (!$this->model->criar($dadosCriar)) {
            throw MessagesException::erroCriar($this->model->errors());
        }

        $id = $this->model->getInsertID();

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            throw MessagesException::erroAtualizar(['Erro na transação']);
        }
        return $this->buscar($id);
    }

    public function atualizar(int $id, array $dados): array
    {

        $registro = $this->model->buscarPorId($id)
            ?? throw MessagesException::naoEncontrado($id);

        $permitidos = $this->model->allowedFields;

        $dadosAtualizar = $this->filtrarCamposPermitidos($dados, $permitidos);

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


        if (!$this->model->deletar($id)) {
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

