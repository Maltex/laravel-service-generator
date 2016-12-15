# Laravel 5 Service Generator
A command line tool for generating service layer files for your application. Inspired by Jeffrey Way's L5 generator packages.

# Intro
The generator provided in this package is aimed to reduce time spent on boilerplate code when using a service layer within laravel. Although it allows for use without, the main aim is to be used in conjunction with a repository.

# Set Up
### 1. Install
Install the package using composer
```bash
composer require maltex/laravel-service-generators
```
### 2. Add Service Provider
Make the package available to your application by adding the service provider. This is not required for production code so register it by adding the following to your ```app/Providers/AppServiceProvider.php```:

```php
public function register()
{
    if ($this->app->environment() == 'local') {
        $this->app->register('Maltex\Generators\GeneratorsServiceProvider');
    }
}
```

# Examples
Let's say you're working with a *Client* model. You've created your front end screen, your routes and a controller end-point. Now we need to manage the business logic.

``` php artisan make:service AddClientService --repo=Client --func=addClient  ```

You will see that a Services folder is now available in your application with the *AddClientService.php* file.

```php

use Maltex\Generators\Contracts\ServiceContract;
use App\Repositories\CLientRepository;

class AddClientService implements ServiceContract
{
  /**
   * @var App\Repositories\ClientRepository
   */
   protected $repository;

   /**
    * Inject the repository
    *
    * @return void
    */
    public function __construct(App\Repositories\ClientRepository $repository)
    {
       $this->repository = repository;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        // TODO Add business logic

        $this->repository->addClient($data)
    }
}
```

This service should then be injected into your controller function.

```php
// ClientController.php
public function create(
    Request $request,
    AddClientService $service
)
{
    if($service->execute($request)) {
        // success response
    }
}
```

# To Do
- Add config details for repository dir and service dir
- Add generators for a repository service (tie into Dingo or Lara5 Repo)
- Offer the option to generate test file for the service