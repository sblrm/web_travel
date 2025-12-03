<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadPaymentProofRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'proof_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'account_holder_name' => ['required', 'string', 'max:255'],
            'transfer_from' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'proof_image' => 'bukti pembayaran',
            'account_holder_name' => 'nama pemilik rekening',
            'transfer_from' => 'transfer dari',
            'notes' => 'catatan',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'proof_image.required' => 'Bukti pembayaran wajib diupload.',
            'proof_image.image' => 'File harus berupa gambar.',
            'proof_image.mimes' => 'Format gambar harus JPG, JPEG, atau PNG.',
            'proof_image.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
