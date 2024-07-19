<?php

declare(strict_types=1);

namespace YieldStudio\EloquentPublicId\Tests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->index()->unique();
            $table->morphs('postable');
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->index()->unique();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->index()->unique();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->index()->unique();
        });

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->uuid()->index()->unique();
        });
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
