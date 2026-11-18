%title: Validate country formats
%status: complete
---

**As a** shop administrator,  
**I want to** be warned about invalid country formats,  
**So that** I do not store a format that is never used or that breaks the save.

## Acceptance Criteria

### when-country-and-type-already-exist

**Given** a country format exists for a country and a type  
**When** the admin saves another country format for the same country and type  
**Then** an error message is shown  
**And** the existing country format is kept.

Tests:
- `Test/Integration/Form/CountryFormatValidatorTest::testDuplicateCountryAndTypeIsRejected`
- `Test/Integration/Form/CountryFormatValidatorTest::testSameCountryFormatIsNotADuplicateOfItself`
- `Test/Integration/Form/CountryFormatValidatorTest::testOtherTypeOfSameCountryIsAccepted`
- `Test/Integration/Form/FormActionTest::testDuplicateCountryAndTypeIsRejected`
- `Test/Playwright/form-test.spec.ts: rejects a second country format for the same country and type`

### when-country-or-type-is-missing-or-invalid

**Given** the form is shown  
**When** the admin saves without a country, without a type or with an unknown type  
**Then** an error message is shown and nothing is stored.

Tests:
- `Test/Integration/Form/CountryFormatValidatorTest::testMissingCountryAndTypeAreRejected`
- `Test/Integration/Form/CountryFormatValidatorTest::testUnknownTypeIsRejected`
- `Test/Integration/Form/FormActionTest::testMissingCountryAndTypeAreRejected`
- `Test/Integration/Form/FormActionTest::testInvalidTypeIsRejected`
- `Test/Integration/Component/ProviderTest::testFormFieldsUseModuleOptions`
