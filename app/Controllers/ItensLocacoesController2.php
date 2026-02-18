<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\RequestFilterTrait;
use App\Traits\TratarErroTrait;



class ItensLocacoesController2 extends BaseController
{
    use TratarErroTrait;
    use RequestFilterTrait;

    /** 🔹 Nome da classe do Service (pode ser trocado) */
    private const SERVICE = \App\Services\ItensLocacoesService2::class;

    private $service;

    public function __construct()
    {
        $serviceClass = self::SERVICE;
        $this->service = new $serviceClass();

    }

    public function index()
    {
        try {
            $params = $this->getRequestFilters($this->request, [
                'pagination' => false,
                'dynamic' => true,
                'ordering' => true,
                'dates' => true,
            ]);

            $resultado = $this->service->listar($params);

            return $this->response->setJSON([
                'success' => true,
                ...$resultado,
                'filtros' => $params['filtros'],
            ]);

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    public function show($id = null)
    {
        try {
            $registro = $this->service->buscar((int) $id);

            return $this->response->setJSON([
                'success' => true,
                'registro' => $registro
            ]);

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }



    public function create()
    {


        try {
            $dados_usuario = service('request')->user ?? null;

            // $data = $this->request->getJSON(true);
            $data = $this->request->getPost();
            $data['responsavel'] = $dados_usuario->sub ?? null;
            $files = $this->request->getFiles();



            $registro = $this->service->criar($data, $files);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Criado com sucesso',
                'registro' => $registro
                // 'registro' => $teste
            ])->setStatusCode(201);

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    public function update($id = null)
    {
        try {
            $dados_usuario = service('request')->user ?? null;

            $data = $this->request->getPost();
            $data['responsavel'] = $dados_usuario->sub ?? null;
            $files = $this->request->getFiles();

            $registro = $this->service->atualizar((int) $id, $data, $files);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Atualizado com sucesso',
                'registro' => $registro
            ]);

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    public function delete($id = null)
    {
        try {
            $this->service->deletar((int) $id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Deletado com sucesso'
            ]);

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

    public function deleteImagem($arquivo = null)
    {
        try {
            $this->service->deletarImagem((string) $arquivo);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Deletado com sucesso'
            ]);

        } catch (\Exception $e) {
            return $this->tratarErro($e);
        }
    }

}
