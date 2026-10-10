<?php

namespace Modules\Academic\Enums;

enum InformationForm: int
{
    // --- نموذج معلومات 1 (الأسئلة 1 - 12) ---
    case SIBLINGS_COUNT                  = 1;
    case SPEECH_DELAY                    = 2;
    case FAMILY_DISORDERS                = 3;
    case PSYCHOLOGICAL_CONDITION         = 4;
    case ABNORMAL_BEHAVIORS              = 5;
    case HOME_COOPERATION                = 6;
    case PREVIOUS_ABUSE                  = 7;
    case PSYCHOLOGIST_VISIT              = 8;
    case ANGER_TANTRUMS                  = 9;
    case ISOLATION_TENDENCY              = 10;
    case HYPERACTIVITY                   = 11;
    case PLAY_DIFFICULTY                 = 12;

    // --- نموذج معلومات 2 (الأسئلة 13 - 22) ---
    case JUMPING_AND_RUNNING             = 13;
    case SITTING_DIFFICULTY              = 14;
    case CLASSROOM_TALKING               = 15;
    case FRIENDS_VISIT_RESTLESS          = 16;
    case CALM_ACTIVITY_PARTICIPATION     = 17;
    case INAPPROPRIATE_CLIMBING          = 18;
    case BEDWETTING_OR_SEATED_URINATION  = 19;
    case SEDENTARY_ACTIVITY_DIFFICULTY   = 20;
    case FOCUS_DIFFICULTY                = 21;
    case EXCESSIVE_TALKING               = 22;

    //------------------------------------------
    /**
     * Get the form/tab number (1 or 2).
     */
    public function formNumber(): int
    {
        return $this->value <= 12 ? 1 : 2;
    }

    //------------------------------------------
    /**
     * Get the translatable key for the question.
     */
    public function translationKey(): string
    {
        return match ($this) {
            // نموذج معلومات 1
            self::SIBLINGS_COUNT                  => 'Does the child have siblings? How many?',
            self::SPEECH_DELAY                    => 'Does the child suffer from speech delay?',
            self::FAMILY_DISORDERS                => 'Are there any injuries, syndromes, or disorders in any family member? e.g. first, second, and third-degree relatives',
            self::PSYCHOLOGICAL_CONDITION         => 'Has your child ever been exposed to a severe psychological condition that affected him as a physical illness?',
            self::ABNORMAL_BEHAVIORS              => 'Have abnormal behaviors been observed in the child before?',
            self::HOME_COOPERATION                => 'Does the child cooperate at home?',
            self::PREVIOUS_ABUSE                  => 'Has anyone abused or harmed your child before?',
            self::PSYCHOLOGIST_VISIT              => 'Has your child been examined by a psychologist or psychiatrist before?',
            self::ANGER_TANTRUMS                  => 'Does the child have bouts of anger and stress?',
            self::ISOLATION_TENDENCY              => 'Does your child tend to isolate?',
            self::HYPERACTIVITY                   => 'Is the child constantly moving in a way that bothers those around him?',
            self::PLAY_DIFFICULTY                 => 'Does the child find difficulty in participating with children around him in quiet play?',

            // نموذج معلومات 2
            self::JUMPING_AND_RUNNING             => 'Is the child constantly jumping, running, and moving from one place to another?',
            self::SITTING_DIFFICULTY              => 'Does the child have difficulty sitting still for a long time, fidgeting while seated, and may leave their seat?',
            self::CLASSROOM_TALKING               => 'Is the child constantly talking with those around him in class?',
            self::FRIENDS_VISIT_RESTLESS          => 'Is the child constantly restless when visiting friends (with parents constantly occupied with him touching and damaging everything around him)?',
            self::CALM_ACTIVITY_PARTICIPATION     => 'Does the child participate in activities quietly?',
            self::INAPPROPRIATE_CLIMBING          => 'Does the child climb and run around at inappropriate times?',
            self::BEDWETTING_OR_SEATED_URINATION  => 'Does the child wet themselves constantly while seated?',
            self::SEDENTARY_ACTIVITY_DIFFICULTY   => 'Does the child have difficulty in activities that require sitting, such as reading?',
            self::FOCUS_DIFFICULTY                => 'Does the child find difficulty in focusing or sustaining enjoyment for long periods?',
            self::EXCESSIVE_TALKING               => 'Does the child talk excessively, causing problems in study and social settings?',
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
     * Get all integer codes.
     *
     * @return array<int>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    //------------------------------------------
    /**
     * Get cases belonging to Form 1 (questions 1 - 12).
     *
     * @return array<self>
     */
    public static function form1Cases(): array
    {
        return array_values(array_filter(self::cases(), fn (self $case) => $case->formNumber() === 1));
    }

    //------------------------------------------
    /**
     * Get cases belonging to Form 2 (questions 13 - 22).
     *
     * @return array<self>
     */
    public static function form2Cases(): array
    {
        return array_values(array_filter(self::cases(), fn (self $case) => $case->formNumber() === 2));
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

    //------------------------------------------
    /**
     * Get Form 1 questions formatted with code and localized text.
     *
     * @return array<int, array{code: int, question: string}>
     */
    public static function form1Options(?string $locale = null): array
    {
        return array_map(fn (self $case) => [
            'code'     => $case->value,
            'question' => $case->question($locale),
        ], self::form1Cases());
    }

    //------------------------------------------
    /**
     * Get Form 2 questions formatted with code and localized text.
     *
     * @return array<int, array{code: int, question: string}>
     */
    public static function form2Options(?string $locale = null): array
    {
        return array_map(fn (self $case) => [
            'code'     => $case->value,
            'question' => $case->question($locale),
        ], self::form2Cases());
    }
}
