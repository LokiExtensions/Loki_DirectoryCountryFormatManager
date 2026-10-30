# Loki_DirectoryCountryFormatManager

<!-- badges.specs.start -->
<div class="gitlab-specs-badges">

![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://gitlab.yireo.com/loki-checkout/Loki_DirectoryCountryFormatManager/-/badges/release.svg)

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

## Current status

<!-- badges.test.start -->
<div class="gitlab-pipeline-badges">

[![GitLab CI Pipelines](https://gitlab.yireo.com/loki-checkout/Loki_DirectoryCountryFormatManager/badges/main/pipeline.svg?style=flat-square
)](https://gitlab.yireo.com/loki-checkout/Loki_DirectoryCountryFormatManager/-/pipelines)

</div>
<!-- badges.test.end -->
