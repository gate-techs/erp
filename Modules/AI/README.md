# AI Module

The AI module provides provider-independent AI capabilities for the ERP.

## Configuration

Open `/settings/ai` and add the provider from the admin screen. API keys are encrypted in the database. The `.env` values remain available as a fallback for local development:

```env
AI_DEFAULT_PROVIDER=gemini
GEMINI_API_KEY=
GEMINI_MODEL=gemini-2.5-flash
OPENAI_API_KEY=
OPENAI_MODEL=gpt-4o-mini
DEEPL_API_KEY=
```

## Usage

Use the module service instead of calling a provider directly:

```php
use Modules\AI\Services\AIService;

$translation = app(AIService::class)->translate('الأصول', 'ar', 'en');
$summary = app(AIService::class)->generate('Summarize this report...', [], 'openai');
```

The Accounting, Sales, and HR modules should depend on `AIService` or the module contracts, never on Gemini, OpenAI, or DeepL classes directly.

## API endpoints

Both endpoints require `auth:sanctum`:

- `POST /api/v1/ai/translate`
- `POST /api/v1/ai/generate`
