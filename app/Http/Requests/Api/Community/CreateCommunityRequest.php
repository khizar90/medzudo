<?php

namespace App\Http\Requests\Api\Community;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateCommunityRequest extends FormRequest
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
            'cover' => 'required',
            'logo' => 'required',
            'name' => 'required',
            'tagline' => 'required',
            'location' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'categories' => 'required',
            'type' => 'required',
            'mode' => 'required',
            'price' => 'required',
            'description' => 'required',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errorMessage = implode(', ', $validator->errors()->all());

        throw new HttpResponseException(response()->json([
            'status'   => false,
            'action' => $errorMessage
        ]));
    }
}
