<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\LocacoesService;
use App\Exceptions\LocacoesException;
use App\Traits\RequestFilterTrait;

class LocacoesController extends BaseController
{
    use RequestFilterTrait;
    private $LocacoesService;
    public function __construct()
    {
        $this->LocacoesService = new LocacoesService();
    }

    public function index()
    {
        try {

            // Aqui pego os parametros da url e no optional passo os filtros ativos
            $params = $this->getRequestFilters($this->request, [
                'pagination' => true
            ]);



            $resultado = $this->LocacoesService->listar($params);

            return $this->response->setJSON([
                'success' => true,
                ...$resultado,
                // 'registros' => $resultado['registros'],
                // 'paginacao' => $resultado['paginacao'],
                'filtros' => $params['filtros'],
            ]);

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    public function show($id = null)
    {
        try {
            $itemLocacao = $this->LocacoesService->buscar((int) $id);

            return $this->response->setJSON([
                'success' => true,
                'Registros' => $itemLocacao
            ]);

        } catch (LocacoesException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode($e->getCode());

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            $itemLocacao = $this->LocacoesService->criar($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Item Locação criado com sucesso',
                'registro' => $itemLocacao
            ])->setStatusCode(201);

        } catch (LocacoesException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode($e->getCode());

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);

            $itemLocacao = $this->LocacoesService->atualizar((int) $id, $data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Item Locação atualizado com sucesso',
                'registro' => $itemLocacao
            ]);

        } catch (LocacoesException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode($e->getCode());

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    public function delete($id = null)
    {
        try {
            $this->LocacoesService->deletar((int) $id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Item Locação deletado com sucesso'
            ]);

        } catch (LocacoesException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode($e->getCode());

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }
    private function tratarErro(\Exception $e): \CodeIgniter\HTTP\Response
    {
        log_message('error', '[ClientesController] ' . $e->getMessage());

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro interno do servidor',
            'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null
        ])->setStatusCode(500);
    }
}
