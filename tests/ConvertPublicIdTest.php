<?php

use Illuminate\Database\Eloquent\Relations\Relation;
use YieldStudio\EloquentPublicId\NotFoundModel;
use YieldStudio\EloquentPublicId\Test\Models\Category;
use YieldStudio\EloquentPublicId\Test\Models\Media;
use YieldStudio\EloquentPublicId\Test\Models\Post;
use YieldStudio\EloquentPublicId\Test\Models\Tag;
use YieldStudio\EloquentPublicId\Test\Models\User;
use YieldStudio\EloquentPublicId\ConvertPublicId;

class RequestTest
{
    use ConvertPublicId;

    protected array $publicIdsToConvert = [
        'category_id' => Category::class,
        'tags.*' => Tag::class,
        'postable_id' => 'postable_type',
        'suggestions' => [
            '*' => [
                'post_id' => Post::class,
                'tags.*' => Tag::class,
                'postable_id' => 'postable_type',
            ]
        ],
        'media_id' => Media::class,
        'checked_id' => 'App\\Models\\Checker' // Not existing model
    ];

    /*
     * Fake Request methods
     */

    public function __construct(public array $data){}

    public function all()
    {
        return $this->data;
    }

    public function merge(array $data)
    {
        $this->data = array_merge($this->data, $data);
    }
}

test('convert public ids to ids', function () {
    $category = Category::create();
    $tag1 = Tag::create();
    $tag2 = Tag::create();
    $tag3 = Tag::create();
    $user = User::create();

    $result = new RequestTest([
        'category_id' => $category->getPublicId(),
        'tags' => [
            $tag2->getPublicId(),
            $tag1->getPublicId(),
            $tag3->getPublicId(),
            'missing'
        ],
        'postable_type' => User::class,
        'postable_id' => $user->public_id
    ]);

    $result->prepareForValidation();
    expect($result->all())
        ->category_id->toBe(1)
        ->tags->toBe([2, 1, 3, 'missing'])
        ->postable_id->toBe(1);
});

test('convert public ids to ids with morph map', function () {
    $user = User::create();

    Relation::enforceMorphMap([
        'an_user' => User::class
    ]);

    $result = new RequestTest([
        'postable_type' => 'an_user',
        'postable_id' => $user->public_id
    ]);

    $result->prepareForValidation();
    expect($result->all())
        ->postable_id->toBe(1);
});

test('convert public ids to ids with a non conventional model', function () {
    Media::create();
    $media2 = Media::create();

    $result = new RequestTest([
        'media_id' => $media2->uuid,
    ]);

    $result->prepareForValidation();
    expect($result->all())
        ->media_id->toBe(2);
});

test('convert public ids with nesting', function () {
    $category = Category::create();
    $tag1 = Tag::create();
    $tag2 = Tag::create();
    $tag3 = Tag::create();
    $user = User::create();

    $result = new RequestTest([
        'category_id' => $category->getPublicId(),
        'tags' => [
            $tag2->getPublicId(),
            $tag1->getPublicId(),
            $tag3->getPublicId(),
            'missing'
        ],
        'postable_type' => User::class,
        'postable_id' => $user->public_id,

        'suggestions' => [
            [
                'tags' => [
                    $tag2->getPublicId(),
                    $tag1->getPublicId(),
                ],
                'postable_type' => User::class,
                'postable_id' => $user->public_id
            ],
            [
                'tags' => [
                    $tag2->getPublicId(),
                    'missing'
                ],
                'postable_type' => null,
                'postable_id' => ''
            ]
        ]
    ]);

    $result->prepareForValidation();
    expect($result->all())
        ->category_id->toBe(1)
        ->tags->toBe([2, 1, 3, 'missing'])
        ->postable_id->toBe(1)
        ->suggestions->toBe([
           [
               'tags' => [2, 1],
               'postable_type' => User::class,
               'postable_id' => 1
           ],
           [
               'tags' => [2, 'missing'],
               'postable_type' => null,
               'postable_id' => ''
           ]
        ]);
});

test('convert public ids to ids with not found morph throws an NotFoundModel exception', function ($type) {
    $result = new RequestTest([
        'postable_type' => $type,
        'postable_id' => 'an-uuid'
    ]);

    $result->prepareForValidation();
})->with(['not-found-model', 'App\\Models\\DummyModel'])->throws(NotFoundModel::class);


test('convert public ids to ids with not found model throws an NotFoundModel exception', function () {
    $result = new RequestTest([
        'checked_id' => 'an-uuid',
    ]);

    $result->prepareForValidation();
})->throws(NotFoundModel::class);
