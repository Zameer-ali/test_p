<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'email'          => 'required|string|max:255|email|unique:users,email',
            'password'       => 'required|string|min:6',
            'phone'          => 'required|string|regex:/^[0-9]{10}$/|unique:users,phone',
            'subjects'       => 'required|array',
            'subjects.*'     => 'required',
            'documents'      => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $hasImage = false;
                    $hasPDF = false;
                    $hasDocument = false;

                    foreach ($value as $file) {
                        $extension = strtolower($file->getClientOriginalExtension());

                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                            $hasImage = true;
                        } elseif ($extension === 'pdf') {
                            $hasPDF = true;
                        } elseif (in_array($extension, ['doc', 'docx'])) {
                            $hasDocument = true;
                        }
                    }

                    if (!($hasImage && $hasPDF && $hasDocument)) {
                        $fail('The ' . $attribute . ' must contain at least one image, one PDF, and one document file.');
                    }
                },
            ],
            'documents.*' => 'file',
        ];
    }
}
