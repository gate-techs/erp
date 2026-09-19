<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrWorkPolicy extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hr_work_policies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'label_ar',
        'label_en',
        'options',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options'    => 'array',
        'sort_order' => 'integer',
    ];

    //======================
    /**
     * Get a policy value by key with optional default fallback.
     */
    public static function getVal(string $key, mixed $default = null): mixed
    {
        $policy = static::where('key', $key)->first();
        if (!$policy) {
            return $default;
        }

        return match ($policy->type) {
            'number'  => is_numeric($policy->value) ? (str_contains((string)$policy->value, '.') ? (float)$policy->value : (int)$policy->value) : $default,
            'boolean' => filter_var($policy->value, FILTER_VALIDATE_BOOLEAN),
            default   => $policy->value ?? $default,
        };
    }

    //======================
    /**
     * Set a policy value by key.
     */
    public static function setVal(string $key, mixed $value, ?string $group = null): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            array_filter([
                'value' => is_bool($value) ? ($value ? '1' : '0') : (string)$value,
                'group' => $group,
            ], fn($v) => !is_null($v))
        );
    }

    //======================
    /**
     * Get all policy key-value pairs for a specific group as an associative array.
     */
    public static function getGroup(string $group): array
    {
        $policies = static::where('group', $group)->orderBy('sort_order')->get();
        $result = [];

        foreach ($policies as $p) {
            $val = match ($p->type) {
                'number'  => is_numeric($p->value) ? (str_contains((string)$p->value, '.') ? (float)$p->value : (int)$p->value) : $p->value,
                'boolean' => filter_var($p->value, FILTER_VALIDATE_BOOLEAN),
                default   => $p->value,
            };
            $result[$p->key] = $val;
        }

        return $result;
    }
}
