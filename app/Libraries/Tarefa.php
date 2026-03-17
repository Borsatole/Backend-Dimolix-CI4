<?php

namespace App\Libraries;

class Tarefa
{
    private $command;
    private $tarefas;

    public function __construct($command, &$tarefas)
    {
        $this->command = $command;
        $this->tarefas = &$tarefas;
    }

    public function todoDiaAs($hora)
    {
        $this->tarefas[] = [
            'tipo' => 'diario',
            'hora' => $hora,
            'command' => $this->command
        ];
    }

    public function todaSemana($dia, $hora)
    {
        $this->tarefas[] = [
            'tipo' => 'semanal',
            'dia' => $dia,
            'hora' => $hora,
            'command' => $this->command
        ];
    }

    public function todoMes($dia, $hora)
    {
        $this->tarefas[] = [
            'tipo' => 'mensal',
            'dia' => $dia,
            'hora' => $hora,
            'command' => $this->command
        ];
    }

    public function aCadaMinutos($minutos)
    {
        $this->tarefas[] = [
            'tipo' => 'intervalo',
            'minutos' => $minutos,
            'command' => $this->command
        ];
    }
}