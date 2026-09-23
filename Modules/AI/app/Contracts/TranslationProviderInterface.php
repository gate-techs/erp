<?php

namespace Modules\AI\Contracts;

interface TranslationProviderInterface
{
    public function translate(string $text, string $sourceLanguage, string $targetLanguage): string;
}
