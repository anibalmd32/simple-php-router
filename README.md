# Simple PHP Router

A lightweight, robust, and attribute-based routing library for PHP 8.2+ web applications.

Designed to provide a modern developer experience with minimal overhead, **Simple PHP Router** leverages PHP 8 attributes to define routes directly within your controllers, promoting a clean and organized codebase.

## Table of Contents

- [Installation](#installation)
- [Project Structure](#project-structure)
- [Quick Start](#quick-start)
- [Usage Guide](#usage-guide)
  - [Routing](#routing)
  - [Controllers](#controllers)
  - [The Request Object](#the-request-object)
  - [The Response Object](#the-response-object)
  - [Views & Templates](#views--templates)
- [Advanced Example: User CRUD](#advanced-example-user-crud)
- [License](#license)

## Installation

Install the library via Composer:

```bash
composer require anibalmd32/simple-php-router
```

## Project Structure

To maintain a scalable and maintainable application, we strongly recommend implementing a clean architecture. Separete your concerns by keeping controllers, models, and views in dedicated directories.

A recommended directory structure looks like this:

```text
my-app/
├── index.php       # Entry point
├── src/
│   ├── Controllers/    # Route handlers
│   ├── Models/         # Business logic & Database interaction
│   └── views/          # HTML templates
├── vendor/
├── composer.json
```

By following this convention, your codebase remains navigable and easy to test as your project grows.

## Quick Start

### 1. Entry Point (`index.php`)

Initialize the router and load your controllers in your main entry file.

```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SimplePhpRouter\Router;
use App\Controllers\HomeController;

// Initialize Router
$router = new Router();

// Register Controllers
$router->loadControllers([
    HomeController::class,
]);

// Dispatch Request
$router->dispatch();
```

### 2. Create a Controller (`src/Controllers/HomeController.php`)

Use PHP attributes to define routes.

```php
<?php

namespace App\Controllers;

use SimplePhpRouter\Attributes\Get;
use SimplePhpRouter\Request;
use SimplePhpRouter\Response;

class HomeController
{
    #[Get('/')]
    public function index(Request $req, Response $res)
    {
        return $res->json(['message' => 'Welcome to Simple PHP Router!']);
    }
}
```

## Usage Guide

### Routing

Simple PHP Router uses PHP Attributes to register routes. Available attributes correspond to standard HTTP methods:

- `#[Get('/path')]`
- `#[Post('/path')]`
- `#[Put('/path')]`
- `#[Delete('/path')]`
- `#[Patch('/path')]`
- `#[Options('/path')]`
- `#[Head('/path')]`

### Controllers

Controllers are simple PHP classes. Public methods decorated with Route attributes become route handlers. Each handler receives a `Request` and `Response` object.

**Parametrized Routes:**
You can define dynamic route parameters using the `:paramName` syntax.

```php
#[Get('/users/:id')]
public function show(Request $req, Response $res) 
{
    $userId = $req->params['id'];
    // ... logic to find user
}
```

### The Request Object

The `SimplePhpRouter\Request` object provides easy access to client input.

- **`$req->body`**:  Parsed JSON body (automatically decoded).
- **`$req->params`**: Route parameters (e.g., `:id`).
- **`$req->queries`**: URL query parameters (e.g., `?search=term`).
- **`$req->files`**: Uploaded files (`$_FILES`).

**Example:**

```php
#[Post('/users')]
public function store(Request $req, Response $res)
{
    $data = $req->body; // ['name' => 'John', 'email' => '...']
    // ...
}
```

### The Response Object

The `SimplePhpRouter\Response` object offers a fluent API for sending responses.

- **`status(int $code)`**: Set HTTP status code.
- **`json(array $data)`**: Send a JSON response with correct headers.
- **`render(string $template, array $context)`**: Render a PHP template.
- **`redirect(string $url)`**: Redirect to another URL.
- **`setHeaders(array $headers)`**: Set multiple custom headers.

### Views & Templates

Render views using the `render` method. It is best practice to keep your templates in a separate `views` or `templates` directory.

```php
// In Controller
#[Get('/profile')]
public function profile(Request $req, Response $res)
{
    // Renders src/views/profile.php passing variables
    return $res->render(__DIR__ . '/../views/profile.php', [
        'user' => 'Anibal'
    ]);
}
```

## Advanced Example: User CRUD

Here is a real-world example of a RESTful User Controller handling Create, Read, Update, and Delete operations.

**`src/Controllers/UserController.php`**

```php
<?php

namespace App\Controllers;

use SimplePhpRouter\Attributes\Get;
use SimplePhpRouter\Attributes\Post;
use SimplePhpRouter\Attributes\Put;
use SimplePhpRouter\Attributes\Delete;
use SimplePhpRouter\Request;
use SimplePhpRouter\Response;

class UserController
{
    // GET /users - List all users
    #[Get('/users')]
    public function index(Request $req, Response $res)
    {
        // Simulate database fetch
        $users = [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
        ];
        
        return $res->status(200)->json($users);
    }

    // GET /users/:id - Get a single user
    #[Get('/users/:id')]
    public function show(Request $req, Response $res)
    {
        $id = $req->params['id'];
        
        // Simulate finding user
        if ($id == '999') {
            return $res->status(404)->json(['error' => 'User not found']);
        }

        return $res->json([
            'id' => $id, 
            'name' => 'Simulated User'
        ]);
    }

    // POST /users - Create a new user
    #[Post('/users')]
    public function store(Request $req, Response $res)
    {
        $userData = $req->body;

        // Basic validation
        if (!isset($userData['name']) || !isset($userData['email'])) {
            return $res->status(400)->json(['error' => 'Missing required fields']);
        }

        // Simulate creation logic...

        return $res->status(201)->json([
            'message' => 'User created successfully', 
            'data' => $userData
        ]);
    }

    // PUT /users/:id - Update user
    #[Put('/users/:id')]
    public function update(Request $req, Response $res)
    {
        $id = $req->params['id'];
        $updates = $req->body;

        return $res->json([
            'message' => "User {$id} updated",
            'updates' => $updates
        ]);
    }

    // DELETE /users/:id - Delete user
    #[Delete('/users/:id')]
    public function destroy(Request $req, Response $res)
    {
        $id = $req->params['id'];
        
        // Simulate delete...

        return $res->status(200)->json(['message' => "User {$id} deleted"]);
    }
}
```

## License

This project is licensed under the MIT License.
