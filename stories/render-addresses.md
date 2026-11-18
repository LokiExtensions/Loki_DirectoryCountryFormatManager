%title: Render addresses with the country format
%status: complete
---

**As a** shop owner,  
**I want to** have addresses rendered with the format of their country,  
**So that** customers, emails and PDFs show addresses in the local format.

## Acceptance Criteria

### when-a-country-format-matches

**Given** a country format exists for a country and a type  
**When** an address of that country is rendered with that type  
**Then** the country format is used.

Tests:
- `Test/Integration/Model/CountryFormatUsageTest::testCountryFormatIsUsedForMatchingCountryAndType`
- `Test/Integration/Model/CountryFormatUsageTest::testCountryFormatIsUsedForEveryType`
- `Test/Playwright/address-format-test.spec.ts: renders the default billing address with the managed country format`

### when-no-country-format-matches

**Given** no country format exists for the country or the type  
**When** an address is rendered  
**Then** the default address template is used.

Tests:
- `Test/Integration/Model/CountryFormatUsageTest::testCountryFormatIsNotUsedForOtherType`
- `Test/Integration/Model/CountryFormatUsageTest::testCountryFormatIsNotUsedForOtherCountry`
- `Test/Integration/Model/CountryFormatUsageTest::testDefaultFormatIsUsedWithoutCountryFormat`
