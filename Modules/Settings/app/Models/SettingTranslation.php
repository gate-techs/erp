<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SettingTranslation extends Model
{
    use HasFactory;

    /**
     * Disable timestamps for translation table.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'setting_id',
        'locale',
        'site_name',
        'slogan',
        'address',
        'footer_text',
    ];
}
