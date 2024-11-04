<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use BaseCodeOy\Relatable\Models\Concerns\HasRelatedContent;
use Illuminate\Database\Eloquent\Model;

final class HasFruitAsRelatedContent extends Model
{
    use HasRelatedContent;

    protected $guarded = [];
}
