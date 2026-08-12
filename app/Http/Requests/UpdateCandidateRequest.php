<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama'      => ['required', 'string', 'max:255'],
            'company'   => ['nullable', 'string', 'max:255'],
            'urutan'    => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'foto'      => ['nullable', 'image', 'mimes:jpeg,jpg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPEG.',
            'foto.max'   => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
