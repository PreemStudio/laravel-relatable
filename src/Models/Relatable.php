<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Relatable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Config;

/**
 * @property int    $id
 * @property int    $related_id
 * @property string $related_type
 * @property int    $source_id
 * @property string $source_type
 */
final class Relatable extends Model
{
    protected $guarded = [];

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTable(): string
    {
        return Config::get('relatable.table', 'relatables');
    }

    public function getRelatedValues(): array
    {
        return [
            'type' => $this->related_type,
            'id' => $this->related_id,
        ];
    }
}
