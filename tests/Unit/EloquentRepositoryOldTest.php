<?php

declare(strict_types=1);

use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Spatie\LaravelData\Data;
use Vaskiq\LaravelDataLayer\Factories\DataFactory;
use Vaskiq\LaravelDataLayer\Repositories\EloquentRepository;
use Vaskiq\LaravelDataLayer\Repositories\EloquentRepositoryOld;

// ✅ Globally mock config() for isolated testing
if (! function_exists('config')) {
    function config($key = null, $default = null)
    {
        return $default;
    }
}

// ✅ Concrete implementation for testing purposes
class TestEloquentRepositoryOld extends EloquentRepository
{
    public function dataClass(): string
    {
        return Data::class;
    }
}

beforeEach(function () {
    // ✅ Mock Laravel container
    $container = new Container;
    Facade::setFacadeApplication($container);

    // ✅ Bind config service
    $container->singleton('config', function () {
        return Mockery::mock(ConfigRepository::class)->shouldIgnoreMissing();
    });

    // ✅ Mock Eloquent Model and Builder
    $this->model = Mockery::mock(Model::class);
    $this->builder = Mockery::mock(Builder::class);
    $this->dataFactory = new DataFactory;

    // ✅ Mock common model methods
    $this->model->shouldReceive('newQuery')->andReturn($this->builder);
    $this->model->shouldReceive('find')->with(1)->andReturn($this->model);
    $this->model->shouldReceive('all')->andReturn(new Collection([$this->model]));
    $this->model->shouldReceive('whereKey')->andReturnSelf();
    $this->model->shouldReceive('delete')->andReturnTrue();
    $this->model->shouldReceive('save')->andReturnTrue();
    $this->model->shouldReceive('fill')->andReturnSelf();
    $this->model->shouldReceive('getKeyName')->andReturn('id');
    $this->model->shouldReceive('update')
        ->with(Mockery::type('array'))
        ->andReturnTrue();
    $this->model->shouldReceive('create')
        ->with(Mockery::type('array'))
        ->andReturn($this->model);

    // ✅ Mock builder methods
    $this->builder->shouldReceive('when')->andReturnSelf();
    $this->builder->shouldReceive('where')->andReturnSelf();
    $this->builder->shouldReceive('first')->andReturn($this->model);
    $this->builder->shouldReceive('get')->andReturn(new Collection([$this->model]));

    // ✅ Mock repository
    $this->repository = Mockery::mock(
        TestEloquentRepositoryOld::class,
        [$this->model, $this->dataFactory]
    )
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $this->repository
        ->shouldReceive('model')
        ->andReturn($this->model);
});

it('can find a model by ID', function () {
    $data = Mockery::mock(Data::class);

    $this->builder->shouldReceive('find')->with(1)->andReturn($this->model);
    $this->repository->shouldReceive('toData')->with($this->model)->andReturn($data);

    $result = $this->repository->find(1);

    expect($result)->toBe($data);
});

it('has an all method', function () {
    // Mock the Model and DataFactory
    $model = Mockery::mock(Model::class);
    $dataFactory = Mockery::mock(DataFactory::class);

    // Create an instance of the repository
    $repository = new TestEloquentRepositoryOld($model, $dataFactory);

    // Assert that the method exists
    expect(method_exists($repository, 'all'))->toBeTrue();
});

it('can find by field', function () {
    $data = Mockery::mock(Data::class);
    $collection = new Collection([$this->model]);

    $this->builder->shouldReceive('where')->andReturnSelf();
    $this->builder->shouldReceive('first')->andReturn($this->model);
    $this->builder->shouldReceive('get')->andReturn($collection);

    $this->repository->shouldReceive('toData')->andReturn($data);
    $this->repository->shouldReceive('toDataCollection')->andReturn($collection);

    $result = $this->repository->findBy('field', 'value', true);
    expect($result)->toBeInstanceOf(Data::class);

    $result = $this->repository->findBy('field', 'value', false);
    expect($result)->toBeInstanceOf(Collection::class);
});

it('can save a model', function () {
    $data = Mockery::mock(Data::class);

    $data->shouldReceive('toArray')->andReturn(['field' => 'value']);
    $this->model->shouldReceive('getKeyName')->andReturn('id');
    $this->model->shouldReceive('fill')->andReturnSelf();
    $this->model->shouldReceive('update')->with(['field' => 'value'])->andReturnTrue();
    $this->model->shouldReceive('save')->andReturnTrue();

    $this->repository->shouldReceive('toData')->andReturn($data);

    $result = $this->repository->save($data);

    expect($result)->toBe($data);
});

it('can update a model', function () {
    $data = Mockery::mock(Data::class);

    $this->builder->shouldReceive('find')->with(1)->andReturn($this->model);
    $this->model->shouldReceive('fill')->with(['field' => 'value'])->andReturnSelf();
    $this->model->shouldReceive('save')->andReturnTrue();

    $this->repository->shouldReceive('toData')->andReturn($data);

    $result = $this->repository->update(1, ['field' => 'value']);

    expect($result)->toBe($data);
});

it('can delete a model', function () {
    $this->builder->shouldReceive('find')->with(1)->andReturn($this->model);
    $this->model->shouldReceive('delete')->andReturnTrue();

    $result = $this->repository->delete(1);

    expect($result)->toBeTrue();
});

afterEach(function () {
    Mockery::close();
});
