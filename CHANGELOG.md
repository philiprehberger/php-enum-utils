# Changelog

All notable changes to `php-enum-utils` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.2] - 2026-03-23

### Changed
- Standardize README requirements format per template guide

## [1.1.1] - 2026-03-23

### Fixed
- Remove decorative dividers from README for template compliance

## [1.1.0] - 2026-03-22

### Added
- `casesWhere()` method for filtering enum cases by custom predicate
- `in()` method to check if a case is among a given set
- `toSelectArray()` method returning value-to-label mappings for dropdown rendering

## [1.0.3] - 2026-03-17

### Changed
- Standardized package metadata, README structure, and CI workflow per package guide

## [1.0.2] - 2026-03-16

### Changed
- Standardize composer.json: add type, homepage, scripts

## [1.0.1] - 2026-03-15

### Changed
- Standardize README badges

## [1.0.0] - 2026-03-15

### Added
- Initial release
- `EnumUtils` trait with `fromName`, `tryFromName`, `names`, `values`, `random`, `toSelectArray`, `toArray`, `count`, and `equals` methods
- `#[Label]` attribute for human-readable enum case labels
- `#[Description]` attribute for longer enum case descriptions
- `EnumMeta` helper class for reading attributes from enum cases
