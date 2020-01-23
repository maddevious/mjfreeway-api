<?php

namespace App\Observers;

class Observer
{
    protected $model;

    public function creating($model)
    {
        $request_data = request()->all();
        $data = !empty($request_data) ? $request_data : $model->getAttributes();
        $v = $this->model->validateCreate($data);
        if ($v->fails()) {
            throw new \Exception(json_encode($v->errors()->all()));
        }
    }

    public function created($model)
    {
        //
    }

    public function updating($model)
    {
        $request_data = request()->all();
        $data = !empty($request_data) ? $request_data : $model->getAttributes();
        $v = $this->model->validateCreate($data);
        if ($v->fails()) {
            throw new \Exception(json_encode($v->errors()->all()));
        }
    }

    public function updated($model)
    {
        //
    }

    public function deleting($model)
    {
        //
    }

    public function deleted($model)
    {
        //
    }

    protected function setModel($model)
    {
        $this->model = $model;
    }
}
