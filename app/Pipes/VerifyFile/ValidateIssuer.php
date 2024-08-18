<?php

namespace App\Pipes\VerifyFile;

use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Http\DTO\VerifyFileResult;
use App\Enums\FileVerification\ResultStatus;

class ValidateIssuer
{
    const DNS_RESOLVER = 'https://dns.google/resolve';
    const DNS_TYPE = 'TXT';

    public function handle($content, Closure $next): VerifyFileResult
    {
        $validator = validator($content, [
            'data.issuer.name' => ['required', 'string'],
            'data.issuer.identityProof.key' => ['required', 'string'],
            'data.issuer.identityProof.location' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->invalidIssuerResult();
        }

        if (!$this->verifyIssuer(
            dns: data_get($content, 'data.issuer.identityProof.location'),
            key: data_get($content, 'data.issuer.identityProof.key')
        )) {
            return $this->invalidIssuerResult();
        }

        return $next($content);
    }

    private function invalidIssuerResult(): VerifyFileResult
    {
        return new VerifyFileResult(ResultStatus::InvalidIssuer);
    }

    private function verifyIssuer(string $dns, string $key): bool
    {
        $dnsResponse = Http::get(self::DNS_RESOLVER, [
            'name' => $dns,
            'type' => self::DNS_TYPE
        ]);

        $dnsRecords = collect($dnsResponse->json('Answer', []))
            ->pluck('data')
            ->toArray();

        foreach ($dnsRecords as $record) {
            if (Str::contains($record, $key)) {
                return true;
            }
        }

        return false;
    }
}
