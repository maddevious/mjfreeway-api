<?php

namespace App\Repositories;

use Illuminate\Http\Request;

abstract class Repository
{
    protected $model;

    public function get(Request $request)
    {
        $model_name = $this->__getModelName();
        $results = collect([]);
        if (empty($request->per_page)) {
            $request->merge(['per_page' => 50]);
        }
        if (empty($request->page)) {
            $request->merge(['page' => 1]);
        }

        $query = $this->model::where(function ($q) use ($request) {
            $modelTablePrefix = '';
            try {
                $modelTablePrefix = $this->model->getTable();
                if ($modelTablePrefix) {
                    $modelTablePrefix .= '.';
                }
            } catch(\Exception $e) {
                $modelTablePrefix = '';
            }
            if (!empty($request->filters['id'])) {
                $q->where($modelTablePrefix.'id', $request->filters['id']);
            }
            if (!empty($request->filters['uuid'])) {
                $q->where($modelTablePrefix.'uuid', $request->filters['uuid']);
            }
            if (!empty($request->filters['name'])) {
                $q->where($modelTablePrefix.'name', $request->filters['name']);
            }
            if (!empty($request->filters['search'])) {
                $q->where($modelTablePrefix.'name', 'like', "%{$request->filters['search']}%");
            }
            if (!empty($request->filters['created_start_at'])) {
                $q->where($modelTablePrefix.'created_at', '>=', $request->filters['created_start_at']);
            }
            if (!empty($request->filters['created_end_at'])) {
                $q->where($modelTablePrefix.'created_at', '<=', $request->filters['created_end_at']);
            }
            if (!empty($request->filters['updated_start_at'])) {
                $q->where($modelTablePrefix.'updated_at', '>=', $request->filters['updated_start_at']);
            }
            if (!empty($request->filters['updated_end_at'])) {
                $q->where($modelTablePrefix.'updated_at', '<=', $request->filters['updated_end_at']);
            }
            if (!empty($request->filters['is_deleted'])) {
                $q->withTrashed();
            }
        });

        if (!empty($request->orderby)) {
            $sortby = !empty($request->sortby) ? $request->sortby : 'asc';
            $query->orderBy($request->orderby, $sortby);
        }
        $results[$model_name.'_count'] = $query->count();
        $results[$model_name] = $query->offset($request->page - 1)->limit($request->per_page)->get();
        if (!empty($results[$model_name]) && $results[$model_name]->count() > 0) {
            return $results;
        } else {
            throw new \Exception("No results found", 400);
        }
        return $results;
    }

    public function store($data)
    {
        $this->model->fill($data)->save();
        if ($this->model->id) {
            $request = new Request();
            $request->merge([
                'filters' => [
                    'id' => $this->model->id
                ]
            ]);
            return $this->get($request);
        }
        throw new \Exception("Unable to create record", 999);
    }

    public function update($uuid, $data)
    {
        $result = $this->model::where('uuid', $uuid)->first();

        if ($result) {
            $result->fill($data);
            if ($result->save()) {
                return static::show(['uuid' => $result->uuid]);
            }
            throw new \Exception("Unable to update record", 999);
        }
        throw new \Exception("Record for update not found", 400);
    }

    public function show($uuid)
    {
        $model_name = $this->__getModelName();
        $request = request()->merge(['uuid' => $uuid]);
        $result = $this->get($request);
        if (!empty($results[$model_name]) && $results[$model_name]->count() > 0) {
            return $result;
        } else {
            throw new \Exception("Record not found", 400);
        }
    }

    public function destroy($uuid, $force = false)
    {
        if ($force) {
            $result = $this->model::withTrashed()->where('uuid', $uuid)->first();
        } else {
            $result = $this->model::where('uuid', $uuid)->first();
        }

        if (empty($result)) {
            throw new \Exception("Record not found to delete", 400);
        }
        if ($force) {
            if (!$result->forceDelete()) {
                throw new \Exception("Unable to delete record", 999);
            }
        } else {
            if (!$result->delete()) {
                throw new \Exception("Unable to delete record", 999);
            }
        }
        return true;
    }

    public function transform($result)
    {
        unset($result->id);
        return $result;
    }

    private function __getModelName()
    {
        return strtolower((new \ReflectionClass($this->model))->getShortName());
    }
}
