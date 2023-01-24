# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
### Changed
### Deprecated
### Removed
### Fixed
### Security

## [v0.3.0] - 2023-01-24
### Fixed
- When an URI matches multiple API operations (example: `/api/path` and `/api/{pattern}`)
  the `ResponseValidator` was looping over each and tried to validate the `ResponseInterface`
  body with the definition. Now the `ResponseValidator` will only validates against
  a single matching operation. If no operation matches, an error will be thrown.
  That last point wasn't caught before.

## Changed
- Capture explicitely new exceptions from the league/openapi-psr7-validator
  library to ensure those errors will be useful for the end user (retrieving
  correct message, correct file…).

## [v0.2.0] - 2022-10-29
### Changed
- Add more open version constraints to let install Raven in more projects.
  As a library, being too strict with dependencies is always bad.

## [v0.1.0] - 2022-10-06
### Added
- Introduces a new object in the `CHStudio\Raven\Http\Factory` namespace named:
  `RequestUriParametersResolver`. It allows resolving URI parameters value using
  a `ValueResolverInterface` object.

## [v0.0.0] - 2022-10-04

> **Welcome Raven !**
>
> This is the first release for this library, now we are able to test OpenAPI docs !

[Unreleased]: https://github.com/chstudio/raven/compare/v0.3.0...HEAD
[v0.3.0]: https://github.com/chstudio/raven/releases/tag/v0.3.0
[v0.2.0]: https://github.com/chstudio/raven/releases/tag/v0.2.0
[v0.1.0]: https://github.com/chstudio/raven/releases/tag/v0.1.0
[v0.0.0]: https://github.com/chstudio/raven/releases/tag/v0.0.0
