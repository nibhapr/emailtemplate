<?php

namespace App\SuperAdmin\Http\Requests\Api\Lang;

use Vinkla\Hashids\Facades\Hashids;
use App\SuperAdmin\Http\Requests\Api\SuperAdminBaseRequest;

class UpdateRequest extends SuperAdminBaseRequest
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
        $convertedId = Hashids::decode($this->route('lang'));
        $id = $convertedId[0];

        return [
            'name' => 'required',
            'key' => 'required|unique:langs,key,' . $id,
        ];
    }
}
