<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * The column never held a path.
 *
 * `avatar_path` is written from `$socialUser->getAvatar()`, which every provider returns as a remote
 * URL. Nothing ever downloaded or stored a file, so the name described an intention that was not
 * implemented — and a reader taking it at face value would render it as a local asset, or write a
 * cleanup job for files that do not exist.
 *
 * The value is unchanged; only the name stops lying about it.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('socials', 'avatar_path') && ! Schema::hasColumn('socials', 'avatar_url')) {
            Schema::table('socials', function (Blueprint $table): void {
                $table->renameColumn('avatar_path', 'avatar_url');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('socials', 'avatar_url') && ! Schema::hasColumn('socials', 'avatar_path')) {
            Schema::table('socials', function (Blueprint $table): void {
                $table->renameColumn('avatar_url', 'avatar_path');
            });
        }
    }
};
