<?php

namespace Modules\AI\Contracts;

interface TextGenerationProviderInterface
{
    public function generate(string $prompt, array $options = []): string;
}
