<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class RequisitionRequest extends Request
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
            'document_at' => 'required',
            'take_id' => 'required',
            'warehouse_good_id' => 'required|array',
            'amount' => 'required|array',
        ];
    }
}
