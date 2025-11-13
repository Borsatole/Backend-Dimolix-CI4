<?php

namespace App\Services;

use App\Models\ClienteModel;
use App\Models\EnderecosModel;

use App\Exceptions\ClienteException;

class ClienteService
{
    private $clientesModel;
    private $enderecosModel;
    private $db;

    public function __construct()
    {
        $this->clientesModel = new ClienteModel();
        $this->enderecosModel = new EnderecosModel();

        $this->db = \Config\Database::connect();
    }

    /**
     * Lista todos os níveis com paginação e permissões
     */
    // public function listar(int $limite = 10, int $pagina = 1, array $filtros = [], ?string $data_inicio = null, ?string $data_fim = null): array
    // {
    //     // PaginacaoSimples
    //     $registros = $this->clientesModel->listarComPaginacao($limite, $pagina, $filtros, $data_inicio, $data_fim);

    //     return $registros;
    // }

    public function listar(int $limite = 10, int $pagina = 1, array $filtros = [], ?string $data_inicio = null, ?string $data_fim = null): array
    {
        // Obtém os registros paginados (clientes)
        $registros = $this->clientesModel->listarComPaginacao($limite, $pagina, $filtros, $data_inicio, $data_fim);

        

        // Verifica se o resultado contém a chave 'registros' (caso seu método retorne com paginação)
        if (isset($registros['registros']) && is_array($registros['registros'])) {
            foreach ($registros['registros'] as &$cliente) {
                // Busca os endereços desse cliente
                $enderecos = $this->enderecosModel->buscarPorCliente($cliente['id']);
                // Adiciona o campo no cliente
                $cliente['enderecos'] = $enderecos ?? [];
            }
        }

        return $registros;
    }

    /**
     * Busca um nível específico com suas permissões
     */
    public function buscar(int $id): array
    {
        $registro = $this->clientesModel->buscarPorId($id);
        $enderecos = $this->enderecosModel->buscarPorCliente($id);
        $registro['enderecos'] = $enderecos ?? [];
        
        if (!$registro) {
            throw ClienteException::naoEncontrado();
        }

        return $registro;
    }

    /**
     * Cria um novo nível com todas as permissões (allow = 0)
     * 
     * AQUI é onde a ORQUESTRAÇÃO acontece:
     * 1. Valida
     * 2. Cria nível
     * 3. Vincula permissões
     * 4. Retorna nível completo
     */
    public function criar(array $dados): array
    {
        // 1️⃣ Validação de negócio
        if (empty($dados['nome'])) {
            throw ClienteException::nomeObrigatorio();
        }

        // 3️⃣ Inicia transação (garante atomicidade)
        $this->db->transStart();

        try {
            $dadosCliente = [
                'nome' => $dados['nome'] ?? null,
                'razao_social' => $dados['razao_social'] ?? null,
                'email' => $dados['email'] ?? null,
                'telefone' => $dados['telefone'] ?? null,
                'celular' => $dados['celular'] ?? null,
                'observacao' => $dados['observacao'] ?? null,
            ];

            

            if (!$this->clientesModel->criar($dadosCliente)) {
                throw ClienteException::erroCriar($this->clientesModel->errors());
            };
            
            if (!empty($dados['enderecos'])) {
                foreach ($dados['enderecos'] as $endereco) {
                    $dadosEndereco = [
                        'cliente_id' => $this->clientesModel->getInsertID(),
                        'tipo' => $endereco['tipo'] ?? null,
                        'logradouro' => $endereco['logradouro'] ?? null,
                        'numero' => $endereco['numero'] ?? null,
                        'complemento' => $endereco['complemento'] ?? null,
                        'bairro' => $endereco['bairro'] ?? null,
                        'cidade' => $endereco['cidade'] ?? null,
                        'estado' => $endereco['estado'] ?? null,
                        'cep' => $endereco['cep'] ?? null,
                    ];
                    
                    if (!$this->enderecosModel->criar($dadosEndereco)) {
                        throw ClienteException::erroCriar($this->enderecosModel->errors());
                    }
                }
            }
            

            // 6️⃣ Finaliza transação
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw ClienteException::erroCriar();
            }

            // 7️⃣ Retorna o nível completo com permissões
            return $this->buscar($this->clientesModel->getInsertID());

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Atualiza um nível existente (nome e/ou permissões)
     */
    public function atualizar(int $id, array $dados): array
    {
        // 1️⃣ Validação
        if (empty($dados['nome'])) {
            throw ClienteException::nomeObrigatorio();
        }

        // 2️⃣ Verifica se existe
        $nivelExistente = $this->clientesModel->buscarPorId($id);
        if (!$nivelExistente) {
            throw ClienteException::naoEncontrado();
        }

        // 4️⃣ Inicia transação
        $this->db->transStart();

        try {
            $dadosCliente = [
                'nome' => $dados['nome'] ?? null,
                'razao_social' => $dados['razao_social'] ?? null,
                'email' => $dados['email'] ?? null,
                'telefone' => $dados['telefone'] ?? null,
                'celular' => $dados['celular'] ?? null,
                'observacao' => $dados['observacao'] ?? null,
            ];

            if (!$this->clientesModel->atualizar($id, $dadosCliente)) {
                throw ClienteException::erroAtualizar($this->clientesModel->errors());
            }

        
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw ClienteException::erroAtualizar();
            }

            return $this->buscar($id);

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Deleta um nível e suas permissões
     */
    public function deletar(int $id): bool
    {
        // 1️⃣ Verifica se existe
        if (!$this->clientesModel->buscarPorId($id)) {
            throw ClienteException::naoEncontrado();
        }

        // 2️⃣ Inicia transação
        $this->db->transStart();

        try {
           

            // 4️⃣ Remove o nível
            if (!$this->clientesModel->deletar($id)) {
                throw ClienteException::erroDeletar();
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw ClienteException::erroDeletar();  
            }

            return true;

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

   
}