<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\EnderecoService;
use App\Exceptions\EnderecoException;



class EnderecosController extends BaseController
{
    private $enderecoService;

    public function __construct()
    {
        $this->enderecoService = new EnderecoService();
    }

    public function index()
    {
        //
    }

    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            $endereco = $this->enderecoService->criar($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Endereço criado com sucesso',
                'registro' => $endereco
            ])->setStatusCode(201);

        } catch (EnderecoException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode($e->getCode());

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    public function show($id = null)
    {
        try {
            $endereco = $this->enderecoService->buscar((int) $id);

            return $this->response->setJSON([
                'success' => true,
                'Registros' => $endereco
            ]);

        } catch (EnderecoException $e) {
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
            $endereco = $this->enderecoService->atualizar((int) $id, $data);


            return $this->response->setJSON([
                'success' => true,
                'message' => 'Endereço atualizado com sucesso',
                'registro' => $endereco
            ]);

        } catch (EnderecoException $e) {
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
            $this->enderecoService->deletar((int) $id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Endereço deletado com sucesso'
            ]);

        } catch (EnderecoException $e) {
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
        log_message('error', '[EnderecosController] ' . $e->getMessage());

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro interno do servidor',
            'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null
        ])->setStatusCode(500);
    }
}
