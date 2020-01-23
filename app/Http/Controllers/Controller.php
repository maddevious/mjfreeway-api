<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Validator;
use Illuminate\Support\Arr;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $output;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $results = $this->output->model->get($request);
            return $this->output->setMessages(100)->transform($results, 'index')->render();
        } catch (\Exception $e) {
            return $this->output->setMessages($e)->render();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $results = $this->output->model->store($request->all());
            return $this->output->setMessages(100)->transform($results)->render();
        } catch (\Exception $e) {
            return $this->output->setMessages($e)->render();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($uuid, Request $request)
    {
        try {
            $request->merge(['filters' => ['uuid' => $uuid]]);
            $results = $this->output->model->show($request);
            return $this->output->setMessages(100)->transform($results)->render();
        } catch (\Exception $e) {
            return $this->output->setMessages($e)->render();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($uuid, Request $request)
    {
        try {
            $results = $this->output->model->update($uuid, $request->all());
            return $this->output->setMessages(100)->transform($results)->render();
        } catch (\Exception $e) {
            return $this->output->setMessages($e)->render();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        try {
            $this->output->model->destroy($uuid);
            return $this->output->setMessages(100)->render();
        } catch (\Exception $e) {
            return $this->output->setMessages($e)->render();
        }
    }

    public function validate(Request $request, $rules, $custom_messages = [])
    {
        $validator = Validator::make($request->all(), $rules, $custom_messages);
        $messages = Arr::flatten($validator->messages()->toArray());
        if ($validator->fails()) {
            throw new \Exception(json_encode($messages), 201);
        }
        return false;
    }
}
