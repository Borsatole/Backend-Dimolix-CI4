<?php

namespace App\Libraries;

class Schedule
{
    private array $tarefas = [];

    public function comando(string $command)
    {
        return new Tarefa($command, $this->tarefas);
    }

    public function getTarefas()
    {
        return $this->tarefas;
    }

    public function adicionar(array $tarefa)
    {
        $this->tarefas[] = $tarefa;
    }
}