<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'      => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-zA-Z])(?=.*[0-9]).+$/'],
            'mobile'        => ['required', 'digits:10'],
            'dob'           => ['required', 'date'],
            'gender'        => ['required', 'in:Male,Female,Other'],
            'profile_image' => ['nullable', 'image', 'max:2048'],
            'status'        => ['required', 'in:Active,Inactive'],
        ];
    }
}
