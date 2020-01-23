<?php

namespace App\Repositories;

use Log;

class OutputRepository
{
    public $debug = true;
    public $model;
    protected $message = [];
    protected $status;
    protected $output = [];
    protected $format = 'json';
    protected $request;
    protected $params;
    protected $MESSAGES = [
        100 => ['httpCode' => 200, 'error' => 'Request successful'],
        201 => ['httpCode' => 200, 'error' => 'Missing/Invalid field'],
        204 => ['httpCode' => 200, 'error' => 'Account is locked/expired'],
        205 => ['httpCode' => 200, 'error' => 'We need to verify your email, please check your email'],
        206 => ['httpCode' => 200, 'error' => 'Security questions reset required'],
        207 => ['httpCode' => 200, 'error' => 'Password reset required'],
        208 => ['httpCode' => 200, 'error' => 'Token expired'],
        209 => ['httpCode' => 200, 'error' => 'We need to verify your cell phone, please check your text messages'],
        211 => ['httpCode' => 200, 'error' => 'Image upload failed'],
        212 => ['httpCode' => 200, 'error' => 'Account is disabled'],
        400 => ['httpCode' => 200, 'error' => 'No results found'],
        401 => ['httpCode' => 401, 'error' => 'Session expired'],
        402 => ['httpCode' => 200, 'error' => 'Invalid credentials provided'],
        403 => ['httpCode' => 200, 'error' => 'Record already exists'],
        405 => ['httpCode' => 415, 'error' => 'Unsupported content type'],
        407 => ['httpCode' => 200, 'error' => 'The account has already been registered'],
        408 => ['httpCode' => 200, 'error' => 'The username has already been taken'],
        501 => ['httpCode' => 200, 'error' => 'Maximum login attempts, please reset your password'],
        999 => ['httpCode' => 500, 'error' => 'Unexpected error occurred']
    ];

    public function __construct()
    {
        $this->request = request();
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function setMessages($e)
    {
        $this->message = [];
        $exception = '';
        $message = is_object($e) ? json_decode($e->getMessage()) : null;
        if ($message) {
            if (is_numeric($message) && isset($this->MESSAGES[$message])) {
                $this->message = ['code' => $message, 'messages' => $this->MESSAGES[$message]['error']];
            } else {
                $this->message = ['code' => 201, 'messages' => $message];
            }
        } else {
            if (is_object($e)) {
                if (isset($this->MESSAGES[$e->getCode()]['error'])) {
                    $code = $e->getCode();
                    $message = isset($this->MESSAGES[$e->getCode()]['error']) ? $this->MESSAGES[$e->getCode()]['error'] : $e->getMessage();
                } else {
                    $code = !empty($e->getCode()) ? $e->getCode() : 201;
                    $message = $e->getMessage();
                }
                if (!env('APP_DEBUG') && strstr($message, 'SQLSTATE')) {
                    $code = 999;
                    $message = 'Unexpected error occurred';
                }
                $exception = $e->getMessage().' in '.$e->getFile().', line '.$e->getLine();
            } elseif (isset($this->MESSAGES[$e]['error'])) {
                $code = $e;
                $message = $this->MESSAGES[$e]['error'];
            } elseif (is_string($e)) {
                $code = 201;
                $message = $e;
            } else {
                $code = 999;
                $message = 'Unexpected error occurred';
            }
            $this->message = ['code' => $code, 'messages' => $message];
        }
        if (!empty($exception)) {
            if ($this->debug) {
                dd($e);
            }
            if (isset($this->request)) {
                $method = $this->request->method();
                $params = $this->request->all();
                $url = $this->request->url();
            } else {
                $method = $params = $url = '';
            }
            if ($code == 400) {
                if (env('APP_DEBUG')) {
                    Log::info($exception . "; Request: " . $method . " " . $url . ", params: " . json_encode($params).", sid:".$this->request->header('sid'));
                }
            } else {
                Log::error($exception . "; Request: " . $method . " " . $url . ", params: " . json_encode($params).", sid:".$this->request->header('sid'));
            }
        }
        return $this;
    }

    public function setStatus($status)
    {
        if (!empty($status)) {
            $this->status = $status;
            return $this;
        }
        if (empty($this->messages_output)) {
            if (!empty($status) && config("errors.$status")) {
                $this->message = ['code' => 999, 'message' => config("errors.$status")];
            }
        }
        $this->status = empty($this->status) ? $this->__getStatus() : $this->status;
        return $this;
    }

    public function setOutput($output)
    {
        $this->output = $output;
        return $this;
    }

    public function transform($collection, $type = '')
    {
        $this->output = new \stdClass();

        if ($type == 'count') {
            $this->output->total_count = $collection;
            return $this;
        } elseif ($type == 'index') {
            $this->output->total_count = isset($collection[$this->__getModelName().'_count']) ? $collection[$this->__getModelName().'_count'] : 1;
            $this->output->per_page = !empty(request()->input('per_page')) ? (int)request()->input('per_page') : 10;
            $this->output->current_page = !empty(request()->input('page')) ? (int)request()->input('page') : 1;
            $this->output->last_page = ceil($this->output->total_count / $this->output->per_page);
        }

        $process_row = function($row) {
            return $this->model->transform($row);
        };

        $objectname = strtolower(str_ireplace(['app\\','repositories','repository', '\\'], ['','','',''], get_class($this->model)));
        if (isset($collection[$objectname]) && is_object($collection[$objectname])) {
            foreach ($collection[$objectname] as $row) {
                if ($type == 'index') {
                    $this->output->$objectname[] = $process_row($row);
                } else {
                    $this->output = $process_row($row);
                }
            }
        } else {
            $this->output = !empty($collection[$objectname]) ? $process_row($collection[$objectname]) : null;
        }
        return $this;
    }

    public function render()
    {
        $this->setStatus(null);
        if (!empty($this->message) && $this->status != 200) {
            if (isset($this->request)) {
                $method = $this->request->method();
                $params = $this->request->all();
                $url = $this->request->url();
            } else {
                $method = $params = $url = '';
            }
            Log::error(json_encode($this->message) . "; Request: " . $method . " " . $url . ", params: " . json_encode($params));
        }
        $format = $this->format;
        if (env('APP_DEBUG') && isset($this->request)) {
            if (env('APP_DEBUG_REQUEST_ONLY') == true) {
                Log::info($this->request->method().' '.$this->request->url().': params: '.json_encode($this->request->all()).', sid:'.$this->request->header('sid'));
            } else {
                Log::info($this->request->method() . ' ' . $this->request->url() . ': params: ' . json_encode($this->request->all()) . ', sid:' . $this->request->header('sid') . ', output: ' . json_encode(['message' => $this->message, 'data' => $this->output]));
            }
        }
        return response()->$format([
            'message' => $this->message,
            'data' => $this->output
        ], $this->status);
    }

    private function __getStatus()
    {
        return isset($this->message['code']) && array_key_exists($this->message['code'], $this->MESSAGES) ? $this->MESSAGES[$this->message['code']]['httpCode'] : 500;
    }

    private function __getModelName()
    {
        return strtolower(str_ireplace(['app\\','repositories','repository', '\\'], ['','','',''], get_class($this->model)));
    }
}
