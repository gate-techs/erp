<?php

namespace Modules\Academic\Enums;

enum DiseaseType: int
{
    case DIABETES        = 1;
    case EPILEPSY        = 2;
    case ASTHMA          = 3;
    case BLOOD_DISEASES  = 4;
    case ALLERGY         = 5;
    case KIDNEY_DISEASES = 6;
    case HEART_DISEASES  = 7;
    case OTHER           = 8;

    //------------------------------------------
    /**
     * Get the translation key for the disease.
     */
    public function translationKey(): string
    {
        return match ($this) {
            self::DIABETES        => 'Diabetes',
            self::EPILEPSY        => 'Epilepsy',
            self::ASTHMA          => 'Asthma',
            self::BLOOD_DISEASES  => 'Blood Diseases',
            self::ALLERGY         => 'Allergy',
            self::KIDNEY_DISEASES => 'Kidney Diseases',
            self::HEART_DISEASES  => 'Heart Diseases',
            self::OTHER           => 'Other Diseases',
        };
    }

    //------------------------------------------
    /**
     * Get the translated label using Laravel localization.
     */
    public function label(?string $locale = null): string
    {
        return __($this->translationKey(), [], $locale);
    }

    //------------------------------------------
    /**
     * Get all disease integer codes.
     *
     * @return array<int>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    //------------------------------------------
    /**
     * Get all disease options formatted with code and localized label.
     *
     * @return array<int, array{code: int, label: string}>
     */
    public static function options(?string $locale = null): array
    {
        return array_map(fn (self $case) => [
            'code'  => $case->value,
            'label' => $case->label($locale),
        ], self::cases());
    }
}
