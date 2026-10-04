<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'settings_email_templates';

    protected $fillable = [
        'code',
        'module',
        'name',
        'name_ar',
        'description',
        'subject_ar',
        'subject_en',
        'body_ar',
        'body_en',
        'variables',
        'action_button_text_ar',
        'action_button_text_en',
        'action_url_variable',
        'is_active',
        'is_system',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    //-----------------------------------------
    /**
     * Get normalized list of variables as key => description string.
     */
    public function getNormalizedVariablesAttribute(): array
    {
        $raw = $this->variables ?? [];
        $normalized = [];
        $isAr = app()->getLocale() === 'ar';

        foreach ($raw as $k => $v) {
            if (is_array($v)) {
                $key = $v['key'] ?? (is_string($k) ? $k : '');
                $desc = $isAr ? ($v['desc_ar'] ?? $v['desc_en'] ?? $key) : ($v['desc_en'] ?? $v['desc_ar'] ?? $key);
                if ($key) {
                    $normalized[$key] = $desc;
                }
            } elseif (is_string($v)) {
                $key = is_string($k) ? $k : $v;
                $normalized[$key] = $v;
            }
        }

        return $normalized;
    }

    //-----------------------------------------
    /**
     * Get display name based on current application locale.
     */
    public function getDisplayNameAttribute(): string
    {
        if (app()->getLocale() === 'ar' && !empty($this->name_ar)) {
            return $this->name_ar;
        }

        return $this->name ?? '';
    }

    //-----------------------------------------
    /**
     * Get subject based on locale.
     */
    public function getSubject(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        if ($locale === 'ar' && !empty($this->subject_ar)) {
            return $this->subject_ar;
        }
        return $this->subject_en ?: $this->subject_ar ?: '';
    }

    //-----------------------------------------
    /**
     * Get body content based on locale.
     */
    public function getBody(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        if ($locale === 'ar' && !empty($this->body_ar)) {
            return $this->body_ar;
        }
        return $this->body_en ?: $this->body_ar ?: '';
    }

    //-----------------------------------------
    /**
     * Get button text based on locale.
     */
    public function getButtonText(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        if ($locale === 'ar' && !empty($this->action_button_text_ar)) {
            return $this->action_button_text_ar;
        }
        return $this->action_button_text_en ?: $this->action_button_text_ar;
    }

    //-----------------------------------------
    /**
     * Render subject by replacing dynamic placeholder tags.
     */
    public function renderSubject(array $data = [], ?string $locale = null): string
    {
        $subject = $this->getSubject($locale);
        return $this->replacePlaceholders($subject, $data);
    }

    //-----------------------------------------
    /**
     * Render body by replacing dynamic placeholder tags.
     */
    public function renderBody(array $data = [], ?string $locale = null): string
    {
        $body = $this->getBody($locale);
        return $this->replacePlaceholders($body, $data);
    }

    //-----------------------------------------
    /**
     * Render button URL and text.
     */
    public function renderButton(array $data = [], ?string $locale = null): ?array
    {
        $text = $this->getButtonText($locale);
        if (!$text) {
            return null;
        }

        $url = $this->action_url_variable ? ($data[$this->action_url_variable] ?? $data[trim($this->action_url_variable, '{}')] ?? null) : null;
        if (!$url && isset($data['action_url'])) {
            $url = $data['action_url'];
        }

        if (!$url) {
            return null;
        }

        return [
            'text' => $this->replacePlaceholders($text, $data),
            'url'  => $url,
        ];
    }

    //-----------------------------------------
    /**
     * Render complete responsive HTML email.
     */
    public function renderFullHtml(array $data = [], ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $renderedSubject = $this->renderSubject($data, $locale);
        $renderedBody    = $this->renderBody($data, $locale);
        $actionButton    = $this->renderButton($data, $locale);

        return view('settings::emails.master', [
            'template'     => $this,
            'subject'      => $renderedSubject,
            'bodyContent'  => $renderedBody,
            'actionButton' => $actionButton,
            'locale'       => $locale,
            'data'         => $data,
        ])->render();
    }

    //-----------------------------------------
    /**
     * Replace dynamic placeholders {key} with values from data array.
     */
    protected function replacePlaceholders(string $text, array $data): string
    {
        // Add standard system placeholders if not provided
        if (!isset($data['{company_name}']) && !isset($data['company_name'])) {
            $data['{company_name}'] = config('app.name', 'ERP System');
        }
        if (!isset($data['{current_year}']) && !isset($data['current_year'])) {
            $data['{current_year}'] = date('Y');
        }
        if (!isset($data['{system_url}']) && !isset($data['system_url'])) {
            $data['{system_url}'] = url('/');
        }

        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }

            $tag = str_starts_with($key, '{') && str_ends_with($key, '}') ? $key : '{' . $key . '}';
            $text = str_replace($tag, (string) $value, $text);
        }

        return $text;
    }

    //-----------------------------------------
    /**
     * Scope for active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    //-----------------------------------------
    /**
     * Scope for filtering by module.
     */
    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }
}
