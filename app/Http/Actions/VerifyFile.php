<?php

namespace App\Http\Actions;

use App\Models\Verification;
use Carbon\Carbon;
use App\Http\DTO\VerifyFileResult;
use App\Enums\FileVerification\ResultStatus;
use Illuminate\Pipeline\Pipeline;
use App\Pipes\VerifyFile\ValidateRecipient;
use App\Pipes\VerifyFile\ValidateIssuer;
use App\Pipes\VerifyFile\ValidateSignature;

class VerifyFile
{
    public function __invoke(string $fileContent): VerifyFileResult
    {
        $fileContent = json_decode($fileContent, JSON_OBJECT_AS_ARRAY) ?? [];

        return app(Pipeline::class)
            ->send($fileContent)
            ->through([
                ValidateRecipient::class,
                ValidateIssuer::class,
                ValidateSignature::class,
            ])
            ->then(function () use ($fileContent) {
                return new VerifyFileResult(
                    resultStatus: ResultStatus::Verified,
                    issuer: $fileContent['data']['issuer']['name']
                );
            });
    }

    private function storeAndRespond($request, $result, $issuer = null)
    {
        // Store the verification result
        Verification::create([
            'user_id' => $request->user()->id,
            'file_type' => 'json',
            'result_status' => $result,
            'issuer_name' => $issuer,
            'created_at' => Carbon::now(),

            // private function verifyIssuer(string $dns, string $key): bool
            // {
            //     $dnsResponse = Http::get('https://dns.google/resolve', [
            //         'name' => $dns,
            //         'type' => 'TXT'
            //     ]);

            //     $dnsRecords = collect($dnsResponse->json('Answer', []))
            //         ->pluck('data')
            //         ->toArray();

            //     foreach ($dnsRecords as $record) {
            //         if (Str::contains($record, $key)) {
            //             return true;
            //         }
            //     }

            //     return false;
            // }
        ]);

        // Return the response
        return response()->json([
            'data' => [
                'issuer' => $issuer,
                'result' => $result,
            ]
        ], 200);
    }
}
