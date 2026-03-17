<?php

namespace App\Config;

use App\Libraries\Schedule;


/*
    lembre-se: UTC o cpanel tem uma diferença de 3 horas para o fuso horário
    ou seja o fuso horário brasileiro eh -3 horas

    6:00 da manha do Brasil eh 9:00 da manha do Cpanel
*/


class Rotinas
{
    public static function registrar(Schedule $schedule)
    {
        $schedule->comando('limpeza:limpar-logs')
            ->aCadaMinutos(60);

        // $schedule->comando('financeiro:gerar-notificacao')
        //     ->todoDiaAs('6:00');
        $schedule->comando('financeiro:gerar-notificacao')
            ->aCadaMinutos(1);

        $schedule->comando('financeiro:gerar-contas')
            ->todoMes(1, '6:00');


    }
}


/*
|--------------------------------------------------------------------------
| Scheduler - Métodos disponíveis
|--------------------------------------------------------------------------
|
| Exemplos de uso:
|
| $schedule->comando('nome:comando')->metodo();
|
| -----------------------------
| EXECUÇÃO POR MINUTOS
| -----------------------------
|
| todoMinuto()
| Executa a cada minuto
|
| $schedule->comando('email:processar')
|     ->todoMinuto();
|
|
| aCadaMinutos(int $minutos)
| Executa a cada X minutos
|
| $schedule->comando('email:processar')
|     ->aCadaMinutos(5);
|
|
| -----------------------------
| EXECUÇÃO POR HORAS
| -----------------------------
|
| todaHora()
| Executa a cada hora
|
| $schedule->comando('sistema:limpar-cache')
|     ->todaHora();
|
|
| aCadaHoras(int $horas)
| Executa a cada X horas
|
| $schedule->comando('backup:incremental')
|     ->aCadaHoras(6);
|
|
| -----------------------------
| EXECUÇÃO DIÁRIA
| -----------------------------
|
| todoDia()
| Executa todos os dias
|
| $schedule->comando('sistema:verificar')
|     ->todoDia();
|
|
| todoDiaAs(string $hora)
| Executa todos os dias em um horário específico
|
| $schedule->comando('financeiro:gerar-contas')
|     ->todoDiaAs('00:01');
|
|
| -----------------------------
| EXECUÇÃO SEMANAL
| -----------------------------
|
| todaSemana(string $dia, string $hora)
| Executa uma vez por semana
|
| Dias aceitos:
| segunda, terca, quarta, quinta, sexta, sabado, domingo
|
| $schedule->comando('financeiro:backup')
|     ->todaSemana('domingo', '03:00');
|
|
| -----------------------------
| EXECUÇÃO MENSAL
| -----------------------------
|
| todoMes(int $dia, string $hora)
| Executa uma vez por mês
|
| $schedule->comando('financeiro:relatorio')
|     ->todoMes(1, '00:01');
|
|
| -----------------------------
| EXECUÇÃO POR INTERVALO DE DIAS
| -----------------------------
|
| aCadaDias(int $dias)
| Executa a cada X dias
|
| $schedule->comando('sistema:limpeza')
|     ->aCadaDias(15);
|
|
| -----------------------------
| EXEMPLO COMPLETO
| -----------------------------
|
| $schedule->comando('financeiro:gerar-contas')
|     ->todoDiaAs('00:01');
|
| $schedule->comando('financeiro:gerar-notificacao')
|     ->todoDiaAs('09:32');
|
| $schedule->comando('financeiro:backup')
|     ->todaSemana('domingo', '03:00');
|
| $schedule->comando('financeiro:relatorio')
|     ->todoMes(1, '00:01');
|
*/