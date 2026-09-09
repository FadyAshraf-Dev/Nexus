# fadyashraf-dev/nexus-support

Framework-agnostic PHP utilities extracted from the Nexus marketplace platform. PSR-4 autoloaded, zero app-level dependencies - no `ROOT_PATH` constant, no bare `$_SESSION` reads, no static globals reached into internally.

## Install

```bash
composer require fadyashraf-dev/nexus-support
```

## Classes

**Pure / stateless (no constructor dependencies):**
- **Html** — `escape()`, `slug()`.
- **Price** — `calculatePrice()`, `oldPrice()`.
- **Role** — `CUSTOMER` / `VENDOR` / `ADMIN` constants.
- **Cookie** — `set()`, `get()`, `exists()`, `delete()` wrapping `setcookie()`/`$_COOKIE`.
- **Navigation** — `currentPage()`, `activeIf()` wrapping `$_SERVER['PHP_SELF']`.
- **Session** — session lifecycle (`start`, `login`, `logout`, `check`, `regenerate`, etc). Manages PHP's session state directly; has no app-config dependency, so it needed no constructor changes.
- **Repository** — abstract base class wrapping a `PDO` connection (prepare/query/transactions). Extend it and inject your own `PDO`.

**Constructor-injected (previously read app config or a static global):**
- **Config** — takes `app` and `database` config arrays via the constructor instead of `require`-ing files off a `ROOT_PATH` constant.
- **Database** — takes a `Config` instance via the constructor instead of a static singleton `PDO`.
- **Validator** — takes a `PDO` instance via the constructor instead of calling `Database::connection()` internally. All validation rules (required, email, min/max, regex, exists, same, etc.) are unchanged.
- **Response** — takes admin/base URL prefixes and an optional 403 template path via the constructor instead of `Config::app(...)`.
- **Asset** — takes admin/base URL prefixes and a public directory path via the constructor instead of `Config::app(...)`.
- **Mailer** — takes from-address/from-name/base-url via the constructor instead of `Config::app(...)`. Still wraps PHP's built-in `mail()` (no SMTP library).

**Session-backed (previously touched `$_SESSION` directly):**
- **SessionStore** — interface with `get()`/`set()`/`has()`/`remove()`.
- **ArraySessionStore** — default `$_SESSION`-backed implementation.
- **Errors**, **Old**, **Flash** — each takes a `SessionStore` via the constructor instead of reading/writing `$_SESSION[...]` directly, so they work against any session backend (or an in-memory one for tests).

## Usage

```php
use FadyAshrafDev\NexusSupport\Config;
use FadyAshrafDev\NexusSupport\Database;
use FadyAshrafDev\NexusSupport\Validator;
use FadyAshrafDev\NexusSupport\Errors;
use FadyAshrafDev\NexusSupport\ArraySessionStore;

$config = new Config(
    appConfig: ['urls' => ['base_url' => '/', 'admin_url' => '/admin'], 'mail' => ['from_address' => 'noreply@example.com', 'from_name' => 'Example']],
    databaseConfig: ['driver' => 'mysql', 'host' => 'localhost', 'dbname' => 'myapp', 'charset' => 'utf8mb4', 'username' => 'root', 'password' => '']
);

$pdo = (new Database($config))->connection();

$validator = (new Validator($_POST, $pdo))->validate([
    'email' => 'required|email',
    'age' => 'nullable|integer|min:18',
]);

if ($validator->fails()) {
    (new Errors(new ArraySessionStore()))->set($validator->errors());
}
```

```php
use FadyAshrafDev\NexusSupport\Repository;

class ProductRepository extends Repository
{
    public function find(int $id): array
    {
        $stmt = $this->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: [];
    }
}
```

## How this maps onto the Nexus app

Every class in `app/Core` and `app/Helpers` that used to live only in the Nexus app now has a portable, dependency-free counterpart in this package. The original app-level classes still exist with their original names and static APIs (`Config::app(...)`, `Database::connection()`, `new Validator($input)`, `Role::CUSTOMER`, etc.) - **every existing call site across the app is unchanged** - but they now delegate to (or, for `Repository`/`Role`/`Validator` which are extended/instantiated directly rather than called statically, alias/wrap) the classes in this package:

| App-level class | Kind of wrapper | Package class |
|---|---|---|
| `app/Helpers/Config.php` | static facade | `Config` |
| `app/Core/Database.php` | static facade | `Database` |
| `app/Core/Validator.php` | subclass, defaults `$pdo` to `Database::connection()` | `Validator` |
| `app/Core/Repository.php` | abstract alias (12 repositories `extends` it) | `Repository` |
| `app/Core/Role.php` | constant alias (43 call sites) | `Role` |
| `app/Core/Response.php` | static facade | `Response` |
| `app/Helpers/Asset.php` | static facade | `Asset` |
| `app/Helpers/Mailer.php` | static facade | `Mailer` |
| `app/Core/Cookie.php` | static facade | `Cookie` |
| `app/Helpers/Navigation.php` | static facade | `Navigation` |
| `app/Core/Session.php` | static facade | `Session` |
| `app/Helpers/Errors.php` | static facade (backed by `ArraySessionStore`) | `Errors` |
| `app/Helpers/Old.php` | static facade (backed by `ArraySessionStore`) | `Old` |
| `app/Helpers/Flash.php` | static facade (backed by `ArraySessionStore`) | `Flash` |
| `app/Helpers/Html.php` | static facade | `Html` |
| `app/Helpers/Price.php` | static facade | `Price` |

## Releasing

Bump the version in a git tag matching `support-vX.Y.Z` (e.g. `support-v1.0.0`) and push it. The `composer-publish.yml` GitHub Actions workflow notifies Packagist to re-sync the package (Packagist tracks versions via git tags, not a build step).
