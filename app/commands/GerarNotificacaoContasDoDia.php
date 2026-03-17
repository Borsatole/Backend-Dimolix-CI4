<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\FinanceiroModel;

helper('email');

class GerarNotificacaoContasDoDia extends BaseCommand
{

    protected $group = 'Financeiro';
    protected $name = 'financeiro:gerar-notificacao';
    protected $description = 'Envia notificação de contas vencendo hoje ou atrasadas';

    public function run(array $params)
    {
        $financeiroModel = new FinanceiroModel();

        $hoje = date('Y-m-d');
        $hojedatetime = date('Y-m-d H:i:s');

        // buscar contas pendentes
        $contas = $financeiroModel
            ->where('status', 'pendente')
            ->where('tipo_movimentacao', 'saida')
            ->where('data_movimentacao <=', $hoje)
            ->where('notificado_at', null)
            ->findAll();


        if (empty($contas)) {
            CLI::write('Nenhuma conta para notificar.', 'yellow');
            return;
        }

        $total = 0;
        $linhas = [];

        foreach ($contas as $conta) {

            $valor = (float) $conta['valor'];
            $total += $valor;
            $dataBrasileira = date('d/m/Y', strtotime($conta['data_movimentacao']));

            $linhas[] = "
                <tr style='border-bottom:1px solid #eee;'>

                    <td style='padding:8px;'>
                        {$conta['descricao']}
                    </td>

                    <td style='padding:8px; color:#555;'>
                        {$dataBrasileira}
                    </td>

                    <td style='padding:8px; text-align:right; font-weight:bold;'>
                        R$ " . number_format($valor, 2, ',', '.') . "
                    </td>

                </tr>";
        }

        $tabela = implode('', $linhas);

        $totalFormatado = number_format($total, 2, ',', '.');

        $adminEmail = env('ADMIN_EMAIL') ?? 'borsatole@gmail.com';

        // enviar email
        enviarEmailTemplate(
            'notificacao_contas',
            [
                'tabela_contas' => $tabela,
                'total_contas' => $totalFormatado
            ],
            $adminEmail,
            "📊 R$" . number_format($total, 2, ',', '.') . " em contas pendentes de pagamento hoje"
        );

        // marcar como notificadas
        $ids = array_column($contas, 'id');

        $financeiroModel
            ->builder()
            ->whereIn('id', $ids)
            ->update([
                'notificado_at' => date('Y-m-d H:i:s')
            ]);

        CLI::write(count($contas) . ' contas notificadas.', 'green');
    }
}