<?php

namespace App\Repositories\Contracts;

interface TitikPantauRepositoryInterface
{
    public function all($columns = ['*']);
    public function paginate($perPage = 10, $columns = ['*']);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
