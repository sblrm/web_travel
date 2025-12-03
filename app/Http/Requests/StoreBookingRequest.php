<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'visit_date' => ['required', 'date', 'after_or_equal:today'],
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'visitor_name' => ['required', 'string', 'max:255'],
            'visitor_email' => ['required', 'email', 'max:255'],
            'visitor_phone' => ['required', 'string', 'max:20'],
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
            'visit_date' => 'tanggal kunjungan',
            'quantity' => 'jumlah pengunjung',
            'payment_method_id' => 'metode pembayaran',
            'visitor_name' => 'nama pengunjung',
            'visitor_email' => 'email pengunjung',
            'visitor_phone' => 'nomor telepon',
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
            'visit_date.after_or_equal' => 'Tanggal kunjungan tidak boleh di masa lalu.',
            'quantity.min' => 'Minimal 1 pengunjung.',
            'quantity.max' => 'Maksimal 50 pengunjung per booking.',
        ];
    }
}
