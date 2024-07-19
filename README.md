# Eloquent Public Id

[![Latest Version](https://img.shields.io/github/release/yieldstudio/eloquent-public-id?style=flat-square)](https://github.com/yieldstudio/eloquent-public-id/releases)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/yieldstudio/eloquent-public-id/tests.yml?branch=main&style=flat-square)](https://github.com/yieldstudio/eloquent-public-id/actions/workflows/tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/yieldstudio/eloquent-public-id?style=flat-square)](https://packagist.org/packages/yieldstudio/eloquent-public-id)

## What It Does

The interest of public IDs is to keep a whole and incremental ID, while having a UUID to expose to the front end, which can be convenient for security reasons.

This package offers two features:

- Allow models to manage a public ID
- Allow FormRequest to convert public IDs to IDs

## Installation

You can install the package via composer:

```bash
composer require yieldstudio/eloquent-public-id
```

## HasPublicId Trait

This Trait will enable your Model to have benefit of all the actions needed to process the public id.

Once package installed, Add a public id field into your table

```php
Schema::create('users', function (Blueprint $table) {
    // ..
    $table->uuid('public_id')->index()->unique();
    // ..
});
```

Next step, use the HasPublicId trait into your Model

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use YieldStudio\EloquentPublicId\HasPublicId;

class User extends Model
{
    use HasPublicId;
}
```

It's ready to work :)

> ⚠️ By default the trait will mark the ID field as a hidden field and guard the public ID.

The Trait adds some methods to your Model, here they are:

| Name                                                     | Description                                           |
|----------------------------------------------------------|-------------------------------------------------------|
| wherePublicId(string $publicId)                          | A new scope to find with a public ID                  |
| findByPublicId(string $publicId, array $columns = ['*']) | A new static method to get a model by their public ID |
| getPublicIdName()                                        | Returns the public ID column name                     |
| getPublicId()                                            | Returns the public ID of the model                    |

### Change the name of the public ID column

If in your migration you have chosen another field name instead of public_id, you have to specify this field using the `getPublicIdName` function.

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use YieldStudio\EloquentPublicId\HasPublicId;

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

The public id is automatically generated once your Model is created in the database.
If you want to modify the value of the generation of this field, you must add the `generatePublicId` function to your Model

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use YieldStudio\EloquentPublicId\HasPublicId;

class User extends Model
{
    use HasPublicId;
    
    public function generatePublicId(): string
    {
        return Str::random();
    }
}
```

## ConvertPublicId Trait

Allowing to convert public IDs to IDs in a FormRequest (before validation).

```php
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use YieldStudio\EloquentPublicId\ConvertPublicId;

class CreatePostRequest extends FormRequest
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

## Contact us

[<img src="https://github.com/user-attachments/assets/da9a38b2-fb3c-4581-957a-d3b520e32128" width="419px" />](https://www.yieldstudio.fr/contact)

Our team at Yield Studio is ready to welcome you and make every interaction an exceptional experience. You can [contact us](https://www.yieldstudio.fr/contact).

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

