<?php

namespace YieldStudio\EloquentPublicId\Test;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
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
            $table->uuid('uuid')->index()->unique();
        });
    }

    /**
     * Define environment setup.
     * @param Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
