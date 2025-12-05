<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Leave as true for demo; in real world implement policy checks.
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:191',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:30',
        ];
    }
}
