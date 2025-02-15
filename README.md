# Laravel DataLayer
This package provides a repository implementation for Laravel applications.

## Installation

To install the package, run:

```bash
composer require vaskiq/laravel-datalayer
```

## Usage

### EloquentModelRepository

The `EloquentModelRepository` class offers various methods to interact with Eloquent models.

#### Methods

- `findModel(int|string $id): ?Model`
- `findModelOrFail(int|string $id): Model`
- `allModels(): Collection`
- `createModel(array $data): Model`
- `updateModel(int|string $id, array $data): ?Model`
- `deleteModel(int|string $id): bool`
- `query(): EloquentBuilder`
- `raw(): QueryBuilder`
- `modelClass(): string`
- `newModel(): Model`
- `updateOrCreateModel(array $attributes, array $values): Model`
- `deleteAllModelsBy(array|Closure|Expression $conditions): int`
- `findModelBy(array|Closure|Expression $conditions): ?Model`
- `findAllModelsBy(array|Closure|Expression $conditions): Collection`

#### Example

```php
use App\Models\User;
use Vaskiq\LaravelDataLayer\Repositories\EloquentModelRepository;

class UserRepository extends EloquentModelRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}

// Usage
$userRepository = new UserRepository(new User());

// Find a user by ID
$user = $userRepository->findModel(1);

// Create a new user
$newUser = $userRepository->createModel(['name' => 'John Doe', 'email' => 'john@example.com']);

// Update a user
$updatedUser = $userRepository->updateModel(1, ['name' => 'Jane Doe']);

// Delete a user
$userRepository->deleteModel(1);

// Get all users
$users = $userRepository->allModels();
