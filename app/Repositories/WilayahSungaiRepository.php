<?php

namespace App\Repositories;

use App\Repositories\Contracts\WilayahSungaiRepositoryInterface;
use App\Models\WilayahSungai;

class WilayahSungaiRepository implements WilayahSungaiRepositoryInterface
{
    public function all($columns = ['*'])
    {
        return WilayahSungai::all($columns);
    }

    public function paginate($perPage = 10, $columns = ['*'])
    {
        return WilayahSungai::select($columns)->paginate($perPage);
    }

    public function find($id)
    {
        return WilayahSungai::findOrFail($id);
    }

    public function create(array $data)
    {
        return WilayahSungai::create($data);
    }

    public function update($id, array $data)
    {
        $wilayahSungai = $this->find($id);
        $wilayahSungai->update($data);
        return $wilayahSungai;
    }

    public function delete($id)
    {
        $wilayahSungai = $this->find($id);
        return $wilayahSungai->delete();
    }
}
