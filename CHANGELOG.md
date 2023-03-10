# Changelog
All notable changes to this project will be documented in this file.

The format based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [0.4.1] - 2023-03-10
[0.4.1]: https://github.com/MagicLegacy/mtgmelee-client/compare/0.4.0...0.4.1
### Changed
- Fix pairing formatter with bye
- Fix client request (missing regex value)

## [0.4.0] - 2023-03-10
[0.4.0]: https://github.com/MagicLegacy/mtgmelee-client/compare/0.3.1...0.4.0
### Changed
- Support PHP 8.2
- Update player entity to have display name
- Improve makefile
- Update composer.json
- Update client to use native json function & exception
### Removed
- Remove Safe dependency
- Remove old php compatibility config files


## [0.3.1] - 2022-03-12
[0.3.1]: https://github.com/MagicLegacy/mtgmelee-client/compare/0.3.0...0.3.1
### Changed
- CI improvements (php compatibility check, makefile, github workflow)
- Now compatible with PHP 7.4, 8.0 & 8.1
- Fix phpdoc + some return type according to phpstan analysis
- Fix bug with Pairing client
### Added
- phpstan for static analysis
### Removed
- phpcompatibility (no more maintained)

## [0.3.0] - 2020-10-30
[0.3.0]: https://github.com/MagicLegacy/mtgmelee-client/compare/0.2.0...0.3.0
### Changed
- Upgrade dependency curl & require php 7.4


## [0.2.0] 2020-08-28 - Release v0.2.0
[0.2.0]: https://github.com/MagicLegacy/mtgmelee-client/compare/0.1.0...0.2.0
### Changed
- Rename `Client` to `MtgMeleeClient` and move it to root `src/`
- Rename composer name `magiclegacy/mtgmelee-importer` to `magiclegacy/mtgmelee-client`
- Update namespace of all class according to new naming
- Update example & README

## [0.1.0] 2020-08-25 - Release v0.1.0
### Added
- Add code to import and convert json tournament pairing data from MtgMelee into Entities
- Add tests
- Add doc
