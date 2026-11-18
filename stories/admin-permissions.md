%title: Restrict admin access
%status: complete
---

**As a** shop owner,  
**I want to** control which admin users view, save and delete country formats,  
**So that** only authorised staff change the addresses on invoices and emails.

## Acceptance Criteria

### when-admin-edits-a-role

**Given** an admin edits a role  
**When** the role resource tree is shown  
**Then** Country Format Manager is listed under Stores > Settings with View, Open Form, Save and Delete.

Tests:
- `Test/Integration/Acl/AclTest::testAclResourcesExist`
- `Test/Integration/Acl/AclTest::testAclResourcesHaveReadableTitles`

### when-admin-lacks-a-permission

**Given** an admin role lacks the save, delete or form permission  
**When** the admin saves or deletes a country format  
**Then** the change is not applied.

Tests:
- `Test/Integration/Form/FormActionTest::testCountryFormatIsNotSavedWithoutSavePermission`
- `Test/Integration/Form/FormActionTest::testCountryFormatIsNotDeletedWithoutDeletePermission`
- `Test/Integration/Form/FormActionTest::testCountryFormatIsNotDeletedWithoutFormPermission`
- `Test/Integration/Component/ProviderTest::testBlocksRequireViewPermission`
- `Test/Integration/Controller/Adminhtml/Index/GridTest::testAclNoAccess`
- `Test/Integration/Controller/Adminhtml/Index/FormTest::testAclNoAccess`
