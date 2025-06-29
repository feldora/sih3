<?php

namespace App\Repositories;

use App\Repositories\Contracts\SungaiRepositoryInterface;
use App\Models\Sungai;

class SungaiRepository implements SungaiRepositoryInterface
{
    public function all($columns = ['*'])
    {
        return Sungai::all($columns);
    }

    public function paginate($perPage = 10, $columns = ['*'])
    {
        return Sungai::select($columns)->paginate($perPage);
    }

    public function find($id)
    {
        return Sungai::findOrFail($id);
    }

    public function create(array $data)
    {
        return Sungai::create($data);
    }

    public function update($id, array $data)
    {
        $sungai = $this->find($id);
        $sungai->update($data);
        return $sungai;
    }

    public function delete($id)
    {
        $sungai = $this->find($id);
        return $sungai->delete();
    }
}
