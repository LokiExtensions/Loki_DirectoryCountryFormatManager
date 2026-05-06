# Loki_DirectoryCountryFormatManager

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
