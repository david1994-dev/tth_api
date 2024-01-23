<?php

namespace App\Repositories\Interface;

interface BaseRepositoryInterface
{
    /**
     * Get Empty Array or Traversable Object.
     *
     * @return \App\Models\Base[]|\Traversable|array
     */
    public function getEmptyList();

    /**
     * Get All Models.
     *
     * @param string|null $order
     * @param string|null $direction
     *
     * @return \App\Models\Base[]|\Traversable|array
     */
    public function all(string $order = null, string $direction = null);

    /**
     * Get Models with Order.
     *
     * @param string $order
     * @param string $direction
     * @param int $offset
     * @param int $limit
     *
     * @return \App\Models\Base[]|\Traversable|array
     */
    public function get(string $order, string $direction, int $offset, int $limit);

    /**
     * @return int
     */
    public function count();

    /**
     * Get All Models with filter conditions.
     *
     * @param array $filter
     * @param string|null $order
     * @param string|null $direction
     *
     * @return \App\Models\Base[]|\Traversable|array
     */
    public function allByFilter(array $filter, string $order = null, string $direction = null);

    /**
     * Get Models with Order.
     *
     * @param array $filter
     * @param string $order
     * @param string $direction
     * @param int $offset
     * @param int $limit
     *
     * @return \App\Models\Base[]|\Traversable|array
     */
    public function getByFilter(array $filter, string $order, string $direction, int $offset, int $limit);

    /**
     * @param array $filter
     *
     * @return int
     */
    public function countByFilter(array $filter);

    /**
     * @param array $filter
     *
     * @return \App\Models\Base | null
     */
    public function firstByFilter(array $filter);

    /**
     * @param array $filter
     *
     * @return string
     */
    public function getSQLByFilter(array $filter);

    /**
     * @param array $filter
     **/
    public function deleteByFilter(array $filter);

    /**
     * @return string
     */
    public function getModelClassName();

    /**
     * Get Empty Array or Traversable Object.
     *
     * @return \Illuminate\Database\Eloquent\Model | \Illuminate\Database\Query\Builder;
     */
    public function getBlankModel();

    /**
     * @param \Illuminate\Support\Collection $collection
     * @param string                         $value
     * @param string|null                    $key
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck($collection, $value, $key = null);
    public function findById($id);
    public function create($input);
    public function update($model, $input);
    public function save($model);
    public function delete($model);
    public function load($collections, $relationShip);
}
