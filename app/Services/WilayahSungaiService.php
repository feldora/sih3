<?php
namespace App\Services;

use App\Repositories\Contracts\WilayahSungaiRepositoryInterface;

class WilayahSungaiService
{
    protected $repository;

    public function __construct(WilayahSungaiRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all Wilayah Sungai records.
     *
     * @return Collection
     */
    public function all($columns = ['*'])
    {
        return $this->repository->all($columns);
    }

    /**
     * Paginate Wilayah Sungai records.
     *
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = 10, $columns = ['*'])
    {
        return $this->repository->paginate($perPage, $columns);
    }

    /**
     * Find a Wilayah Sungai by ID.
     *
     * @param int $id
     * @return WilayahSungai|null
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Create a new Wilayah Sungai.
     *
     * @param array $data
     * @return WilayahSungai
     */
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    /**
     * Update an existing Wilayah Sungai.
     *
     * @param int $id
     * @param array $data
     * @return WilayahSungai|null
     */
    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a Wilayah Sungai by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}