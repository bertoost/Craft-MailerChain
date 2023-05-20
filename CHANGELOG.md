# Craft CMS - Mailer chain

## Unreleased

### Changed

- Moved logic of changing transport to deeper level at the chain transporter

## v1.1.0 - 2023-03-23

### Added

- Dummy transport instead of directly return a configured chain transport 

### Changed

- Change transport on `Mailer::BEFORE_PREP` instead to support Campaign plugin

## v1.0.0 - 2023-02-23

### Changed

- Changed random picking configured mailer chain element by ordered by sent, ranking and randomness

## v1.0.0-beta4 - 2023-02-20

### Changed

- Improved checking using this mailer chain adapter is configured in the Craft mail settings

## v1.0.0-beta3 - 2023-02-19

### Changed

- Added requirement to test adapters in the chain before using
- Added message to the adapter settings view when there is no or not successful tested adapter in the chain
- Removed custom CP section and added redirect for plugin settings
- Fixed saving transport settings in JSON format for non-json supporting databases

## v1.0.0-beta2 - 2023-02-18

### Changed

- Fixed storing transport class based on native transport determination
- Fixed getting mailer settings at new creation of a chain record

## v1.0.0-beta1 - 2023-02-13

### Added

- Initial release of the Mailer Chain Craft CMS plugin