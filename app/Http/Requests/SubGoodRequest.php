<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubGoodRequest extends FormRequest
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
            'warehouse_id' => 'required',
            'date' => 'required|date|date_format:Y-m-d',
            'user_id' => 'required|integer',
            'key_name' => 'required',
            'key_id' => 'required|integer',
            'goods' => 'required|array',
            'goods.good_id' => 'required|integer',
            'goods.coil_code' => 'required',
            'goods.amount' => 'required|numeric',
        ];
    }
}
