<?php

namespace App\Pipes\VerifyFile;

use Closure;
use App\Enums\FileVerification\ResultStatus;
use App\Http\DTO\VerifyFileResult;
use Illuminate\Support\Arr;

class ValidateSignature
{
    const HASH_ALGORITHM = 'sha256';

    public function handle($content, Closure $next): VerifyFileResult
    {
        $fileContentValidator = validator($content, [
            'signature.targetHash' => ['required', 'string'],
            'data' => ['required', 'array']
        ]);

        if ($fileContentValidator->fails()) {
            return $this->invalidSignatureResult();
        }

        $generatedHash = $this->generateHashForData($content['data']);

        if ($generatedHash !== $content['signature']['targetHash']) {
            return $this->invalidSignatureResult();
        }

        return $next($content);
    }

    private function invalidSignatureResult(): VerifyFileResult
    {
        return new VerifyFileResult(ResultStatus::InvalidSignature);
    }

    private function generateHashForData(array $data): string
    {
        $propertyHashes = $this->generatePropertyHashes($data);
        sort($propertyHashes);

        return hash(self::HASH_ALGORITHM, json_encode($propertyHashes));
    }

    private function generatePropertyHashes(array $array)
    {
        $dotArray = Arr::dot($array);
        $hashes = [];

        foreach ($dotArray as $dottedKey => $value) {
            $hashes[] = hash(self::HASH_ALGORITHM, json_encode([$dottedKey => $value]));
        }

        return $hashes;
    }
}
