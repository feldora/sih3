<?php

namespace App\Repositories;

use App\Repositories\Contracts\TitikPantauRepositoryInterface;
use App\Models\TitikPantau;

class TitikPantauRepository implements TitikPantauRepositoryInterface
{
    public function all($columns = ['*'])
    {
        return TitikPantau::all($columns);
    }

    public function paginate($perPage = 10, $columns = ['*'])
    {
        return TitikPantau::select($columns)->paginate($perPage);
    }

    public function find($id)
    {
        return TitikPantau::findOrFail($id);
    }

    public function create(array $data)
    {
        return TitikPantau::create($data);
    }

    public function update($id, array $data)
    {
        $titikPantau = $this->find($id);
        $titikPantau->update($data);
        return $titikPantau;
    }

    public function delete($id)
    {
        $titikPantau = $this->find($id);
        return $titikPantau->delete();
    }
}
