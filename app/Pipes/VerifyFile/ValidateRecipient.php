<?php

namespace App\Pipes\VerifyFile;

use Closure;
use App\Http\DTO\VerifyFileResult;
use App\Enums\FileVerification\ResultStatus;

class ValidateRecipient
{
    public function handle($content, Closure $next): VerifyFileResult
    {
        $validator = validator($content, [
            'data.recipient.name' => ['required', 'string'],
            'data.recipient.email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return new VerifyFileResult(ResultStatus::InvalidRecipient);
        }

        return $next($content);
    }
}
