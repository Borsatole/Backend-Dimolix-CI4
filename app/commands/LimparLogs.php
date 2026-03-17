<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\FinanceiroContasFixasModel;
use App\Models\FinanceiroModel;

class LimparLogs extends BaseCommand
{
    protected $group = 'Limpeza';
    protected $name = 'limpeza:limpar-logs';
    protected $description = 'Apaga logs antigos';

    public function run(array $params)
    {
        CLI::write("Limpeza de logs iniciada.", 'yellow');

        $this->clearLogs();


        CLI::write("Processo finalizado.", 'yellow');
    }

    private function clearLogs()
    {
        // acessa a pasta de logs
        $logPath = WRITEPATH . 'logs/';

        // obtém a lista de arquivos na pasta
        $files = glob($logPath . '*');

        // exclui todos os arquivos
        foreach ($files as $file) {
            unlink($file);
        }
    }
}
