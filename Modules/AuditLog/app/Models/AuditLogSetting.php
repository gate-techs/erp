<?php

namespace Modules\AuditLog\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AuditLogSetting extends Model
{
    protected $table = 'audit_log_settings';

    protected $fillable = [
        'is_external_db_enabled',
        'driver',
        'host',
        'port',
        'database',
        'username',
        'password',
        'fallback_to_local',
        'is_queue_enabled',
        'last_tested_at',
        'last_test_status',
        'last_test_message',
    ];

    protected $casts = [
        'is_external_db_enabled' => 'boolean',
        'fallback_to_local'      => 'boolean',
        'is_queue_enabled'       => 'boolean',
        'last_tested_at'         => 'datetime',
    ];

    /**
     * Mutator to encrypt password when set.
     */
    public function setPasswordAttribute($value): void
    {
        if (!empty($value)) {
            $this->attributes['password'] = Crypt::encryptString($value);
        } else {
            $this->attributes['password'] = null;
        }
    }

    /**
     * Accessor to decrypt password.
     */
    public function getDecryptedPassword(): ?string
    {
        if (empty($this->attributes['password'])) {
            return null;
        }

        try {
            return Crypt::decryptString($this->attributes['password']);
        } catch (Exception) {
            return null;
        }
    }

    /**
     * Get or create single settings row.
     */
    public static function getOrCreate(): self
    {
        return static::firstOrCreate([], [
            'is_external_db_enabled' => false,
            'driver'                 => 'mysql',
            'host'                   => '127.0.0.1',
            'port'                   => '3306',
            'database'               => '',
            'username'               => '',
            'password'               => null,
            'fallback_to_local'      => true,
            'is_queue_enabled'       => false,
        ]);
    }
}
