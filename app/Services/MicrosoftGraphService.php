<?php

namespace App\Services;

use App\Models\Email;
use App\Models\MicrosoftAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Microsoft\Graph\Graph;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Abstractions\ApiException;

class MicrosoftGraphService
{
    private MicrosoftAccount $account;

    public function __construct(MicrosoftAccount $account)
    {
        $this->account = $account;
    }

    /**
     * Get access token from account's stored token
     */
    private function getAccessToken(): ?string
    {
        return $this->account->getAccessToken();
    }

    /**
     * Refresh access token if needed
     */
    public function refreshTokenIfNeeded(): bool
    {
        return $this->account->refreshTokenIfNeeded();
    }

    /**
     * Fetch emails from Outlook inbox
     */
    public function fetchEmails(int $limit = 50): array
    {
        if (!$this->refreshTokenIfNeeded()) {
            throw new \Exception('Unable to refresh access token');
        }

        try {
            $response = Http::withToken($this->getAccessToken())
                ->get('https://graph.microsoft.com/v1.0/me/mailFolders/inbox/messages', [
                    '$top' => $limit,
                    '$orderby' => 'receivedDateTime desc',
                    '$select' => 'id,subject,body,bodyPreview,from,toRecipients,ccRecipients,bccRecipients,receivedDateTime,isRead,hasAttachments,attachments',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['value'] ?? [];
            }

            throw new \Exception('Failed to fetch emails: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Failed to fetch emails from Microsoft Graph', [
                'microsoft_account_id' => $this->account->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Sync emails to database
     */
    public function syncEmails(int $limit = 50): int
    {
        $emails = $this->fetchEmails($limit);
        $synced = 0;

        foreach ($emails as $emailData) {
            try {
                $email = Email::updateOrCreate(
                    [
                        'message_id' => $emailData['id'],
                        'microsoft_account_id' => $this->account->id,
                    ],
                    [
                        'subject' => $emailData['subject'] ?? '(Sem assunto)',
                        'body_html' => $emailData['body']['content'] ?? null,
                        'body_text' => $emailData['bodyPreview'] ?? null,
                        'from_email' => $emailData['from']['emailAddress']['address'] ?? '',
                        'from_name' => $emailData['from']['emailAddress']['name'] ?? '',
                        'to' => collect($emailData['toRecipients'] ?? [])->map(fn($r) => [
                            'email' => $r['emailAddress']['address'] ?? '',
                            'name' => $r['emailAddress']['name'] ?? '',
                        ])->toArray(),
                        'cc' => collect($emailData['ccRecipients'] ?? [])->map(fn($r) => [
                            'email' => $r['emailAddress']['address'] ?? '',
                            'name' => $r['emailAddress']['name'] ?? '',
                        ])->toArray(),
                        'bcc' => collect($emailData['bccRecipients'] ?? [])->map(fn($r) => [
                            'email' => $r['emailAddress']['address'] ?? '',
                            'name' => $r['emailAddress']['name'] ?? '',
                        ])->toArray(),
                        'received_at' => $emailData['receivedDateTime'] ?? now(),
                        'is_read' => $emailData['isRead'] ?? false,
                        'outlook_id' => $emailData['id'],
                        'metadata' => $emailData,
                    ]
                );

                // Fetch attachments if needed
                if (($emailData['hasAttachments'] ?? false) && !$email->attachments) {
                    $attachments = $this->fetchEmailAttachments($emailData['id']);
                    $email->update(['attachments' => $attachments]);
                }

                $synced++;
            } catch (\Exception $e) {
                Log::error('Failed to sync email', [
                    'microsoft_account_id' => $this->account->id,
                    'email_id' => $emailData['id'] ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $synced;
    }

    /**
     * Fetch attachments for an email
     */
    private function fetchEmailAttachments(string $emailId): array
    {
        try {
            $response = Http::withToken($this->getAccessToken())
                ->get("https://graph.microsoft.com/v1.0/me/messages/{$emailId}/attachments");

            if ($response->successful()) {
                $data = $response->json();
                return collect($data['value'] ?? [])->map(fn($att) => [
                    'id' => $att['id'],
                    'name' => $att['name'],
                    'contentType' => $att['contentType'],
                    'size' => $att['size'],
                ])->toArray();
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch email attachments', [
                'email_id' => $emailId,
                'error' => $e->getMessage(),
            ]);
        }

        return [];
    }

    /**
     * Mark email as read in Outlook
     */
    public function markAsRead(string $emailId): bool
    {
        if (!$this->refreshTokenIfNeeded()) {
            return false;
        }

        try {
            $response = Http::withToken($this->getAccessToken())
                ->patch("https://graph.microsoft.com/v1.0/me/messages/{$emailId}", [
                    'isRead' => true,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to mark email as read', [
                'email_id' => $emailId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

