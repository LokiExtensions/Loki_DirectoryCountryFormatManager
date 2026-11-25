# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 25 November 2026
### Added
- Separate admin permissions for viewing, opening, saving and deleting country formats, grouped under Stores > Settings
- Grid shows country names and address type labels, and offers search and filters
- Form validates country formats: country and type are required, the type must be a valid address template type and a country can only have one format per type
- Form prefills the default template of the selected address type
- Translations and usage documentation

### Changed
- Address template types are now read from the Magento address formats instead of a fixed list

### Fixed
- Save and delete now check admin permissions, so restricted admin roles can no longer change country formats
- Missing dependencies on Magento_Directory, Magento_Customer, Loki_Components and Loki_AdminComponents 0.9

## [1.0.2] - 30 October 2026
### Fixed
- Fix dependencies

## [1.0.1] - 07 August 2026
### Fixed
- Upgrade constraint with Loki AdminComponents

## [1.0.0] - 04 May 2026
- Initial release
