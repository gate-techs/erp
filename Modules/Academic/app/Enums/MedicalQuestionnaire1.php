<?php

namespace Modules\Academic\Enums;

enum MedicalQuestionnaire1: int
{
    case MEDICATION_ALLERGY     = 1;
    case DIETARY_PROBLEMS       = 2;
    case FRACTURE_OR_SURGERY    = 3;
    case CURRENT_MEDICATIONS    = 4;
    case SUMMER_VACCINATIONS    = 5;
    case CONGENITAL_DEFORMITIES = 6;
    case MEDICAL_GLASSES        = 7;
    case HEARING_PROBLEMS       = 8;
    case SPORTS_RESTRICTION     = 9;
    case CONTINUOUS_TREATMENT   = 10;

    //------------------------------------------
    /**
     * Get the translation key for the question.
     */
    public function translationKey(): string
    {
        return match ($this) {
            self::MEDICATION_ALLERGY     => 'Does your child suffer from allergy to any type of medication?',
            self::DIETARY_PROBLEMS       => 'Does your child suffer from any dietary or nutritional problems?',
            self::FRACTURE_OR_SURGERY    => 'Has your child had a fracture or undergone surgical operation?',
            self::CURRENT_MEDICATIONS    => 'Does your child take any kind of medications?',
            self::SUMMER_VACCINATIONS    => 'Did the child receive any vaccination during the summer holiday?',
            self::CONGENITAL_DEFORMITIES => 'Does the child suffer from any congenital deformities?',
            self::MEDICAL_GLASSES        => 'Does the child wear medical eyeglasses?',
            self::HEARING_PROBLEMS       => 'Does the child suffer from hearing problems?',
            self::SPORTS_RESTRICTION     => 'Are there any reasons preventing the child from participating in sports activities?',
            self::CONTINUOUS_TREATMENT   => 'Does your child follow a specific ongoing treatment or regularly take medicine?',
        };
    }

    //------------------------------------------
    /**
     * Get the translated question text using Laravel localization.
     */
    public function question(?string $locale = null): string
    {
        return __($this->translationKey(), [], $locale);
    }

    //------------------------------------------
    /**
     * Get the translated question label using Laravel localization.
     */
    public function label(?string $locale = null): string
    {
        return $this->question($locale);
    }

    //------------------------------------------
    /**
     * Get all questionnaire integer codes.
     *
     * @return array<int>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    //------------------------------------------
    /**
     * Get all questions formatted with code and localized text.
     *
     * @return array<int, array{code: int, question: string}>
     */
    public static function options(?string $locale = null): array
    {
        return array_map(fn (self $case) => [
            'code'     => $case->value,
            'question' => $case->question($locale),
        ], self::cases());
    }
}
