<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => ['required', 'string', 'regex:/^(0)(5|6|7)[0-9]{8}$/'],
            'wilaya_id'        => 'required|exists:wilayas,id',
            'commune_id'       => 'required|exists:communes,id',
            'items'            => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1|max:99',
            'items.*.options'    => 'nullable|array',
            '_hp_field'        => 'nullable|string|max:0', // honeypot: must be empty
        ];
    }

    public function messages(): array
    {
        return [
            'customer_phone.regex'  => 'Numéro de téléphone algérien invalide (ex: 0555123456).',
            'customer_phone2.regex' => 'Numéro de téléphone secondaire invalide.',
            '_hp_field.max'         => 'Formulaire invalide.',
        ];
    }
}
