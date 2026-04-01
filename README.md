# PHP Enum Utils

[![Tests](https://github.com/philiprehberger/php-enum-utils/actions/workflows/tests.yml/badge.svg)](https://github.com/philiprehberger/php-enum-utils/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/philiprehberger/php-enum-utils.svg)](https://packagist.org/packages/philiprehberger/php-enum-utils)
[![Last updated](https://img.shields.io/github/last-commit/philiprehberger/php-enum-utils)](https://github.com/philiprehberger/php-enum-utils/commits/main)

Utility trait and helpers for PHP 8.1+ native enums.

## Requirements

- PHP 8.2+

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

### Collections

```php
use PhilipRehberger\EnumUtils\EnumCollection;

// Wrap all cases in a fluent collection
$active = Status::collect()
    ->filter(fn (Status $s) => $s !== Status::Completed)
    ->sortBy(fn (Status $s) => $s->value)
    ->toArray();

// Get the first matching case
$first = Status::collect()->first(fn (Status $s) => str_starts_with($s->value, 'p'));

// Group cases
$grouped = Status::collect()->groupBy(fn (Status $s) => $s === Status::Completed ? 'done' : 'active');

// Partition into two arrays: [matching, non-matching]
[$pending, $rest] = Status::collect()->partition(fn (Status $s) => $s === Status::Pending);
```

### Serialization

```php
// Serialize all cases to JSON
$json = Status::toJson();
// [{"name":"Pending","value":"pending","label":"Pending Review","description":"..."},...]

// Deserialize back to enum cases
$cases = Status::fromJson($json);  // [Status::Pending, Status::InProgress, ...]

// Get value => label map
$map = Status::toMap();
// ['pending' => 'Pending Review', 'in_progress' => 'In Progress', 'completed' => 'Completed']
```

### State Transitions

```php
use PhilipRehberger\EnumUtils\Attributes\AllowedTransitions;

enum OrderStatus: string
{
    use EnumUtils;

    #[AllowedTransitions(self::Processing, self::Cancelled)]
    case Pending = 'pending';

    #[AllowedTransitions(self::Shipped, self::Cancelled)]
    case Processing = 'processing';

    #[AllowedTransitions(self::Delivered)]
    case Shipped = 'shipped';

    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
}

OrderStatus::Pending->canTransitionTo(OrderStatus::Processing);  // true
OrderStatus::Pending->canTransitionTo(OrderStatus::Shipped);     // false
OrderStatus::Pending->allowedTransitions();  // [OrderStatus::Processing, OrderStatus::Cancelled]
OrderStatus::Delivered->allowedTransitions(); // [] (no transitions defined)
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
| `::collect(): EnumCollection` | Wrap all cases in a fluent collection |
| `::toJson(): string` | Serialize all cases to JSON |
| `::fromJson(string $json): array` | Deserialize JSON back to enum cases |
| `::toMap(): array` | `[value => label]` map for all cases |
| `->canTransitionTo(self $target): bool` | Check if transition is allowed |
| `->allowedTransitions(): array` | Get all allowed target states |

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
| `#[AllowedTransitions(...)]` | Enum case | Define valid state transitions |

## Development

```bash
composer install
vendor/bin/phpunit
vendor/bin/pint --test
vendor/bin/phpstan analyse
```

## Support

If you find this project useful:

⭐ [Star the repo](https://github.com/philiprehberger/php-enum-utils)

🐛 [Report issues](https://github.com/philiprehberger/php-enum-utils/issues?q=is%3Aissue+is%3Aopen+label%3Abug)

💡 [Suggest features](https://github.com/philiprehberger/php-enum-utils/issues?q=is%3Aissue+is%3Aopen+label%3Aenhancement)

❤️ [Sponsor development](https://github.com/sponsors/philiprehberger)

🌐 [All Open Source Projects](https://philiprehberger.com/open-source-packages)

💻 [GitHub Profile](https://github.com/philiprehberger)

🔗 [LinkedIn Profile](https://www.linkedin.com/in/philiprehberger)

## License

[MIT](LICENSE)
