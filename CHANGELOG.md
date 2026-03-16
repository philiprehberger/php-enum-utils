# Changelog

All notable changes to `php-enum-utils` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
