# Changelog

## v0.6 - 2025-07-26

- Remove ray from composer.json
- Added Laravel 11 compatibility and updated README

## v0.4 - 2025-06-09

- Fixed `unsubscribe` controller logic by adding missing `Controller` import
- Fixed documentation about which Laravel model to declare when using model specific notifications
- Added documentation about what basic functionality the controller provides
- Reorganised the readme a bit so that unsubscribe is nearer the end
- Added more documentation for protected and public variables in the base class

## v0.3 - 2025-05-31

### Changed
- Added `composer test`
 
### Fixed
- Removed redundant `subscribed` database field
- Repeat frequency is now an enum
- Dropped `strtolower` in frequency checking and migrated from `switch` to `match`

### Added
- Changelog
- First major release with all tests passing.

## v0.1 - 2025-05-30

### Added
- first alpha
