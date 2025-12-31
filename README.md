[![Latest Version on Packagist](https://img.shields.io/packagist/v/n3xt0r/laravel-passport-modern-scopes.svg?style=flat-square)](https://packagist.org/packages/n3xt0r/laravel-passport-modern-scopes)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/n3xt0r/laravel-passport-modern-scopes/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/n3xt0r/laravel-passport-modern-scopes/actions?query=workflow%3Atests+branch%3Amain)
[![Maintainability](https://qlty.sh/gh/N3XT0R/projects/laravel-passport-modern-scopes/maintainability.svg)](https://qlty.sh/gh/N3XT0R/projects/laravel-passport-modern-scopes)

---

# Passport Modern Scopes

---

## Attribute-based OAuth Scope Enforcement

Laravel Passport traditionally enforces OAuth scopes at the routing level, typically via middleware definitions in route
files. While functional, this approach tends to scatter authorization rules across routes and couples controllers to
infrastructure-level concerns.

This package introduces an **attribute-based approach** to OAuth scope enforcement.

By leveraging PHP 8 attributes and a single resolving middleware, required OAuth scopes can be declared **directly on
controllers or controller actions**, keeping authorization rules close to the code they protect while remaining fully
compatible with Laravel Passport.

### Key ideas

- OAuth scopes are **declared, not wired**
- Controllers express **requirements**, not middleware mechanics
- Passport remains untouched and fully in control of token validation
- Routes stay clean and infrastructure-agnostic

### Example

```php
use N3XT0R\PassportModernScopes\Support\Attributes\RequiresScope;
use N3XT0R\PassportModernScopes\Support\Attributes\RequiresAnyScope;

#[RequiresScope('users:read')]
final class UserController
{
    public function index()
    {
        // Requires users:read
    }

    #[RequiresAnyScope('users:update', 'users:write')]
    public function update()
    {
        // Requires at least one of the given scopes
    }
}
```

A single middleware inspects controller attributes at runtime and transparently applies Laravel Passport’s `CheckToken`
and `CheckTokenForAnyScope` middleware under the hood.

### Why attributes?

- Declarative and explicit
- No duplication between routes and controllers
- Easy to reason about during code review
- Static-analysis and documentation friendly
- No magic strings scattered across route definitions

This approach provides a clean separation between **authorization intent** and **HTTP wiring**, allowing Passport-based
APIs to scale without losing clarity or consistency.

## Installation

```bash
composer require n3xt0r/laravel-passport-modern-scopes
```

The middleware is automatically registered via the package's service provider.