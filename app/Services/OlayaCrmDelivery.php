<?php

namespace App\Services;

use App\Models\Inquiry;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OlayaCrmDelivery
{
    public function store(array $data): Inquiry
    {
        $inquiry = new Inquiry($data);
        if (config('services.olaya_crm.enabled')) {
            $inquiry->forceFill(['olaya_request_id' => (string) Str::uuid(), 'olaya_status' => 'pending', 'olaya_next_attempt_at' => now()]);
        }
        // Persist the message and its delivery intent together, before any network call.
        $inquiry->save();
        return $inquiry;
    }

    public function send(int $id, bool $force = false): bool
    {
        if (! config('services.olaya_crm.enabled')) return false;

        return (bool) Cache::lock('olaya-crm-inquiry-'.$id, 60)->get(function () use ($id, $force) {
            $inquiry = Inquiry::find($id);
            if (! $inquiry || $inquiry->olaya_status === 'sent') return false;
            if ($force) {
                $inquiry->forceFill(['olaya_request_id' => $inquiry->olaya_request_id ?: (string) Str::uuid(),
                    'olaya_status' => 'pending', 'olaya_attempts' => 0, 'olaya_next_attempt_at' => now()])->save();
            }
            if ($inquiry->olaya_status !== 'pending' || ! $inquiry->olaya_request_id
                || ($inquiry->olaya_next_attempt_at && $inquiry->olaya_next_attempt_at->isFuture())) return false;

            $inquiry->forceFill(['olaya_attempts' => $inquiry->olaya_attempts + 1])->save();
            $url = (string) config('services.olaya_crm.url');
            $key = (string) config('services.olaya_crm.key');
            if (! $this->validUrl($url) || trim($key) === '') return $this->failure($inquiry, 'configuration', false);

            try {
                $response = Http::acceptJson()->asJson()->withHeaders(['X-Website-Key' => $key])
                    ->connectTimeout(3)->timeout(8)->withoutRedirecting()->post($url, [
                        'request_id' => $inquiry->olaya_request_id, 'name' => $inquiry->name,
                        'email' => $inquiry->email, 'phone' => $inquiry->phone,
                        'company' => $inquiry->company, 'message' => $inquiry->message,
                    ]);
            } catch (ConnectionException) {
                return $this->failure($inquiry, 'connection', true);
            }

            if (! $response->successful()) {
                return $this->failure($inquiry, 'http_'.$response->status(),
                    $response->serverError() || in_array($response->status(), [408, 425, 429], true));
            }
            $leadId = $response->json('data.id');
            $validLead = is_int($leadId) && $leadId > 0;
            $removed = $leadId === null && $response->json('duplicate') === true && $response->json('removed') === true;
            if ($response->json('request_id') !== $inquiry->olaya_request_id || (! $validLead && ! $removed)) {
                return $this->failure($inquiry, 'invalid_acknowledgement', true);
            }
            $inquiry->forceFill(['olaya_status' => 'sent', 'olaya_lead_id' => $leadId,
                'olaya_delivered_at' => now(), 'olaya_next_attempt_at' => null, 'olaya_last_error' => null])->save();
            return true;
        });
    }

    private function validUrl(string $url): bool
    {
        $parts = parse_url($url);
        if (! is_array($parts) || ! isset($parts['host']) || isset($parts['user']) || isset($parts['pass'])
            || isset($parts['query']) || isset($parts['fragment'])) return false;
        return ($parts['scheme'] ?? '') === 'https'
            || (app()->environment(['local', 'testing']) && ($parts['scheme'] ?? '') === 'http'
                && in_array($parts['host'], ['127.0.0.1', 'localhost', '[::1]'], true));
    }

    private function failure(Inquiry $inquiry, string $reason, bool $retry): bool
    {
        $retry = $retry && $inquiry->olaya_attempts < 8;
        $delay = [60, 300, 900, 3600][min(3, $inquiry->olaya_attempts - 1)];
        $inquiry->forceFill(['olaya_status' => $retry ? 'pending' : 'failed',
            'olaya_last_error' => $reason, 'olaya_next_attempt_at' => $retry ? now()->addSeconds($delay) : null])->save();
        // Never record the message, email, phone, response body or secret in logs.
        Log::warning('OLAYA CRM inquiry delivery pending or failed.', ['inquiry_id' => $inquiry->id, 'reason' => $reason]);
        return false;
    }
}
