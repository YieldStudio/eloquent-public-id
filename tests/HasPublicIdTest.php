<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use YieldStudio\EloquentPublicId\HasPublicId;
use YieldStudio\EloquentPublicId\Tests\Models\Category;
use YieldStudio\EloquentPublicId\Tests\Models\Media;

test('automatically assign a public id on creation', function () {
    $category = Category::create();
    $this->assertNotNull($category->public_id);
});

test('automatically assign a public id on creation with a custom public id name', function () {
    $category = Media::create();
    $this->assertNotNull($category->uuid);
});

test('classic id is hidden by default', function () {
    $category = Category::create()->toArray();
    $this->assertArrayNotHasKey('id', $category);
});

test('public id property is guarded by default', function () {
    $category = Category::create(['public_id' => 'an-uuid']);
    $this->assertNotEquals('an-uuid', $category->public_id);
});

test('public id property can be unguarded', function () {
    $category = Category::unguarded(fn () => Category::create(['public_id' => 'an-uuid']));
    $this->assertEquals('an-uuid', $category->public_id);
});

test('public id property is guarded by default with a custom public id name', function () {
    $media = Media::create(['uuid' => 'an-uuid']);
    $this->assertNotEquals('an-uuid', $media->uuid);
});

test('public id property can be unguarded with a custom public id name', function () {
    $media = Media::unguarded(fn () => Media::create(['uuid' => 'an-uuid']));
    $this->assertEquals('an-uuid', $media->uuid);
});

test('can customize the public id generate method', function () {
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->uuid('public_id')->index()->unique();
    });

    class Product extends Model
    {
        use HasPublicId;

        public $timestamps = false;

        public function generatePublicId(): string
        {
            return 'an-generated-uuid';
        }
    }

    $product = Product::create();
    $this->assertEquals('an-generated-uuid', $product->public_id);
});

test('public id getter method', function () {
    $media = Media::create();
    $this->assertEquals($media->uuid, $media->getPublicId());
});

test('public id name getter method', function () {
    $media = Media::create();
    $this->assertEquals('uuid', $media->getPublicIdName());
});

test('defines route key name', function () {
    $media = Media::create(['uuid' => 'an-uuid']);
    $this->assertEquals('uuid', $media->getRouteKeyName());
});

test('can retrieve model by public id', function () {
    Media::unguarded(fn () => Media::create(['uuid' => 'media-uuid']));
    $this->assertNotNull(Media::findByPublicId('media-uuid'));
    $this->assertNull(Media::findByPublicId('invalid-uuid'));

    Category::unguarded(fn () => Category::create(['public_id' => 'category-uuid']));
    $this->assertNotNull(Category::findByPublicId('category-uuid'));
    $this->assertNull(Category::findByPublicId('invalid-uuid'));
});

test('can use wherePublicId scope', function () {
    Media::unguarded(fn () => Media::create(['uuid' => 'media-uuid']));
    $this->assertNotNull(Media::query()->wherePublicId('media-uuid')->first());
    $this->assertNull(Media::query()->wherePublicId('invalid-uuid')->first());

    Category::unguarded(fn () => Category::create(['public_id' => 'category-uuid']));
    $this->assertNotNull(Category::query()->wherePublicId('category-uuid')->first());
    $this->assertNull(Category::query()->wherePublicId('invalid-uuid')->first());
});
