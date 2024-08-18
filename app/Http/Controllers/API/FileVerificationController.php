<?php

namespace App\Http\Controllers\API;

use App\Http\Actions\VerifyFile;
use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyFileRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\VerifyFileResultResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response as HttpResponse;
use App\Repositories\VerificationRepository;


class FileVerificationController extends Controller
{
    public function __invoke(
        VerifyFileRequest $request,
        VerifyFile $verifyFile,
        VerificationRepository $verificationRepository
    ): JsonResource {
        try {
            $fileContent = $request->file('file')->get();

            $result = $verifyFile($fileContent);
            $verificationRepository->create(auth()->id(), $result);
        } catch (\Exception $e) {
            Log::error('Unexpected error occurred while verifying file', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            abort(HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new VerifyFileResultResource($result);
    }
}
