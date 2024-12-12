<?php

namespace App\Http\Requests\Api\Expense;

use App\Http\Requests\Api\BaseRequest;

class StoreRequest extends BaseRequest
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
        $rules = [
            'expense_category_id' => 'required',
            'date' => 'required',
            'amount' => 'required|numeric',

        ];

        return $rules;
    }
}
