# PHP Enum Utils

[![Tests](https://github.com/philiprehberger/php-enum-utils/actions/workflows/tests.yml/badge.svg)](https://github.com/philiprehberger/php-enum-utils/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/philiprehberger/php-enum-utils.svg)](https://packagist.org/packages/philiprehberger/php-enum-utils)
[![License](https://img.shields.io/github/license/philiprehberger/php-enum-utils)](LICENSE)

Utility trait and helpers for PHP 8.1+ native enums.


## Requirements

| Dependency | Version |
|------------|---------|
| PHP        | ^8.2    |


## Installation

```bash
composer require philiprehberger/php-enum-utils
```


## Usage

### Adding the trait to your enum

```php
use PhilipRehberger\EnumUtils\EnumUtils;
use PhilipRehberger\EnumUtils\Attributes\Label;
use PhilipRehberger\EnumUtils\Attributes\Description;

enum Status: string
{
    use EnumUtils;

    #[Label('Pending Review')]
    #[Description('The item is waiting for review')]
    case Pending = 'pending';

    #[Label('In Progress')]
    case InProgress = 'in_progress';

    case Completed = 'completed';
}
```

### Lookup by name

```php
$case = Status::fromName('pending');      // Status::Pending (case-insensitive)
$case = Status::tryFromName('unknown');   // null
```

### Listing cases

```php
Status::names();   // ['Pending', 'InProgress', 'Completed']
Status::values();  // ['pending', 'in_progress', 'completed']
Status::count();   // 3
```

### Arrays for forms and selects

```php
Status::toSelectArray();
// ['pending' => 'Pending Review', 'in_progress' => 'In Progress', 'completed' => 'Completed']

Status::toArray();
// ['Pending' => 'pending', 'InProgress' => 'in_progress', 'Completed' => 'completed']
```

### Filtering cases

```php
// Filter by a custom predicate
$active = Status::casesWhere(fn (Status $s) => $s !== Status::Completed);
// [Status::Pending, Status::InProgress]

// Check if a case is among a given set
Status::Pending->in(Status::Pending, Status::InProgress);   // true
Status::Completed->in(Status::Pending, Status::InProgress); // false
```

### Random case and comparison

```php
$case = Status::random();                        // A random Status case
Status::Pending->equals(Status::Pending);        // true
Status::Pending->equals(Status::Completed);      // false
```

### Reading attributes with EnumMeta

```php
use PhilipRehberger\EnumUtils\EnumMeta;

EnumMeta::label(Status::Pending);         // 'Pending Review'
EnumMeta::label(Status::Completed);       // 'Completed' (fallback: humanized name)
EnumMeta::description(Status::Pending);   // 'The item is waiting for review'
EnumMeta::description(Status::Completed); // null
EnumMeta::labels(Status::class);          // ['pending' => 'Pending Review', ...]
```


## API

### EnumUtils Trait

| Method | Description |
|--------|-------------|
| `::fromName(string $name): static` | Case-insensitive lookup by name; throws `ValueError` on miss |
| `::tryFromName(string $name): ?static` | Case-insensitive lookup by name; returns `null` on miss |
| `::names(): array` | All case names as a flat array |
| `::values(): array` | All case values as a flat array |
| `::random(): static` | A random case |
| `::casesWhere(callable $filter): array` | Filter cases by a custom predicate |
| `::toSelectArray(): array` | `[value => label]` for form selects |
| `::toArray(): array` | `[name => value]` for serialization |
| `::count(): int` | Total number of cases |
| `->equals(self $other): bool` | Strict identity comparison |
| `->in(self ...$cases): bool` | Check if case is among the given set |

### EnumMeta Helper

| Method | Description |
|--------|-------------|
| `EnumMeta::label(BackedEnum $case): string` | Label from attribute or humanized name |
| `EnumMeta::description(BackedEnum $case): ?string` | Description from attribute or `null` |
| `EnumMeta::labels(string $enumClass): array` | `[value => label]` for all cases |

### Attributes

| Attribute | Target | Purpose |
|-----------|--------|---------|
| `#[Label('...')]` | Enum case | Human-readable label |
| `#[Description('...')]` | Enum case | Longer description text |


## Development

```bash
composer install
vendor/bin/phpunit
vendor/bin/pint --test
vendor/bin/phpstan analyse
```

## License

MIT
