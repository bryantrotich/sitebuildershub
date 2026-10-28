<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;

class SystemRequest extends FormRequest
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
            'tab' => [
                'required',
                'string',
                'in:images,mail,maps,payment'
            ],
            'logo' => [
                'exclude_unless:tab,images',
                'nullable',
                'required_without:icon',
                File::image()->types(['jpeg','jpg','png','webp'])->max(2048)
            ],
            'icon' => [
                'exclude_unless:tab,images',
                'nullable',
                'required_without:logo',
                File::image()->types(['jpeg','jpg','png','webp'])->max(1024)
            ],
            'host' => [
                'required_if:tab,mail',
                'string'
            ],
            'password' => [
                'required_if:tab,mail',
                'string'
            ],
            'port' => [
                'required_if:tab,mail',
                'string'
            ],
            'username' => [
                'required_if:tab,mail',
                'string'
            ],
            'google_maps_api_key' => [
                'required_if:tab,maps',
                'nullable',
                'string',
                'max:255'
            ],
            'google_maps_id' => [
                'nullable',
                'string',
                'max:255'
            ],
            'action' => [
                'required_if:tab,payment',
                'nullable',
                'in:test'
            ]
        ];
    }
}
