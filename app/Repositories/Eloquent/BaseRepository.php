<?php

namespace App\Repositories\Eloquent;

use App\Models\Base;
use App\Repositories\Interface\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BaseRepository implements BaseRepositoryInterface
{
    protected array $querySearchTargets = [];

    public function getEmptyList()
    {
        return new Collection();
    }

    public function getModelClassName(): string
    {
        $model = $this->getBlankModel();

        return get_class($model);
    }

    public function getBlankModel()
    {
        return new Base();
    }

    public function all(string $order = null, string $direction = null)
    {
        $model = $this->getModelClassName();
        if (!empty($order)) {
            $direction = empty($direction) ? 'asc' : $direction;

            return $model::query()->orderBy($order, $direction)->get();
        }

        return $model::query()->get();
    }

    public function get(string $order, string $direction, int $offset, int $limit)
    {
        $model = $this->getModelClassName();

        return $model::query()->orderBy($order, $direction)->skip($offset)->take($limit)->get();
    }


    public function count(): int
    {
        $model = $this->getModelClassName();

        return $model::query()->count();
    }

    public function allByFilter(array $filter, string $order = null, string $direction = null)
    {
        $query = $this->buildQueryByFilter($this->getBlankModel(), $filter);
        $query = $this->buildOrder($query, $filter, $order, $direction);

        return $query->get();
    }

    public function getByFilter(array $filter, string $order, string $direction, int $offset, int $limit)
    {
        $query = $this->buildQueryByFilter($this->getBlankModel(), $filter);
        $query = $this->buildOrder($query, $filter, $order, $direction);

        return $query->skip($offset)->take($limit)->get();
    }

    public function countByFilter(array $filter)
    {
        $query = $this->buildQueryByFilter($this->getBlankModel(), $filter);

        return $query->count();
    }

    public function firstByFilter(array $filter)
    {
        $query = $this->buildQueryByFilter($this->getBlankModel(), $filter);

        return $query->first();
    }

    public function getSQLByFilter(array $filter)
    {
        $query = $this->buildQueryByFilter($this->getBlankModel(), $filter);

        return $query->toSql();
    }

    public function deleteByFilter(array $filter)
    {
        $query = $this->buildQueryByFilter($this->getBlankModel(), $filter);

        return $query->delete();
    }

    public function pluck($collection, $value, $key = null)
    {
        $items = [];
        foreach ($collection as $model) {
            if (empty($key)) {
                $items[] = $model->$value;
            } else {
                $items[ $model->$key ] = $model->$value;
            }
        }

        return Collection::make($items);
    }

    public function findById($id)
    {
        return $this->getBlankModel()->find($id);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param array                              $filter
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function buildQueryByFilter($query, $filter)
    {
        $tableName = $this->getBlankModel()->getTable();

        $query = $this->queryOptions($query);

        if (count($this->querySearchTargets) > 0 && array_key_exists('query', $filter)) {
            $searchWord = Arr::get($filter, 'query');
            if (!empty($searchWord)) {
                $query = $query->where(function($q) use ($searchWord) {
                    foreach ($this->querySearchTargets as $index => $target) {
                        if ($index === 0) {
                            $q = $q->where($target, 'LIKE', '%'.$searchWord.'%');
                        } else {
                            $q = $q->orWhere($target, 'LIKE', '%'.$searchWord.'%');
                        }
                    }
                });
            }
            unset($filter['query']);
        }

        if(array_key_exists('whereNotIn', $filter)) {
            $params = Arr::get($filter, 'whereNotIn');
            foreach ($params as $column => $values) {
                if (is_array($values)) {
                    $query = $query->whereNotIn($tableName.'.'.$column, $values);
                }
            }

            unset($filter['whereNotIn']);
        }

        if(array_key_exists('greaterThan', $filter)) {
            $params = Arr::get($filter, 'greaterThan');
            foreach ($params as $column => $values) {
                $query = $query->where($tableName.'.'.$column, '>',$values);
            }

            unset($filter['greaterThan']);
        }

        if(array_key_exists('lessThan', $filter)) {
            $params = Arr::get($filter, 'lessThan');
            foreach ($params as $column => $values) {
                $query = $query->where($tableName.'.'.$column, '<',$values);
            }

            unset($filter['lessThan']);
        }

        foreach ($filter as $column => $value) {
            if (is_array($value)) {
                $query = $query->whereIn($tableName.'.'.$column, $value);
            } else {
                $query = $query->where($tableName.'.'.$column, $value);
            }
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param array                              $filter
     * @param string                             $order
     * @param string                             $direction
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function buildOrder($query, $filter, $order, $direction)
    {
        if (!empty($order)) {
            $direction = empty($direction) ? 'asc' : $direction;
            $query     = $query->orderBy($order, $direction);
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function queryOptions($query)
    {
        return $query;
    }

    public function create($input)
    {
        DB::connection()->enableQueryLog();

        $model = $this->getBlankModel();
        $model = $this->update($model, $input);

        $queries = DB::getQueryLog();
        $query = $queries[count($queries) - 1];
        foreach( $query['bindings'] as $key => $value ) {
            $query['query'] = preg_replace("/\?/", "`$value`", $query['query'], 1);
        }

        if( App::environment() != 'testing' ) {
            $user = auth()->user();
            if( !empty($user) ) {
//                Log::create(
//                    [
//                        'user_id' => $user->id,
//                        'table'     => $this->getBlankModel()->getTable(),
//                        'action'    => Log::TYPE_ACTION_INSERT,
//                        'record_id' => $model->id,
//                        'query'     => $query['query'],
//                    ]
//                );
            }
        }

        return $model;
    }

    public function updateOrCreate($input, $update)
    {
        DB::connection()->enableQueryLog();

        $model = $this->getBlankModel()->updateOrCreate($input, $update);

        $queries = DB::getQueryLog();
        $query = $queries[count($queries) - 1];
        foreach( $query['bindings'] as $key => $value ) {
            $query['query'] = preg_replace("/\?/", "`$value`", $query['query'], 1);
        }

        if( App::environment() != 'testing' ) {
            $user = auth()->user();
            if( !empty($user) ) {
//                Log::create(
//                    [
//                        'user_id' => $user->id,
//                        'table'     => $this->getBlankModel()->getTable(),
//                        'action'    => Log::TYPE_ACTION_INSERT,
//                        'record_id' => $model->id,
//                        'query'     => $query['query'],
//                    ]
//                );
            }
        }

        return $model;
    }

    public function update($model, $input)
    {
        foreach ($model->getEditableColumns() as $column) {
            if (array_key_exists($column, $input)) {
                $model->$column = Arr::get($input, $column);
            }
        }

        if( isset($model->id) && $model->id ) {
            DB::connection()->enableQueryLog();
            $model = $this->save($model);
            if( !$model ) {
                return false;
            }

            if( count(DB::getQueryLog()) ) {
                $queries = DB::getQueryLog();
                $query = $queries[count($queries) - 1];
                foreach( $query['bindings'] as $key => $value ) {
                    $query['query'] = preg_replace("/\?/", "`$value`", $query['query'], 1);
                }

                if( App::environment() != 'testing' ) {
                    $user = auth()->user();
                    if( !empty($user) ) {
//                        Log::create(
//                            [
//                                'user_id' => $user->id,
//                                'table'     => $this->getBlankModel()->getTable(),
//                                'action'    => Log::TYPE_ACTION_UPDATE,
//                                'record_id' => $model->id,
//                                'query'     => $query['query'],
//                            ]
//                        );
                    }
                }
            }
        } else {
            $model = $this->save($model);
        }

        return $model;
    }

    public function save($model)
    {
        if (!$model->save()) {
            return false;
        }

        return $model;
    }

    public function delete($model)
    {
        DB::connection()->enableQueryLog();
        $deleted = $model->delete();

        $queries = DB::getQueryLog();
        $query = $queries[count($queries) - 1];
        foreach( $query['bindings'] as $key => $value ) {
            $query['query'] = preg_replace("/\?/", "`$value`", $query['query'], 1);
        }

        if( App::environment() != 'testing' ) {
            $user = auth()->user();

            if( !empty($user) ) {
//                Log::create(
//                    [
//                        'user_id' => $user->id,
//                        'table'     => $this->getBlankModel()->getTable(),
//                        'action'    => Log::TYPE_ACTION_DELETE,
//                        'record_id' => $model->id,
//                        'query'     => $query['query'],
//                    ]
//                );
            }
        }

        return $deleted;
    }

    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'getBy')) {
            return $this->dynamicGet($method, $parameters);
        }

        if (Str::startsWith($method, 'allBy')) {
            return $this->dynamicAll($method, $parameters);
        }

        if (Str::startsWith($method, 'countBy')) {
            return $this->dynamicCount($method, $parameters);
        }

        if (Str::startsWith($method, 'findBy')) {
            return $this->dynamicFind($method, $parameters);
        }

        if (Str::startsWith($method, 'deleteBy')) {
            return $this->dynamicDelete($method, $parameters);
        }

        $className = static::class;
        throw new \BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }

    private function dynamicGet($method, $parameters)
    {
        $finder = substr($method, 5);
        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount = count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model = $this->getBlankModel();
        $whereMethod = 'where'.$finder;
        $query = call_user_func_array([$model, $whereMethod], $conditionParams);

        $order = Arr::get($parameters, 0, 'id');
        $direction = Arr::get($parameters, 1, 'asc');
        $offset = Arr::get($parameters, 2, 0);
        $limit = Arr::get($parameters, 3, 10);

        if (!empty($order)) {
            $direction = empty($direction) ? 'asc' : $direction;
            $query = $query->orderBy($order, $direction);
        }
        if (!is_null($offset) && !is_null($limit)) {
            $query = $query->offset($offset)->limit($limit);
        }

        return $query->get();
    }

    private function dynamicAll($method, $parameters)
    {
        $finder = substr($method, 5);
        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount = count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model = $this->getBlankModel();
        $whereMethod = 'where'.$finder;
        $query = call_user_func_array([$model, $whereMethod], $conditionParams);

        $order = Arr::get($parameters, 0, 'id');
        $direction = Arr::get($parameters, 1, 'asc');

        return $query->orderBy($order, $direction)->get();
    }

    private function dynamicCount($method, $parameters)
    {
        $finder = substr($method, 7);
        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount = count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model = $this->getBlankModel();
        $whereMethod = 'where'.$finder;
        $query = call_user_func_array([$model, $whereMethod], $conditionParams);

        return $query->count();
    }

    private function dynamicFind($method, $parameters)
    {
        $finder = substr($method, 6);
        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount = count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model = $this->getBlankModel();
        $whereMethod = 'where'.$finder;
        $query = call_user_func_array([$model, $whereMethod], $conditionParams);

        return $query->first();
    }

    private function dynamicDelete($method, $parameters)
    {
        $finder = substr($method, 8);
        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount = count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model = $this->getBlankModel();
        $whereMethod = 'where'.$finder;
        $query = call_user_func_array([$model, $whereMethod], $conditionParams);

        return $query->delete();
    }

    public function load($collections, $relationShip)
    {
        if (empty($collections)) {
            return $collections;
        }

        return $collections->load($relationShip);
    }
}
