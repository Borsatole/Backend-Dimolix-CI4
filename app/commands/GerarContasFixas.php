<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\FinanceiroContasFixasModel;
use App\Models\FinanceiroModel;

class GerarContasFixas extends BaseCommand
{
    protected $group = 'Financeiro';
    protected $name = 'financeiro:gerar-contas';
    protected $description = 'Gera lançamentos das contas fixas do mês';

    public function run(array $params)
    {
        $contasModel = new FinanceiroContasFixasModel();
        $financeiroModel = new FinanceiroModel();

        $hoje = date('Y-m-d');
        $mesAtual = date('m');
        $anoAtual = date('Y');

        $contas = $contasModel
            ->groupStart()
            ->where('data_fim >=', $hoje)
            ->orWhere('data_fim', null)
            ->groupEnd()
            ->findAll();

        foreach ($contas as $conta) {

            $diaVencimento = (int) $conta['dia_vencimento'];

            $dataMovimentacao = sprintf(
                '%s-%s-%02d',
                $anoAtual,
                $mesAtual,
                $diaVencimento
            );

            // verifica se já gerou nesse mês
            if (!empty($conta['ultima_recorrencia'])) {

                $ultima = date('Y-m', strtotime($conta['ultima_recorrencia']));
                $atual = date('Y-m');

                if ($ultima === $atual) {
                    continue;
                }
            }

            $financeiroModel->insert([
                'descricao' => $conta['descricao'],
                'id_categoria' => $conta['id_categoria'],
                'data_movimentacao' => $dataMovimentacao,
                'valor' => $conta['valor'],
                'tipo_movimentacao' => 'saida',
                'forma_pagamento' => $conta['forma_pagamento'],
                'status' => 'pendente'
            ]);

            $contasModel->update($conta['id'], [
                'ultima_recorrencia' => $hoje
            ]);

            CLI::write("Conta gerada: {$conta['descricao']}", 'green');
        }

        CLI::write("Processo finalizado.", 'yellow');
    }
}
