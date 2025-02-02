<?php

namespace App\SuperAdmin\Http\Requests\Api\PaymentSettings;

use App\SuperAdmin\Http\Requests\Api\SuperAdminBaseRequest;

class PaystackUpdateRequest extends SuperAdminBaseRequest
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
			'paystack_client_id'    => 'required',
			'paystack_secret'    => 'required',
			'paystack_merchant_email'    => 'required',
			'paystack_status'    => 'required',
		];

		return $rules;
	}
}
