<?php

namespace App\Traits;

trait RequestFilterTrait
{
    /**
     * Processa filtros da requisição GET com opções flexíveis.
     *
     * @param \CodeIgniter\HTTP\IncomingRequest $request
     * @param array $options  Opções para ativar/desativar partes do filtro:
     *                        [
     *                          'pagination' => true/false,
     *                          'dates'      => true/false,
     *                          'ordering'   => true/false,
     *                          'dynamic'    => true/false
     *                        ]
     */
    public function getRequestFilters($request, array $options = []): array
    {
        // Valores padrão das opções
        $options = array_merge([
            'pagination' => false,
            'dates' => false,
            'ordering' => false,
            'dynamic' => false
        ], $options);

        $result = [
            'limite' => null,
            'pagina' => null,
            'data_inicio' => null,
            'data_fim' => null,
            'order_by' => null,
            'order_dir' => null,
            'filtros' => [],
        ];

        $all = $request->getGet();

        // 🔹 PAGINAÇÃO
        if ($options['pagination']) {
            $result['limite'] = intval($request->getGet('limite') ?? 10);
            $result['pagina'] = intval($request->getGet('pagina') ?? 1);
        }

        // 🔹 DATAS
        if ($options['dates']) {
            $result['data_inicio'] = $request->getGet('data_inicio') ?: null;
            $result['data_fim'] = $request->getGet('data_fim') ?: null;
        }

        // 🔹 ORDENAÇÃO
        if ($options['ordering']) {
            $result['order_by'] = $request->getGet('order_by') ?: 'id';
            $result['order_dir'] = $request->getGet('order_dir') ?: 'asc';
        }

        // Campos que precisam ser ignorados nos filtros dinâmicos
        $ignore = [];

        if ($options['pagination']) {
            $ignore[] = 'limite';
            $ignore[] = 'pagina';
        }

        if ($options['dates']) {
            $ignore[] = 'data_inicio';
            $ignore[] = 'data_fim';
        }

        if ($options['ordering']) {
            $ignore[] = 'order_by';
            $ignore[] = 'order_dir';
        }

        // 🔹 FILTROS DINÂMICOS
        if ($options['dynamic']) {
            foreach ($all as $key => $value) {
                if (!in_array($key, $ignore)) {
                    $result['filtros'][$key] = $value;
                }
            }
        }

        return $result;
    }
}
