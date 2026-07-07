<?php

namespace App\Services;

use App\Models\AIGeneration;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GeminiAIService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.api_key');
        $this->model = (string) config('services.gemini.model', 'gemini-2.5-flash-lite');
        $this->baseUrl = rtrim((string) config('services.gemini.base_url'), '/');
    }

    protected function endpoint(?string $model = null): string
    {
        $model = $model ?: $this->model;

        return "{$this->baseUrl}/models/{$model}:generateContent";
    }

    protected function extractText(array $data): string
    {
        return data_get($data, 'candidates.0.content.parts.0.text', '');
    }

    protected function extractUsage(array $data): array
    {
        return [
            'prompt_tokens' => data_get($data, 'usageMetadata.promptTokenCount'),
            'response_tokens' => data_get($data, 'usageMetadata.candidatesTokenCount'),
            'total_tokens' => data_get($data, 'usageMetadata.totalTokenCount'),
        ];
    }

    protected function sendPrompt(string $prompt, ?string $systemInstruction = null): array
    {
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topP' => 0.9,
                'maxOutputTokens' => 1200,
                'responseMimeType' => 'application/json',
            ],
        ];

        if ($systemInstruction) {
            $payload['systemInstruction'] = [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ];
        }

        $response = Http::timeout(40)
            ->retry(2, 1000)
            ->acceptJson()
            ->post($this->endpoint() . '?key=' . $this->apiKey, $payload);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'Gemini request failed: ' . $response->status() . ' - ' . $response->body()
            );
        }

        return $response->json();
    }

    public function generateCampaignContent(array $input, ?int $userId = null): array
    {
        $system = 'You are an expert B2B sales and email marketing assistant. Return only valid JSON.';

        $prompt = <<<PROMPT
Generate a professional outreach email campaign.

Campaign Goal: {$input['goal']}
Target Audience: {$input['audience']}
Tone: {$input['tone']}
Offer / Service: {$input['offer']}

Return JSON exactly in this shape:
{
  "subject": "string",
  "body": "string"
}

Rules:
- Keep subject concise and compelling.
- Body should be readable and practical.
- Use placeholders like {name} and {company} where useful.
- No markdown. No code fences.
PROMPT;

        try {
            $raw = $this->sendPrompt($prompt, $system);
            $text = $this->extractText($raw);
            $decoded = json_decode($text, true);

            $result = [
                'subject' => $decoded['subject'] ?? 'AI Generated Subject',
                'body' => $decoded['body'] ?? $text,
            ];

            $usage = $this->extractUsage($raw);

            AIGeneration::create([
                'type' => 'campaign_content',
                'model' => $this->model,
                'user_id' => $userId,
                'input_payload' => $input,
                'output_payload' => $result,
                'prompt_tokens' => $usage['prompt_tokens'],
                'response_tokens' => $usage['response_tokens'],
                'total_tokens' => $usage['total_tokens'],
                'status' => 'success',
            ]);

            return $result;
        } catch (\Throwable $e) {
            AIGeneration::create([
                'type' => 'campaign_content',
                'model' => $this->model,
                'user_id' => $userId,
                'input_payload' => $input,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function generateLeadInsights(Lead $lead, ?int $userId = null): array
    {
        $system = 'You are a CRM sales assistant. Return only valid JSON.';

        $prompt = <<<PROMPT
Analyze this sales lead.

Name: {$lead->name}
Email: {$lead->email}
Phone: {$lead->phone}
Company: {$lead->company}
Source: {$lead->source}
Subject: {$lead->subject}
Message: {$lead->message}
Current Status: {$lead->status}
Priority: {$lead->priority}

Return JSON exactly in this shape:
{
  "summary": "short summary",
  "score": 0,
  "next_action": "practical next action"
}

Rules:
- score must be integer from 0 to 100
- summary concise ho
- next_action practical ho
- no markdown
- no code fences
PROMPT;

        try {
            $raw = $this->sendPrompt($prompt, $system);
            $text = $this->extractText($raw);
            $decoded = json_decode($text, true);

            $result = [
                'summary' => $decoded['summary'] ?? $text,
                'score' => max(0, min(100, (int) ($decoded['score'] ?? 50))),
                'next_action' => $decoded['next_action'] ?? 'Review manually and follow up.',
            ];

            $usage = $this->extractUsage($raw);

            AIGeneration::create([
                'type' => 'lead_insight',
                'model' => $this->model,
                'user_id' => $userId,
                'lead_id' => $lead->id,
                'input_payload' => [
                    'lead_id' => $lead->id,
                    'lead_name' => $lead->name,
                ],
                'output_payload' => $result,
                'prompt_tokens' => $usage['prompt_tokens'],
                'response_tokens' => $usage['response_tokens'],
                'total_tokens' => $usage['total_tokens'],
                'status' => 'success',
            ]);

            return $result;
        } catch (\Throwable $e) {
            AIGeneration::create([
                'type' => 'lead_insight',
                'model' => $this->model,
                'user_id' => $userId,
                'lead_id' => $lead->id,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
        public function generateOtpMessage(?string $name, string $otp, ?int $userId = null): string
    {
        $system = 'You are a helpful assistant generating SMS messages. Return only the SMS text content, no JSON, no markdown.';

        $nameStr = $name ? "named {$name}" : "with an unknown name";

        $prompt = <<<PROMPT
Generate a short, professional, and friendly SMS message for an app user {$nameStr}.
Their OTP code is {$otp}. Let them know the code expires in 10 minutes.
Keep it under 160 characters if possible. Do not include any signature.
PROMPT;

        try {
            $raw = $this->sendPrompt($prompt, $system);
            $text = $this->extractText($raw);

            $usage = $this->extractUsage($raw);

            AIGeneration::create([
                'type' => 'otp_sms',
                'model' => $this->model,
                'user_id' => $userId,
                'input_payload' => ['name' => $name, 'otp' => $otp],
                'output_payload' => ['text' => $text],
                'prompt_tokens' => $usage['prompt_tokens'],
                'response_tokens' => $usage['response_tokens'],
                'total_tokens' => $usage['total_tokens'],
                'status' => 'success',
            ]);

            return trim($text);
        } catch (\Throwable $e) {
            AIGeneration::create([
                'type' => 'otp_sms',
                'model' => $this->model,
                'user_id' => $userId,
                'input_payload' => ['name' => $name, 'otp' => $otp],
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}