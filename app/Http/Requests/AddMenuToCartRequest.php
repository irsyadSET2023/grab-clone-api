<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Menu;

class AddMenuToCartRequest extends FormRequest
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
        $restaurantId = $this->route('restaurant')->id;

        return [
            'menu_id' => [
                'required',
                'numeric',
                Rule::exists('landlord.menus', 'id')->where(function ($query) use ($restaurantId) {
                    $query->where('restaurant_id', '=', $restaurantId);
                }),
            ],
            'quantity' => 'sometimes|numeric',
        ];
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'menu_id.exists' => 'The selected menu item does not belong to this restaurant.',
        ];
    }
}
