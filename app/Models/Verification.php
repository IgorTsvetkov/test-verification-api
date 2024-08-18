<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\FileVerification\ResultStatus;
use App\Enums\FileVerification\FileType;

class Verification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_type',
        'result_status',
        'issuer_name',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'file_type' => FileType::class,
        'result_status' => ResultStatus::class,
        'issuer_name' => 'string',
    ];
}
