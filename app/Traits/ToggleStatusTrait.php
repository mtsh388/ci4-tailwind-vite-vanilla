<?php

namespace App\Traits;

trait ToggleStatusTrait
{
    protected function handleToggleStatus($model, int $id, string $entityName): \CodeIgniter\HTTP\ResponseInterface
    {
        $record = $model->find($id);

        if (!$record) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => $entityName . ' tidak ditemukan',
            ]);
        }

        $newStatus = $record['is_active'] ? 0 : 1;

        $model->update($id, [
            'is_active' => $newStatus,
        ]);

        return $this->response->setJSON([
            'status'    => true,
            'message'   => 'Status berhasil diupdate',
            'is_active' => $newStatus,
        ]);
    }
}
