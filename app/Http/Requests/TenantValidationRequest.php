<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenantValidationRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'session_domain' =>'required|unique:tenants,session_domain',
            'app_url' => 'required|unique:tenants,app_url',
            'id' => 'required|unique:tenants,id'
        ];
    }
    public function messages()
    {
        return [
            'session_domain.required' => 'This is required Field',
            'session_domain.unique' => 'Session Domain Already exists',
            'app_url.required' => 'This is required Field',
            'app_url.unique' => 'App_Url Already exists',
            'id.required' => 'This is required Field',
            'id.unique' => 'Id Already exists',
        ];
    }
}
