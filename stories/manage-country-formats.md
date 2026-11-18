%title: Manage country formats
%status: complete
---

**As a** shop administrator,  
**I want to** create, edit and delete the address template of a country,  
**So that** addresses are shown in the format that is common in that country.

## Acceptance Criteria

### when-admin-creates-a-country-format

**Given** the admin clicks Add New  
**When** the admin selects a country and a type, enters a format and saves  
**Then** the country format is stored and listed in the grid.

Tests:
- `Test/Integration/Controller/Adminhtml/Index/FormTest::testNewFormRendersFields`
- `Test/Integration/Form/FormActionTest::testCountryFormatIsCreated`
- `Test/Integration/Component/ProviderTest::testProviderHandlerSavesAndLoadsCountryFormat`
- `Test/Playwright/grid-test.spec.ts: opens a new form via the "Add New" button`
- `Test/Playwright/form-test.spec.ts: creates, edits and deletes a country format`

### when-admin-chooses-a-type

**Given** the form is shown  
**When** the admin opens the Type field  
**Then** all address formats of Magento are offered (Text, Text One Line, HTML, PDF and formats of other modules).

Tests:
- `Test/Unit/ViewModel/Options/AddressTemplateTypeOptionsTest::testToOptionArrayReturnsFormatsFromAddressConfig`
- `Test/Integration/Component/ProviderTest::testFormFieldsUseModuleOptions`
- `Test/Playwright/form-test.spec.ts: offers the address template types`

### when-admin-leaves-the-format-empty

**Given** the admin selects a country and a type  
**When** the admin saves without a format  
**Then** the default address template of that type is stored as a starting point.

Tests:
- `Test/Unit/Form/CountryFormatConvertorTest::testEmptyFormatIsFilledWithDefaultFormatOfType`
- `Test/Integration/Form/FormActionTest::testEmptyFormatIsFilledWithDefaultTemplate`

### when-admin-edits-or-deletes-a-country-format

**Given** a country format exists  
**When** the admin edits and saves it, or deletes it  
**Then** the change is stored.

Tests:
- `Test/Integration/Controller/Adminhtml/Index/FormTest::testEditFormRendersExistingCountryFormat`
- `Test/Integration/Form/FormActionTest::testExistingCountryFormatCanBeSavedAgain`
- `Test/Integration/Form/FormActionTest::testCountryFormatIsDeletedWithDeletePermission`
- `Test/Integration/Component/ProviderTest::testProviderHandlerUpdatesAndDeletesCountryFormat`
- `Test/Playwright/form-test.spec.ts: creates, edits and deletes a country format`
- `Test/Playwright/form-test.spec.ts: deletes a country format via the "Delete" button`
