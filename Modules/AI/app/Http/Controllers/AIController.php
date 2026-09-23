<?php

namespace Modules\AI\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Modules\AI\Exceptions\AIException;
use Modules\AI\Services\AIService;

class AIController extends Controller
{
    public function __construct(private readonly AIService $ai)
    {
    }

    public function translate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'text' => ['required', 'string', 'max:10000'],
            'source_language' => ['required', 'string', 'size:2'],
            'target_language' => ['required', 'string', 'size:2', 'different:source_language'],
            'provider' => ['nullable', 'string', Rule::in(array_keys(config('ai.providers', [])))],
        ]);

        try {
            return response()->json([
                'data' => [
                    'translation' => $this->ai->translate(
                        $data['text'],
                        $data['source_language'],
                        $data['target_language'],
                        $data['provider'] ?? null,
                    ),
                ],
            ]);
        } catch (AIException $exception) {
            report($exception);

            return response()->json([
                'message' => 'AI provider request failed.',
            ], 502);
        }
    }

    public function generate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'prompt' => ['required', 'string', 'max:30000'],
            'provider' => ['nullable', 'string', Rule::in(array_keys(config('ai.providers', [])))],
            'temperature' => ['nullable', 'numeric', 'min:0', 'max:2'],
            'max_output_tokens' => ['nullable', 'integer', 'min:1', 'max:8192'],
        ]);

        try {
            return response()->json([
                'data' => [
                    'text' => $this->ai->generate(
                        $data['prompt'],
                        array_filter([
                            'temperature' => $data['temperature'] ?? null,
                            'max_output_tokens' => $data['max_output_tokens'] ?? null,
                        ], static fn ($value) => $value !== null),
                        $data['provider'] ?? null,
                    ),
                ],
            ]);
        } catch (AIException $exception) {
            report($exception);

            return response()->json([
                'message' => 'AI provider request failed.',
            ], 502);
        }
    }

    public function translateName(Request $request): JsonResponse
    {
        $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
        ]);

        $nameAr = trim($request->input('name_ar'));
        if ($nameAr === '') {
            return response()->json([
                'success' => false,
                'message' => __('Please provide an Arabic name.'),
            ], 422);
        }

        try {
            $prompt = "Transliterate the following Arabic full name of a person into standard English (Latin alphabet), with proper Title Case capitalization (e.g. 'Ahmed Mohamed Ali'). Output ONLY the Romanized English name with NO extra words, quotes, punctuation, or explanations:\n\n{$nameAr}";

            $aiText = $this->ai->generate($prompt, [
                'temperature' => 0.1,
                'task' => 'translation',
            ]);

            $cleaned = trim(preg_replace('/^["\'`]+|["\'`]+$/u', '', $aiText));

            if ($cleaned === '' || !preg_match('/[a-zA-Z]/', $cleaned)) {
                return response()->json([
                    'success' => false,
                    'message' => __('AI returned an empty or invalid response.'),
                ], 422);
            }

            return response()->json([
                'success' => true,
                'name_en' => $cleaned,
                'source' => 'ai',
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: __('AI provider request failed.'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
