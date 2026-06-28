<?php

namespace App\Traits;

trait DatatableTrait
{
    protected function getDatatableRequest(): array
    {
        $request = service('request');

        return [
            'draw'   => $request->getPost('draw'),
            'start'  => $request->getPost('start'),
            'length' => $request->getPost('length'),
            'search' => $request->getPost('search')['value'] ?? '',
            'orderColumnIndex' => $request->getPost('order')[0]['column'] ?? 0,
            'orderDir'         => $request->getPost('order')[0]['dir'] ?? 'asc',
        ];
    }

    protected function getDatatableOrderColumn(array $columns, int $index, string $default): string
    {
        return $columns[$index] ?? $default;
    }

    protected function formatDatatableResponse(int $draw, int $total, int $filtered, array $data): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setJSON([
            'draw'            => intval($draw),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
    }
}
