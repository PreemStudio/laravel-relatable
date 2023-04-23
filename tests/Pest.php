<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use PreemStudio\Relatable\Models\Relatable;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature');

uses(
    Tests\TestCase::class,
)->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeRelatedToSource', function (Model $source): void {
    expect(
        (bool) Relatable::where([
            'source_id' => $source->getKey(),
            'source_type' => $source->getMorphClass(),
            'related_id' => $this->value->getKey(),
            'related_type' => $this->value->getMorphClass(),
        ])->first(),
    )->toBeTrue();
});

expect()->extend('toContainRelatable', function (Model $related): void {
    expect(
        $this->value->contains(fn (Model $item): bool => $item->id === $related->id && $item::class === $related->getMorphClass()),
    )->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something(): void
{
    // ..
}
