<?php

namespace App\Repositories;

use App\Models\Verification;
use App\Enums\FileVerification\FileType;
use App\Http\DTO\VerifyFileResult;

class VerificationRepository
{
    public function create(
        int $userId,
        VerifyFileResult $verifyFileResult,
    ): Verification {
        return Verification::create([
            'user_id' => $userId,
            'file_type' => FileType::Json,
            'result_status' => $verifyFileResult->getResultStatus(),
            'issuer_name' => $verifyFileResult->getIssuer(),
        ]);
    }
}
