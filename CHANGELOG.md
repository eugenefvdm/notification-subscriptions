# Changelog

## v0.9 - 2025-10-098

### Fixed

- Add a null check in `UnsubscribeController.php` if the subscription couldn't be found by UUID

## v0.8 - 2025-08-17

### Fixed

- Updated `unsubscribe.blade.php` with an additional null check

## v0.7 - 2025-07-31

### Added

- Throw an exception if the notification template cannot be found
- Added a conditional in the listener to do a check for the unsubscribed_at date
- Update README.md file and removed section about publishing resources
- Added information in the README.md file about the `unsubscribed` confirmation page
- Changed `redirect()->back()->with('success||error', 'Message') to an actual unsubscribed page to provide visualisation after unsubscribing
- Moved BaseNotification out of a "Notifications" directory further up to root to have a cleaner name space
- Renamed NotificationController to UnsubscribeController and made it an invokable controller
- Removed queueing on the abstract class BaseNotifications

## v0.6 - 2025-07-26

- Remove ray from composer.json
- Added Laravel 11 compatibility and updated `README.md` file

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
