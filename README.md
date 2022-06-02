# eloquent-public-id

Eloquent Public ID Trait for Laravel 9 and above.

[![Latest Version](https://img.shields.io/github/release/yieldstudio/eloquent-public-id?style=flat-square)](https://github.com/yieldstudio/eloquent-public-id/releases)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/yieldstudio/eloquent-public-id/tests?style=flat-square)](https://github.com/yieldstudio/eloquent-public-id/actions/workflows/tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/yieldstudio/eloquent-public-id?style=flat-square)](https://packagist.org/packages/yieldstudio/eloquent-public-id)

This package offers two features:

- one for the models allowing to manage a public ID
- one for Form Request allowing to convert public IDs to IDs

The interest of public IDs is to keep a whole and incremental ID, while having a UUID to expose to the front end, which can be convenient for security reasons.

## Installation

	composer require yieldstudio/eloquent-public-id

## HasPublicId trait

#### Add a public id field into your table

```php
Schema::create('orders', function (Blueprint $table) {
    // ..
    $table->uuid('id')->index()->unique();
    // ..
});
```

#### Use the HasPublicId trait into your models

```php
<?php

use Illuminate\Database\Eloquent\Model;
use YieldStudio\EloquentPublicId\HasPublicId;

class User extends Model
{
    use HasPublicId;
}
```

It's ready to work :)

⚠️ By default the trait will mark the ID field as an hidden field and guard the public ID.

The trait adds some methods to your Model, here they are:

| Name                                                     | Description                                           |
|----------------------------------------------------------|-------------------------------------------------------|
| wherePublicId(string $publicId)                          | A new scope to find with a public ID                  |
| findByPublicId(string $publicId, array $columns = ['*']) | A new static method to get a model by their public ID |
| getPublicIdName()                                        | Returns the public ID column name                     |
| getPublicId()                                            | Returns the public ID of the model                    |

### Change the name of the public ID column

```php
<?php

class User extends Model
{
    use HasPublicId;
    
    public function getPublicIdName(): string
    {
        return 'uuid';
    }
}
```

### Change the generation of the public ID

```php
<?php

class User extends Model
{
    use HasPublicId;
    
    public function generatePublicId(): string
    {
        return Str::random();
    }
}
```

## ConvertPublicId trait

Allowing to convert public IDs to IDs in a Form Request (before validation).

```php
<?php

use Illuminate\Foundation\Http\FormRequest;
use YieldStudio\EloquentPublicId\ConvertPublicId;

class RequestTest extends FormRequest
{
    use ConvertPublicId;

    protected array $publicIdsToConvert = [
        'category_id' => Category::class,
        'tags.*' => Tag::class,
        'postable_id' => 'postable_type', // You can reference another field as model class in case of morph relationship
        'suggestions' => [ // Nesting fields is allowed
            '*' => [
                'post_id' => Post::class,
                'tags.*' => Tag::class,
                'postable_id' => 'postable_type',
            ]
        ]
    ];
```

## Unit tests

To run the tests, just run `composer install` and `composer test`.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you've found a bug regarding security please mail [contact@yieldstudio.fr](mailto:contact@yieldstudio.fr) instead of using the issue tracker.

## Credits

- [James Hemery](https://github.com/jameshemery)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

