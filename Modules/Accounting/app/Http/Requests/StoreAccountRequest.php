<?php

namespace Modules\Accounting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Accounting\Enums\AccountType;

class StoreAccountRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $entityId = $this->input('entity_id');

        return [
            'entity_id'      => ['required', 'integer', 'exists:entities,id'],
            'parent_id'      => [
                'nullable',
                'integer',
                Rule::exists('accounts', 'id')->where(function ($query) use ($entityId) {
                    if ($entityId) {
                        $query->where('entity_id', $entityId);
                    }
                }),
            ],
            'code'           => [
                'required',
                'string',
                'max:50',
                Rule::unique('accounts', 'code')->where(function ($query) use ($entityId) {
                    return $query->where('entity_id', $entityId)->whereNull('deleted_at');
                }),
            ],
            'account_type'   => ['required', 'string', Rule::in(AccountType::values())],
            'is_group'       => ['nullable', 'boolean'],
            'allow_posting'  => ['nullable', 'boolean'],
            'reconcile'      => ['nullable', 'boolean'],
            'is_active'      => ['nullable', 'boolean'],
            'is_deprecated'  => ['nullable', 'boolean'],
            'sort_order'     => ['nullable', 'integer', 'min:0'],
            'name_ar'        => ['required', 'string', 'max:255'],
            'name_en'        => ['nullable', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string', 'max:1000'],
            'description_en' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'entity_id'      => __('Entity'),
            'parent_id'      => __('Parent Account'),
            'code'           => __('Account Code'),
            'account_type'   => __('Account Type'),
            'is_group'       => __('Is Group Account'),
            'allow_posting'  => __('Allow Posting'),
            'reconcile'      => __('Reconcile'),
            'is_active'      => __('Status'),
            'is_deprecated'  => __('Is Deprecated'),
            'sort_order'     => __('Sort Order'),
            'name_ar'        => __('Arabic Name'),
            'name_en'        => __('English Name'),
            'description_ar' => __('Arabic Description'),
            'description_en' => __('English Description'),
        ];
    }
}
