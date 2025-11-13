<?php

namespace App\Traits;

trait CrudTrait
{
    public function buscarPorId(int $id): ?array
    {
        return $this->find($id);
    }

    public function criar(array $dados): int
    {
        return $this->insert($this->limparDados($dados));
    }

    public function atualizar(int $id, array $dados): bool
    {
        return $this->update($id, $this->limparDados($dados));
    }

    public function deletar(int $id): bool
    {
        return $this->delete($id);
    }

    private function limparDados(array $dados): array
    {
        foreach ($dados as $chave => $valor) {
            if ($valor === '' || $valor === ' ') {
                $dados[$chave] = null;
            }
        }
        return $dados;
    }
}

