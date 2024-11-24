<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Fixtures;

use BaseCodeOy\Relatable\Models\Concerns\HasRelatedContent;
use Illuminate\Database\Eloquent\Model;

final class HasFruitAsRelatedContent extends Model
{
    use HasRelatedContent;

    protected $guarded = [];
}
