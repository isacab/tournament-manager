<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    public function response(array $errors)
    {
        return response()->json($errors, $this->responseCode());
    }
    
    protected function responseCode()
    {
        return 422;
    }
}
