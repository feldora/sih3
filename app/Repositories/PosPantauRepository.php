<?php

namespace App\Repositories;

use App\Repositories\Contracts\PosPantauRepositoryInterface;
use App\Models\PosPantau;

class PosPantauRepository implements PosPantauRepositoryInterface
{
    public function all()
    {
        return PosPantau::all();
    }

    public function find($id)
    {
        return PosPantau::findOrFail($id);
    }

    public function create(array $data)
    {
        return PosPantau::create($data);
    }

    public function update($id, array $data)
    {
        $posPantau = $this->find($id);
        $posPantau->update($data);
        return $posPantau;
    }

    public function delete($id)
    {
        $posPantau = $this->find($id);
        return $posPantau->delete();
    }
    
    public function paginate($perPage = 10, $columns = ['*'])
    {
        return PosPantau::select($columns)->paginate($perPage);
    }
}
