<?php

namespace App\Http\Requests;

use App\Traits\IsValidRequest;
use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
{
    use IsValidRequest;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'mobile' => 'nullable|string',
            'address' => 'nullable|string',
        ];
    }
}
