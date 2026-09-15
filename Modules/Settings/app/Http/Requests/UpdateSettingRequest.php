<?php

namespace Modules\Settings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
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
            // Branding
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,ico,svg', 'max:1024'],
            'primary_color' => ['nullable', 'string', 'max:20'],

            // Contact
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'emergency_phone' => ['nullable', 'string', 'max:50'],

            // System & Financial
            'currency' => ['nullable', 'string', 'max:10'],
            'currency_symbol' => ['nullable', 'string', 'max:10'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'default_locale' => ['nullable', 'string', 'in:ar,en'],
            'tax_number' => ['nullable', 'string', 'max:100'],

            // Translatable Fields (Arabic)
            'ar.site_name' => ['nullable', 'string', 'max:255'],
            'ar.slogan' => ['nullable', 'string', 'max:255'],
            'ar.address' => ['nullable', 'string'],
            'ar.footer_text' => ['nullable', 'string'],

            // Translatable Fields (English)
            'en.site_name' => ['nullable', 'string', 'max:255'],
            'en.slogan' => ['nullable', 'string', 'max:255'],
            'en.address' => ['nullable', 'string'],
            'en.footer_text' => ['nullable', 'string'],

            // Social Media
            'facebook' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'twitter' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],
            'youtube' => ['nullable', 'string', 'max:255'],
        ];
    }
}
