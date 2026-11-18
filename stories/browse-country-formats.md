%title: Browse country formats
%status: complete
---

**As a** shop administrator,  
**I want to** see, search and filter all country formats,  
**So that** I quickly find the address template of a country.

## Acceptance Criteria

### when-admin-opens-the-grid

**Given** country formats exist  
**When** the admin opens the grid  
**Then** the columns ID, Country, Type and Format are shown  
**And** the country is shown with its name and code  
**And** the type is shown with its label.

Tests:
- `Test/Integration/Controller/Adminhtml/Index/GridTest::testGridShowsCountryFormatColumns`
- `Test/Integration/Controller/Adminhtml/Index/GridTest::testGridListsCountryFormat`
- `Test/Unit/ViewModel/CountryNameTest::testGetName`
- `Test/Playwright/grid-test.spec.ts: lists a country format with its columns`

### when-admin-searches-or-filters

**Given** the grid is shown  
**When** the admin searches or filters  
**Then** the country, type and format can be searched  
**And** the country and type can be filtered.

Tests:
- `Test/Integration/Component/ProviderTest::testGridHasSearchAndFilters`
