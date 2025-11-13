<?php

namespace App\Exceptions;

use Exception;

class EnderecoException extends Exception
{
    public static function naoEncontrado(): self
    {
        return new self('Endereço não encontrado', 404);
    }

    public static function idClienteObrigatorio(): self
    {
        return new self('O campo id_cliente é obrigatório', 400);
    }
    

    public static function erroCriar(array $errors = []): self
    {
        $message = 'Erro ao criar endereço';
        if (!empty($errors)) {
            $message .= ': ' . implode(', ', $errors);
        }
        return new self($message, 400);
    }

    public static function erroAtualizar(array $errors = []): self
    {
        $message = 'Erro ao atualizar cliente';
        if (!empty($errors)) {
            $message .= ': ' . implode(', ', $errors);
        }
        return new self($message, 400);
    }

    public static function erroDeletar(): self
    {
        return new self('Erro ao deletar cliente', 400);
    }
}