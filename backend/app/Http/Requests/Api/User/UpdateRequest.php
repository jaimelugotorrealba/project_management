<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
       $userId = $this->route('user')->id ?? null;

        return [
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users')->ignore($userId),
            ],
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'rol' => 'sometimes|string|in:admin,dev',
            'password' => 'nullable|string|min:6|confirmed',
            'profile_image' => 'nullable|image|max:2048',
        ];
    }
}
