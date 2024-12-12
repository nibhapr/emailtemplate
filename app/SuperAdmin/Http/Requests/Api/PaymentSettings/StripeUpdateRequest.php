<?php

namespace App\SuperAdmin\Http\Requests\Api\PaymentSettings;

use App\SuperAdmin\Http\Requests\Api\SuperAdminBaseRequest;

class StripeUpdateRequest extends SuperAdminBaseRequest
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
			'stripe_api_key'    => 'required',
			'stripe_api_secret'    => 'required',
			'stripe_webhook_key'    => 'required',
			'stripe_status'    => 'required',
		];

		return $rules;
	}
}
