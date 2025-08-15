<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class sendBillPaymentAdviceRequest extends FormRequest
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
            "amount" => "required|string",
            "payment_code" => "required|string",
            //"customer_account_number" => "required|string",
            "customer_mobile" => "required|string",
            "customer_id" => "required|string",
            "cifid" => "required|string"
        ];
    }
}
