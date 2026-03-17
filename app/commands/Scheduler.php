<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\Schedule;
use App\Config\Rotinas;

class Scheduler extends BaseCommand
{
    protected $group = 'Geral';
    protected $name = 'cron:rotinas';

    public function run(array $params)
    {
        $schedule = new Schedule();

        // DECLARA AS ROTINAS

        Rotinas::registrar($schedule);


        $this->executar($schedule->getTarefas());
    }

    private function executar($tarefas)
    {
        $horaAtual = date('H:i');
        $diaSemana = strtolower(date('l'));
        $diaMes = date('j');

        $dias = [
            'monday' => 'segunda',
            'tuesday' => 'terca',
            'wednesday' => 'quarta',
            'thursday' => 'quinta',
            'friday' => 'sexta',
            'saturday' => 'sabado',
            'sunday' => 'domingo'
        ];

        foreach ($tarefas as $tarefa) {

            switch ($tarefa['tipo']) {

                case 'diario':

                    if ($horaAtual === $tarefa['hora']) {
                        CLI::write("Executando {$tarefa['command']}");
                        command($tarefa['command']);
                    }

                    break;

                case 'semanal':

                    if (
                        $horaAtual === $tarefa['hora'] &&
                        $dias[$diaSemana] === $tarefa['dia']
                    ) {

                        CLI::write("Executando {$tarefa['command']}");
                        command($tarefa['command']);
                    }

                    break;

                case 'mensal':

                    if (
                        $horaAtual === $tarefa['hora'] &&
                        $diaMes == $tarefa['dia']
                    ) {

                        CLI::write("Executando {$tarefa['command']}");
                        command($tarefa['command']);
                    }

                    break;

                case 'intervalo':

                    $minutoAtual = date('i');

                    if ($minutoAtual % $tarefa['minutos'] == 0) {

                        CLI::write("Executando {$tarefa['command']}");
                        command($tarefa['command']);

                    }

                    break;
            }


        }
    }
}