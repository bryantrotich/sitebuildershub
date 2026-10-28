<?php

namespace App\Http\Requests\Landing;

use Illuminate\Foundation\Http\FormRequest;

class CreatePostingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'categories' => [
                'array',
                'required'
            ],
            'sub_categories' => [
                'array',
                'required'
            ],            
            'description' => [
                'string',
                'required'
            ],
            'images' => [
                'array',
                'required'
            ],
            'location' => [
                'string',
                'required'
            ],
            'town' => [
                'string',
                'required'
            ],
            'county' => [
                'string',
                'required'
            ],
            'country' => [
                'string',
                'required'
            ],
            'quotation' => [
                'string',
                'required'
            ],
            'phone_number' => [
                'string',
                'required'
            ],
            'promotion_status' => [
                'boolean',
                'required'
            ],
            'promotion_section' => [
                'string',
                'nullable',
                'required_if:promotion_status,true'
            ],
            'promotion_image' => [
                'string',
                'nullable',
                'required_if:promotion_status,true'
            ],            
            'promotion_date_from' => [
                'date',
                'nullable',
                'required_if:promotion_status,true'
            ],
            'promotion_date_to' => [
                'date',
                'nullable',
                'required_if:promotion_status,true'
            ],
            'promotion_amount' => [
                'integer',
                'nullable',
                'required_if:promotion_status,true'
            ],  
            'placement_id' => [
                'string',
                'nullable',
                'required_if:promotion_status,true'
            ],    
            'title' => [
                'string',
                'required'
            ],                                                                              
        ];
    }
}
