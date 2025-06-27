<?php
namespace App\Services;

use App\Models\WilayahSungai;
use Illuminate\Support\Collection;

class WilayahSungaiService
{
    /**
     * Get all Wilayah Sungai records.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return WilayahSungai::all();
    }

    /**
     * Find a Wilayah Sungai by ID.
     *
     * @param int $id
     * @return WilayahSungai|null
     */
    public function findById(int $id): ?WilayahSungai
    {
        return WilayahSungai::find($id);
    }

    /**
     * Create a new Wilayah Sungai.
     *
     * @param array $data
     * @return WilayahSungai
     */
    public function create(array $data): WilayahSungai
    {
        return WilayahSungai::create($data);
    }

    /**
     * Update an existing Wilayah Sungai.
     *
     * @param int $id
     * @param array $data
     * @return WilayahSungai|null
     */
    public function update(int $id, array $data): ?WilayahSungai
    {
        $wilayahSungai = WilayahSungai::find($id);
        if ($wilayahSungai) {
            $wilayahSungai->update($data);
        }
        return $wilayahSungai;
    }

    /**
     * Delete a Wilayah Sungai by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $wilayahSungai = WilayahSungai::find($id);
        if ($wilayahSungai) {
            return $wilayahSungai->delete();
        }
        return false;
    }
}