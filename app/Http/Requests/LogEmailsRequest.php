<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogEmailsRequest extends FormRequest
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
            'membership_id' => 'required',
            // 'membership_id' => 'required|exists:lsl_enrollment_master',
            'email_type'=> 'required',// could be Transaction or Enrollment,
            'subject' => 'required',
            'body' => 'required',
            'trans_ref' => 'required_if:email_type,enrollment',
            'email' => 'required',
        ];
    }
}