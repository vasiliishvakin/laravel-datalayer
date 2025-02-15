<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Vaskiq\LaravelDataLayer\Repositories\EloquentModelRepository;
use PHPUnit\Framework\TestCase;

class TestEloquentModelRepository extends EloquentModelRepository
{
    // Concrete implementation for testing purposes
}

it('can find a model by ID', function () {
    $model = Mockery::mock(Model::class);
    $repository = new TestEloquentModelRepository($model);

    $model->shouldReceive('find')->with(1)->andReturn($model);

    $result = $repository->findModel(1);

    expect($result)->toBe($model);
});

it('can find a model by ID or fail', function () {
    $model = Mockery::mock(Model::class);
    $repository = new TestEloquentModelRepository($model);

    $model->shouldReceive('findOrFail')->with(1)->andReturn($model);

    $result = $repository->findModelOrFail(1);

    expect($result)->toBe($model);
});

it('can return all models', function () {
    $model = Mockery::mock(Model::class);
    $repository = new TestEloquentModelRepository($model);

    $collection = Mockery::mock(Collection::class);
    $model->shouldReceive('all')->andReturn($collection);

    $result = $repository->allModels();

    expect($result)->toBe($collection);
});

it('can create a model', function () {
    $model = Mockery::mock(Model::class);
    $repository = new TestEloquentModelRepository($model);

    $data = ['name' => 'Test'];
    $model->shouldReceive('create')->with($data)->andReturn($model);

    $result = $repository->createModel($data);

    expect($result)->toBe($model);
});

it('can update a model', function () {
    $model = Mockery::mock(Model::class);
    $repository = new TestEloquentModelRepository($model);

    $data = ['name' => 'Updated'];
    $model->shouldReceive('find')->with(1)->andReturn($model);
    $model->shouldReceive('update')->with($data)->andReturn(true);

    $result = $repository->updateModel(1, $data);

    expect($result)->toBe($model);
});

it('can delete a model', function () {
    $model = Mockery::mock(Model::class);
    $repository = new TestEloquentModelRepository($model);

    $model->shouldReceive('find')->with(1)->andReturn($model);
    $model->shouldReceive('delete')->andReturn(true);

    $result = $repository->deleteModel(1);

    expect($result)->toBe(true);
});

it('can return the model class name', function () {
    $model = Mockery::mock(Model::class);
    $repository = new TestEloquentModelRepository($model);

    $result = $repository->modelClass();

    expect($result)->toBe(get_class($model));
});

it('can update or create a model', function () {
    $model = Mockery::mock(Model::class);
    $repository = new TestEloquentModelRepository($model);

    $attributes = ['name' => 'Test'];
    $values = ['name' => 'Updated'];
    $model->shouldReceive('updateOrCreate')->with($attributes, $values)->andReturn($model);

    $result = $repository->updateOrCreateModel($attributes, $values);

    expect($result)->toBe($model);
});
