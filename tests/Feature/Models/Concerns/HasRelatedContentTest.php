<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Feature\Models\Concerns;

use Tests\Fixtures\HasFruitAsRelatedContent;
use Tests\Fixtures\Lime;
use Tests\Fixtures\Strawberry;

beforeEach(function (): void {
    $this->hasFruit = HasFruitAsRelatedContent::find(1);
});

it('can add a related model via a model instance', function (): void {
    $lime = Lime::find(1);

    expect($lime)->not()->toBeRelatedToSource($this->hasFruit);

    $this->hasFruit->relate($lime);

    expect($lime)->toBeRelatedToSource($this->hasFruit);
});

it('can add a related model via an id and a type', function (): void {
    $lime = Lime::find(1);

    expect($lime)->not()->toBeRelatedToSource($this->hasFruit);

    $this->hasFruit->relate(1, Lime::class);

    expect($lime)->toBeRelatedToSource($this->hasFruit);
});

it('cant add a related model via id if no type is provided', function (): void {
    $this->hasFruit->relate(1);
})->throws(\InvalidArgumentException::class);

it('can remove a related model via a model instance', function (): void {
    $lime = Lime::find(1);

    $this->hasFruit->relate($lime);

    expect($lime)->toBeRelatedToSource($this->hasFruit);

    $this->hasFruit->unrelate($lime);

    expect($lime)->not()->toBeRelatedToSource($this->hasFruit);
});

it('can remove a related model via an id and a type', function (): void {
    $lime = Lime::find(1);

    $this->hasFruit->relate(1, Lime::class);

    expect($lime)->toBeRelatedToSource($this->hasFruit);

    $this->hasFruit->unrelate(1, Lime::class);

    expect($lime)->not()->toBeRelatedToSource($this->hasFruit);
});

it('can retrieve a collection of its related content', function (): void {
    $lime = Lime::find(1);
    $strawberry = Strawberry::find(1);

    $this->hasFruit->relate($lime);
    $this->hasFruit->relate($strawberry);

    $related = $this->hasFruit->related;

    expect($related)->toHaveCount(2);
    expect($related)->toContainRelatable($lime);
    expect($related)->toContainRelatable($strawberry);
});

it('can determine if it has related content', function (): void {
    expect($this->hasFruit->hasRelated())->toBeFalse();
});

it('can determine if it doenst have related content', function (): void {
    $this->hasFruit->relate(Lime::find(1));

    expect($this->hasFruit->hasRelated())->toBeTrue();
});

it('can sync related content from a collection of models', function (): void {
    $lime = Lime::find(1);
    $strawberry = Strawberry::find(1);

    $this->hasFruit->relate($lime);

    $this->hasFruit->syncRelated(collect([$strawberry]));

    $related = $this->hasFruit->related;

    expect($related)->toHaveCount(1);
    expect($strawberry)->toBeRelatedToSource($this->hasFruit);
    expect($lime)->not()->toBeRelatedToSource($this->hasFruit);
});

it('can sync related content from an array of types and ids', function (): void {
    $lime = Lime::find(1);
    $strawberry = Strawberry::find(1);
    $this->hasFruit->relate($lime);

    $this->hasFruit->syncRelated([['id' => 1, 'type' => Strawberry::class]]);

    $related = $this->hasFruit->fresh()->related;

    expect($related)->toHaveCount(1);
    expect($strawberry)->toBeRelatedToSource($this->hasFruit);
    expect($lime)->not()->toBeRelatedToSource($this->hasFruit);
});

it('can sync related content without detaching', function (): void {
    $lime = Lime::find(1);
    $strawberry = Strawberry::find(1);

    $this->hasFruit->relate($lime);

    $this->hasFruit->syncRelated(collect([$strawberry]), false);

    $related = $this->hasFruit->fresh()->getRelatedAttribute();

    expect($related)->toHaveCount(2);
    expect($strawberry)->toBeRelatedToSource($this->hasFruit);
    expect($lime)->toBeRelatedToSource($this->hasFruit);
});
