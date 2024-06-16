<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', Password::min(8)->symbols()->mixedCase()->numbers()],
            'password_confirmation' => 'required|same:password',
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'name.required' => 'Please enter a name.',
    //         'name.max' => 'The name must be no longer than 255 characters.',
    //         'email.required' => 'Please enter an email address.',
    //         'email.email' => 'The email address format is invalid.',
    //         'email.unique' => 'The email address is already in use.',
    //     ];
    // }
}
