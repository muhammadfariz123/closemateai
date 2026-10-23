<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailDeliveryService
{
    /**
     * Send email using the best available channel:
     * 1. Brevo REST API (HTTPS port 443 - works everywhere, never blocked)
     * 2. Resend REST API (HTTPS port 443)
     * 3. Laravel default Mailer (SMTP / failover)
     */
    public static function send($toEmail, $toName, $subject, $htmlContent): array
    {
        $brevoApiKey = env('BREVO_API_KEY') ?: config('services.brevo.key');
        $fromEmail = env('MAIL_FROM_ADDRESS', 'notifsistem19@gmail.com');
        $fromName = env('MAIL_FROM_NAME', 'CloseMateAI');

        // 1. Try Brevo REST API if API Key is available
        if (!empty($brevoApiKey)) {
            try {
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'api-key' => $brevoApiKey,
                    'content-type' => 'application/json',
                ])
                ->timeout(10)
                ->post('https://api.brevo.com/v3/smtp/email', [
                    'sender' => [
                        'name' => $fromName,
                        'email' => $fromEmail,
                    ],
                    'to' => [
                        [
                            'email' => $toEmail,
                            'name' => $toName ?: explode('@', $toEmail)[0],
                        ],
                    ],
                    'subject' => $subject,
                    'htmlContent' => $htmlContent,
                ]);

                if ($response->successful()) {
                    Log::info("Email successfully sent via Brevo API to {$toEmail}: " . $response->body());
                    return [
                        'success' => true,
                        'driver' => 'brevo_api',
                        'message_id' => $response->json('messageId'),
                    ];
                } else {
                    Log::error("Brevo API error for {$toEmail}: " . $response->body());
                }
            } catch (\Throwable $e) {
                Log::error("Brevo API exception for {$toEmail}: " . $e->getMessage());
            }
        }

        // 2. Try Resend REST API if available
        $resendApiKey = env('RESEND_API_KEY') ?: config('services.resend.key');
        if (!empty($resendApiKey)) {
            try {
                $response = Http::withToken($resendApiKey)
                    ->timeout(10)
                    ->post('https://api.resend.com/emails', [
                        'from' => "{$fromName} <{$fromEmail}>",
                        'to' => [$toEmail],
                        'subject' => $subject,
                        'html' => $htmlContent,
                    ]);

                if ($response->successful()) {
                    Log::info("Email successfully sent via Resend API to {$toEmail}: " . $response->body());
                    return [
                        'success' => true,
                        'driver' => 'resend_api',
                        'message_id' => $response->json('id'),
                    ];
                } else {
                    Log::error("Resend API error for {$toEmail}: " . $response->body());
                }
            } catch (\Throwable $e) {
                Log::error("Resend API exception for {$toEmail}: " . $e->getMessage());
            }
        }

        // 3. Fallback to Laravel Mailer (SMTP / Failover)
        try {
            Mail::html($htmlContent, function ($m) use ($toEmail, $toName, $subject, $fromEmail, $fromName) {
                $m->from($fromEmail, $fromName)
                  ->to($toEmail, $toName ?: null)
                  ->subject($subject);
            });

            Log::info("Email sent via Laravel Mailer to {$toEmail}");
            return [
                'success' => true,
                'driver' => 'laravel_mailer',
            ];
        } catch (\Throwable $e) {
            Log::error("Laravel Mailer failed for {$toEmail}: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
