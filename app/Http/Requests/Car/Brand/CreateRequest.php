<?php

namespace App\Http\Requests\Car\Brand;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        $this->merge([
            'created_by' => User::getUserByAccessToken($this->bearerToken())->id
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:car_brands|max:255',
            'created_by' => 'required|exists:users,id',
        ];
    }
}
