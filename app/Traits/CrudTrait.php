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
        return $this->insert($dados, true);
    }

    public function atualizar(int $id, array $dados): bool
    {
        return $this->update($id, $dados);
    }

    public function deletar(int $id): bool
    {
        return $this->delete($id);
    }
}
