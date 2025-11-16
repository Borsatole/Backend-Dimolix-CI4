<?php

namespace App\Exceptions;

use Exception;

class LocacoesException extends Exception
{
    public static function naoEncontrado(): self
    {
        return new self('Locação não encontrada', 404);
    }

    public static function precoInvalido(): self
    {
        return new self(
            'O preço da diária deve ser um valor válido maior ou igual a zero.',
            400
        );
    }

    public static function statusInvalido(): self
    {
        return new self(
            'Status inválido. Valores permitidos: disponivel, alugado, manutencao, inativo.',
            400
        );
    }

    public static function itemMuitoLongo(): self
    {
        return new self(
            'O nome do item não pode ter mais de 255 caracteres.',
            400
        );
    }

    public static function campoObrigatorio($campo): self
    {
        return new self('O campo ' . $campo . ' é obrigatório', 400);
    }


    public static function erroCriar(array $errors = []): self
    {
        $message = 'Erro ao criar item locação';
        if (!empty($errors)) {
            $message .= ': ' . implode(', ', $errors);
        }
        return new self($message, 400);
    }

    public static function erroAtualizar(array $errors = []): self
    {
        $message = 'Erro ao atualizar Locação';
        if (!empty($errors)) {
            $message .= ': ' . implode(', ', $errors);
        }
        return new self($message, 400);
    }

    public static function erroDeletar(): self
    {
        return new self('Erro ao deletar locação', 400);
    }
}