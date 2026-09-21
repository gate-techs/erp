<?php

namespace Modules\Settings\Services;

use Illuminate\Support\Facades\File;
use Nwidart\Modules\Facades\Module;

class TranslationService
{
    /**
     * Get all available scopes/modules that support translations.
     *
     * @return array<array{id: string, name: string, path: string}>
     */
    public function getModules(): array
    {
        $modules = [
            [
                'id' => 'core',
                'name' => 'Core System',
                'path' => base_path('lang'),
            ],
        ];

        foreach (Module::all() as $module) {
            $langPath = $module->getPath() . DIRECTORY_SEPARATOR . 'lang';
            $modules[] = [
                'id' => $module->getName(),
                'name' => $module->getName() . ' Module',
                'path' => $langPath,
            ];
        }

        return $modules;
    }

    /**
     * Resolve the lang folder path for a given module/scope.
     *
     * @param string $moduleId
     * @return string
     */
    public function getLangPath(string $moduleId = 'core'): string
    {
        if ($moduleId === 'core' || empty($moduleId)) {
            return base_path('lang');
        }

        $module = Module::find($moduleId);
        if ($module) {
            return $module->getPath() . DIRECTORY_SEPARATOR . 'lang';
        }

        return base_path('lang');
    }

    /**
     * Load translations for a specific module, merging Arabic and English keys.
     *
     * @param string $moduleId
     * @return array<int, array{key: string, ar: string, en: string}>
     */
    public function getTranslations(string $moduleId = 'core'): array
    {
        $langPath = $this->getLangPath($moduleId);
        File::ensureDirectoryExists($langPath);

        $arFile = $langPath . DIRECTORY_SEPARATOR . 'ar.json';
        $enFile = $langPath . DIRECTORY_SEPARATOR . 'en.json';

        $arData = File::exists($arFile) ? json_decode(File::get($arFile), true) ?? [] : [];
        $enData = File::exists($enFile) ? json_decode(File::get($enFile), true) ?? [] : [];

        // Combine all unique keys
        $allKeys = array_unique(array_merge(array_keys($arData), array_keys($enData)));
        sort($allKeys);

        $result = [];
        foreach ($allKeys as $key) {
            $result[] = [
                'key' => (string) $key,
                'ar' => $arData[$key] ?? '',
                'en' => $enData[$key] ?? '',
            ];
        }

        return $result;
    }

    /**
     * Return only the requested translation rows for a server-side DataTable.
     * The JSON files are still read once per request, but the browser receives
     * only one page instead of rendering the complete translation set.
     *
     * @return array{data: array<int, array{key: string, ar: string, en: string}>, recordsTotal: int, recordsFiltered: int}
     */
    public function getTranslationsPage(string $moduleId, int $start, int $length, string $search = '', bool $missingOnly = false): array
    {
        $translations = $this->getTranslations($moduleId);
        $total = count($translations);
        $search = trim($search);

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $translations = array_values(array_filter($translations, static function (array $item) use ($needle): bool {
                return str_contains(mb_strtolower($item['key']), $needle)
                    || str_contains(mb_strtolower($item['ar']), $needle)
                    || str_contains(mb_strtolower($item['en']), $needle);
            }));
        }

        if ($missingOnly) {
            $translations = array_values(array_filter($translations, static function (array $item): bool {
                return $item['ar'] === '' || $item['en'] === '';
            }));
        }

        return [
            'data' => array_slice($translations, max(0, $start), max(1, $length)),
            'recordsTotal' => $total,
            'recordsFiltered' => count($translations),
        ];
    }

    /**
     * Update one translation key without requiring all DataTable pages in the request.
     */
    public function updateTranslation(string $moduleId, string $key, ?string $ar, ?string $en): bool
    {
        $translations = $this->getTranslations($moduleId);
        $updated = false;

        foreach ($translations as &$translation) {
            if ($translation['key'] === $key) {
                $translation['ar'] = (string) ($ar ?? '');
                $translation['en'] = (string) ($en ?? '');
                $updated = true;
                break;
            }
        }
        unset($translation);

        if (!$updated) {
            return false;
        }

        return $this->updateTranslations($moduleId, $translations);
    }

    /**
     * Bulk update translations for a specific module.
     *
     * @param string $moduleId
     * @param array<string, array{ar?: string, en?: string}> $translations
     * @return bool
     */
    public function updateTranslations(string $moduleId, array $translations): bool
    {
        $langPath = $this->getLangPath($moduleId);
        File::ensureDirectoryExists($langPath);

        $arFile = $langPath . DIRECTORY_SEPARATOR . 'ar.json';
        $enFile = $langPath . DIRECTORY_SEPARATOR . 'en.json';

        $arData = [];
        $enData = [];

        foreach ($translations as $key => $values) {
            if (is_array($values) && isset($values['key'])) {
                $cleanKey = trim((string) $values['key']);
                $arVal = $values['ar'] ?? '';
                $enVal = $values['en'] ?? '';
            } else {
                $cleanKey = trim((string) $key);
                $arVal = is_array($values) && isset($values['ar']) ? $values['ar'] : '';
                $enVal = is_array($values) && isset($values['en']) ? $values['en'] : '';
            }

            if ($cleanKey === '') {
                continue;
            }

            $arData[$cleanKey] = (string) $arVal;
            $enData[$cleanKey] = (string) $enVal;
        }

        // Save cleanly with Unicode preservation and pretty printing
        File::put($arFile, json_encode($arData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        File::put($enFile, json_encode($enData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return true;
    }

    /**
     * Add a single new translation key.
     *
     * @param string $moduleId
     * @param string $key
     * @param string|null $ar
     * @param string|null $en
     * @return bool
     */
    public function addTranslation(string $moduleId, string $key, ?string $ar = null, ?string $en = null): bool
    {
        $key = trim($key);
        if ($key === '') {
            return false;
        }

        $langPath = $this->getLangPath($moduleId);
        File::ensureDirectoryExists($langPath);

        $arFile = $langPath . DIRECTORY_SEPARATOR . 'ar.json';
        $enFile = $langPath . DIRECTORY_SEPARATOR . 'en.json';

        $arData = File::exists($arFile) ? json_decode(File::get($arFile), true) ?? [] : [];
        $enData = File::exists($enFile) ? json_decode(File::get($enFile), true) ?? [] : [];

        $arData[$key] = $ar ?? '';
        $enData[$key] = $en ?? $key;

        File::put($arFile, json_encode($arData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        File::put($enFile, json_encode($enData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return true;
    }

    /**
     * Delete a translation key from both languages.
     *
     * @param string $moduleId
     * @param string $key
     * @return bool
     */
    public function deleteTranslation(string $moduleId, string $key): bool
    {
        $langPath = $this->getLangPath($moduleId);

        $arFile = $langPath . DIRECTORY_SEPARATOR . 'ar.json';
        $enFile = $langPath . DIRECTORY_SEPARATOR . 'en.json';

        $arData = File::exists($arFile) ? json_decode(File::get($arFile), true) ?? [] : [];
        $enData = File::exists($enFile) ? json_decode(File::get($enFile), true) ?? [] : [];

        if (isset($arData[$key])) {
            unset($arData[$key]);
            File::put($arFile, json_encode($arData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        if (isset($enData[$key])) {
            unset($enData[$key]);
            File::put($enFile, json_encode($enData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        return true;
    }
}
