<?php

namespace App\Http\DTO;

use App\Enums\FileVerification\ResultStatus;

class VerifyFileResult
{
    public function __construct(
        private ResultStatus $resultStatus,
        private ?string $issuer = null
    ) {}

    public function getResultStatus(): ResultStatus
    {
        return $this->resultStatus;
    }

    public function getIssuer(): ?string
    {
        return $this->issuer;
    }
}
