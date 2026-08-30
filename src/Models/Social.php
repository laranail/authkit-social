<?php

declare(strict_types=1);

namespace Simtabi\Laranail\AuthKit\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Simtabi\Laranail\AuthKit\Social\Enums\SocialProvider;
use Simtabi\Laranail\AuthKit\Social\Database\Factories\SocialFactory;
use Simtabi\Laranail\AuthKit\Social\Casts\IdentityProviderCast;

class Social extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'socialable_type',
        'socialable_id',
        'provider',
        'provider_id',
        'name',
        'nickname',
        'email',
        'avatar_url',
        'token',
        'refresh_token',
        'expires_at',
    ];

    public static function newFactory(): SocialFactory
    {
        return new SocialFactory;
    }

    public function socialable(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'provider'      => IdentityProviderCast::class,
            'token'         => 'encrypted',
            'refresh_token' => 'encrypted',
            'expires_at'    => 'immutable_datetime',
        ];
    }
}
