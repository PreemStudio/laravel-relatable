<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use BombenProdukt\Relatable\Models\Concerns\HasRelatedContent;

final class HasFruitAsRelatedContent extends Model
{
    use HasRelatedContent;

    protected $guarded = [];
}
