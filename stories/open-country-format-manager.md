%title: Open the Country Format Manager
%status: complete
---

**As a** shop administrator,  
**I want to** find the country formats below Stores > Settings,  
**So that** I can manage country-specific address templates without database access.

## Acceptance Criteria

### when-admin-opens-the-menu

**Given** an admin is logged in  
**When** the admin clicks Stores > Settings > Country Format Manager  
**Then** the grid of country formats is shown.

Tests:
- `Test/Integration/Acl/AclTest::testMenuItemIsAddedBelowStoresSettings`
- `Test/Integration/ModuleTest::testAdminRouteIsRegistered`
- `Test/Integration/Controller/Adminhtml/Index/GridTest::testGridPageRenders`
- `Test/Playwright/menu-test.spec.ts: opens the grid via Stores > Settings > Country Format Manager`
