# Loki_DirectoryCountryFormatManager

<!-- badges.specs.start -->
<div class="github-specs-badges">

![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://img.shields.io/packagist/v/loki/magento2-directory-country-format-manager)

</div>
<!-- badges.specs.end -->


**This Magento 2 module allows you to manage address templates per country. The core database table `directory_country_format` contains a mapping between a country and an address template, yet there is no way to manage this. This module uses `Loki_AdminComponents` to create a grid and form in the Magento Admin Panel, allowing you to manage things anyway.**

### Installation
Install this package via composer:
```bash
composer require loki/magento2-directory-country-format-manager
```

Next, enable this module:
```bash
bin/magento module:enable Loki_DirectoryCountryFormatManager
```

### Usage
Navigate to `Stores > Settings > Country Format Manager` within the Magento Admin Panel.

The grid lists all country formats with their country name and type. You can search through the country, type and format, and filter by country and type.

When adding or editing a country format, select a country and an address template type and enter the format. Each combination of country and type can only be used once. If you leave the format empty, the default address template of the selected type (`Stores > Configuration > Customers > Customer Configuration > Address Templates`) is used as a starting point.

The available types are read from the address formats of Magento (`etc/address_formats.xml`): by default `Text`, `Text One Line`, `HTML` and `PDF`. Address formats added by other modules are listed as well.

### Template syntax
A format uses the same syntax as the address templates in the Magento configuration, for instance:

```
{{depend company}}{{var company}}<br />{{/depend}}
{{var firstname}} {{var lastname}}<br />
{{if street1}}{{var street1}}<br />{{/if}}
{{var postcode}} {{var city}}<br />
{{var country}}
```

Common variables are `prefix`, `firstname`, `middlename`, `lastname`, `suffix`, `company`, `street1` to `street4`, `city`, `region`, `postcode`, `country`, `telephone`, `fax` and `vat_id`. Use `{{depend name}}...{{/depend}}` or `{{if name}}...{{/if}}` to only output a part when a variable is not empty.

### Permissions (ACL)
The ACL resources are found under `Stores > Settings > Country Format Manager`:

- `Loki_DirectoryCountryFormatManager::grid` (View Country Formats): Open and search the grid;
- `Loki_DirectoryCountryFormatManager::form` (Open Country Format Form): Open the form;
- `Loki_DirectoryCountryFormatManager::save` (Save Country Formats): Create and update country formats;
- `Loki_DirectoryCountryFormatManager::delete` (Delete Country Formats): Delete country formats;

Buttons that an admin user is not allowed to use are hidden.

## Current status

<!-- badges.test.start -->
<div class="github-pipeline-badges">

![Static Tests](https://img.shields.io/github/actions/workflow/status/LokiExtensions/Loki_DirectoryCountryFormatManager/static-tests.yml?label=static-tests)
![Unit Tests](https://img.shields.io/github/actions/workflow/status/LokiExtensions/Loki_DirectoryCountryFormatManager/unit-tests.yml?label=unit-tests)
![Integration Tests](https://img.shields.io/github/actions/workflow/status/LokiExtensions/Loki_DirectoryCountryFormatManager/integration-tests.yml?label=integration-tests)
![Playwright](https://img.shields.io/github/actions/workflow/status/LokiExtensions/Loki_DirectoryCountryFormatManager/playwright.yml?label=playwright)
![DI Compilation](https://img.shields.io/github/actions/workflow/status/LokiExtensions/Loki_DirectoryCountryFormatManager/compile.yml?label=compile)

</div>
<!-- badges.test.end -->
