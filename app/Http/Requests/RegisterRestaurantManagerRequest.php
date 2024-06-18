<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRestaurantManagerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:10|max:255',
            'email' => 'required|email|unique:grabclone.users,email',
            'password' => 'required|confirmed|min:8',
            'restaurant_name' => 'required|min:5',
            'organization_number' => 'required|numeric|digits:12'

        ];
    }
}
