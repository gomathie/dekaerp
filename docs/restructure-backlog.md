# v1.6.0 restructure backlog

Upstream v1.6.0 moved every Filament resource's `form()`, `table()` and
`infolist()` body out into `Schemas/XxxForm.php`, `Schemas/XxxInfolist.php`
and `Tables/XxxTable.php` classes. The body is copied verbatim - **no
behaviour changes**. That single refactor is why ~150 files report as
differing while saying nothing, and it is what makes each upgrade harder to
read than it needs to be.

Adopting it is merge-debt reduction, not a fix. Nothing breaks by leaving it.

## The rule

Adopt where this fork has **no** content of its own in that resource - the
extracted body is then the same code, relocated. Defer where the fork
customised the form or table, because that is exactly where a mechanical move
silently drops company scoping or a local fix.

## How the lists below were produced

Both sides are normalised to code lines (imports, namespace, class
scaffolding and whitespace removed), then compared as sets:

- **Safe** - every line in the fork's resource also exists in upstream's
  resource plus its extracted classes. Nothing of this fork's is lost.
- **Review** - the fork has lines upstream does not. Those are local changes;
  they must be carried into the new class by hand, not overwritten.

**Known limit of this check:** it compares sets, so it proves no content is
lost but not that *order* is preserved. In Filament, field order is display
order. Before adopting a batch, diff the field sequence too, or accept that
screens may reorder.

## Recommendation

Do this after the PHP 8.4 runtime is fixed and the Pest suite can run, and go
plugin by plugin so a regression is attributable to one batch. Doing 70
resources blind, on a production ERP, buys nothing that cannot wait.

SAFE (pure relocation, adopting cannot change behaviour): 70
  plugins/webkul/accounting/src/Filament/Clusters/Accounting/Resources/JournalItemResource.php
  plugins/webkul/accounts/src/Filament/Resources/CashRoundingResource.php
  plugins/webkul/accounts/src/Filament/Resources/FiscalPositionResource.php
  plugins/webkul/accounts/src/Filament/Resources/IncotermResource.php
  plugins/webkul/accounts/src/Filament/Resources/JournalResource.php
  plugins/webkul/accounts/src/Filament/Resources/PaymentTermResource.php
  plugins/webkul/accounts/src/Filament/Resources/ProductCategoryResource.php
  plugins/webkul/accounts/src/Filament/Resources/TaxGroupResource.php
  plugins/webkul/blogs/src/Filament/Admin/Clusters/Configurations/Resources/CategoryResource.php
  plugins/webkul/blogs/src/Filament/Admin/Clusters/Configurations/Resources/TagResource.php
  plugins/webkul/employees/src/Filament/Clusters/Configurations/Resources/ActivityPlanResource.php
  plugins/webkul/employees/src/Filament/Clusters/Configurations/Resources/DepartureReasonResource.php
  plugins/webkul/employees/src/Filament/Clusters/Configurations/Resources/EmployeeCategoryResource.php
  plugins/webkul/employees/src/Filament/Clusters/Configurations/Resources/EmploymentTypeResource.php
  plugins/webkul/employees/src/Filament/Clusters/Configurations/Resources/JobPositionResource.php
  plugins/webkul/employees/src/Filament/Clusters/Configurations/Resources/WorkLocationResource.php
  plugins/webkul/employees/src/Filament/Clusters/Reportings/Resources/EmployeeSkillResource.php
  plugins/webkul/inventories/src/Filament/Clusters/Configurations/Resources/LocationResource.php
  plugins/webkul/inventories/src/Filament/Clusters/Configurations/Resources/OperationTypeResource.php
  plugins/webkul/inventories/src/Filament/Clusters/Configurations/Resources/PackageTypeResource.php
  plugins/webkul/inventories/src/Filament/Clusters/Configurations/Resources/ProductCategoryResource.php
  plugins/webkul/inventories/src/Filament/Clusters/Configurations/Resources/PutawayRuleResource.php
  plugins/webkul/inventories/src/Filament/Clusters/Configurations/Resources/RouteResource.php
  plugins/webkul/inventories/src/Filament/Clusters/Configurations/Resources/RuleResource.php
  plugins/webkul/inventories/src/Filament/Clusters/Operations/Resources/DeliveryResource.php
  plugins/webkul/inventories/src/Filament/Clusters/Operations/Resources/DropshipResource.php
  plugins/webkul/inventories/src/Filament/Clusters/Operations/Resources/InternalResource.php
  plugins/webkul/inventories/src/Filament/Clusters/Operations/Resources/ReceiptResource.php
  plugins/webkul/inventories/src/Filament/Clusters/Operations/Resources/ReplenishmentResource.php
  plugins/webkul/inventories/src/Filament/Clusters/Products/Resources/PackageResource.php
  plugins/webkul/maintenance/src/Filament/Clusters/Configurations/Resources/EquipmentCategoryResource.php
  plugins/webkul/maintenance/src/Filament/Clusters/Configurations/Resources/StageResource.php
  plugins/webkul/maintenance/src/Filament/Clusters/Configurations/Resources/TeamResource.php
  plugins/webkul/manufacturing/src/Filament/Clusters/Configurations/Resources/WorkCenterResource.php
  plugins/webkul/partners/src/Filament/Resources/AddressResource.php
  plugins/webkul/partners/src/Filament/Resources/BankAccountResource.php
  plugins/webkul/partners/src/Filament/Resources/IndustryResource.php
  plugins/webkul/partners/src/Filament/Resources/TagResource.php
  plugins/webkul/partners/src/Filament/Resources/TitleResource.php
  plugins/webkul/products/src/Filament/Resources/CategoryResource.php
  plugins/webkul/products/src/Filament/Resources/PackagingResource.php
  plugins/webkul/products/src/Filament/Resources/PriceListResource.php
  plugins/webkul/projects/src/Filament/Clusters/Configurations/Resources/ActivityPlanResource.php
  plugins/webkul/projects/src/Filament/Clusters/Configurations/Resources/ProjectStageResource.php
  plugins/webkul/projects/src/Filament/Clusters/Configurations/Resources/TagResource.php
  plugins/webkul/projects/src/Filament/Clusters/Configurations/Resources/TaskStageResource.php
  plugins/webkul/purchases/src/Filament/Admin/Clusters/Configurations/Resources/VendorPriceResource.php
  plugins/webkul/recruitments/src/Filament/Clusters/Applications/Resources/JobByPositionResource.php
  plugins/webkul/recruitments/src/Filament/Clusters/Configurations/Resources/ActivityPlanResource.php
  plugins/webkul/recruitments/src/Filament/Clusters/Configurations/Resources/ApplicantCategoryResource.php
  plugins/webkul/recruitments/src/Filament/Clusters/Configurations/Resources/DegreeResource.php
  plugins/webkul/recruitments/src/Filament/Clusters/Configurations/Resources/JobPositionResource.php
  plugins/webkul/recruitments/src/Filament/Clusters/Configurations/Resources/RefuseReasonResource.php
  plugins/webkul/recruitments/src/Filament/Clusters/Configurations/Resources/StageResource.php
  plugins/webkul/recruitments/src/Filament/Clusters/Configurations/Resources/UTMMediumResource.php
  plugins/webkul/recruitments/src/Filament/Clusters/Configurations/Resources/UTMSourceResource.php
  plugins/webkul/sales/src/Filament/Clusters/Configuration/Resources/ActivityPlanResource.php
  plugins/webkul/sales/src/Filament/Clusters/Configuration/Resources/TagResource.php
  plugins/webkul/sales/src/Filament/Clusters/Orders/Resources/CustomerResource.php
  plugins/webkul/security/src/Filament/Resources/TeamResource.php
  plugins/webkul/support/src/Filament/Resources/BankResource.php
  plugins/webkul/support/src/Filament/Resources/CalendarResource.php
  plugins/webkul/support/src/Filament/Resources/UOMCategoryResource.php
  plugins/webkul/time-off/src/Filament/Clusters/Configurations/Resources/AccrualPlanResource.php
  plugins/webkul/time-off/src/Filament/Clusters/Configurations/Resources/LeaveTypeResource.php
  plugins/webkul/time-off/src/Filament/Clusters/Configurations/Resources/MandatoryDayResource.php
  plugins/webkul/time-off/src/Filament/Clusters/Configurations/Resources/PublicHolidayResource.php
  plugins/webkul/time-off/src/Filament/Clusters/Management/Resources/AllocationResource.php
  plugins/webkul/time-off/src/Filament/Clusters/MyTime/Resources/MyAllocationResource.php
  plugins/webkul/time-off/src/Filament/Clusters/MyTime/Resources/MyTimeOffResource.php

NEEDS REVIEW (content differs): 51
  plugins/webkul/accounting/src/Filament/Clusters/Accounting/Resources/JournalEntryResource.php   fork-only:12  upstream-only:15
  plugins/webkul/accounts/src/Filament/Resources/AccountResource.php                              fork-only:8   upstream-only:11
  plugins/webkul/accounts/src/Filament/Resources/AccountTagResource.php                           fork-only:2   upstream-only:3
  plugins/webkul/accounts/src/Filament/Resources/BillResource.php                                 fork-only:12  upstream-only:23
  plugins/webkul/accounts/src/Filament/Resources/InvoiceResource.php                              fork-only:19  upstream-only:41
  plugins/webkul/accounts/src/Filament/Resources/PaymentResource.php                              fork-only:7   upstream-only:10
  plugins/webkul/blogs/src/Filament/Admin/Resources/PostResource.php                              fork-only:6   upstream-only:9
  plugins/webkul/employees/src/Filament/Clusters/Configurations/Resources/SkillTypeResource.php   fork-only:1   upstream-only:4
  plugins/webkul/employees/src/Filament/Resources/DepartmentResource.php                          fork-only:3   upstream-only:6
  plugins/webkul/employees/src/Filament/Resources/EmployeeResource.php                            fork-only:6   upstream-only:110
  plugins/webkul/fields/src/Filament/Resources/FieldResource.php                                  fork-only:10  upstream-only:13
  plugins/webkul/inventories/src/Filament/Clusters/Configurations/Resources/PackagingResource.php fork-only:6   upstream-only:9
  plugins/webkul/inventories/src/Filament/Clusters/Configurations/Resources/StorageCategoryResource.php fork-only:2   upstream-only:5
  plugins/webkul/inventories/src/Filament/Clusters/Configurations/Resources/WarehouseResource.php fork-only:9   upstream-only:12
  plugins/webkul/inventories/src/Filament/Clusters/Operations/Resources/QuantityResource.php      fork-only:18  upstream-only:20
  plugins/webkul/inventories/src/Filament/Clusters/Operations/Resources/ScrapResource.php         fork-only:19  upstream-only:22
  plugins/webkul/inventories/src/Filament/Clusters/Products/Resources/LotResource.php             fork-only:6   upstream-only:9
  plugins/webkul/inventories/src/Filament/Clusters/Reporting/Resources/MoveResource.php           fork-only:10  upstream-only:11
  plugins/webkul/inventories/src/Filament/Clusters/Reporting/Resources/QuantityResource.php       fork-only:12  upstream-only:14
  plugins/webkul/maintenance/src/Filament/Clusters/Maintenance/Resources/MaintenanceRequestResource.php fork-only:5   upstream-only:8
  plugins/webkul/maintenance/src/Filament/Resources/EquipmentResource.php                         fork-only:6   upstream-only:9
  plugins/webkul/manufacturing/src/Filament/Clusters/Configurations/Resources/OperationResource.php fork-only:5   upstream-only:8
  plugins/webkul/manufacturing/src/Filament/Clusters/Operations/Resources/ManufacturingOrderResource.php fork-only:32  upstream-only:35
  plugins/webkul/manufacturing/src/Filament/Clusters/Operations/Resources/TransferResource.php    fork-only:2   upstream-only:3
  plugins/webkul/manufacturing/src/Filament/Clusters/Operations/Resources/WorkOrderResource.php   fork-only:21  upstream-only:27
  plugins/webkul/manufacturing/src/Filament/Clusters/Products/Resources/BillsOfMaterialResource.php fork-only:13  upstream-only:16
  plugins/webkul/plugin-manager/src/Filament/Resources/PluginResource.php                         fork-only:28  upstream-only:23
  plugins/webkul/products/src/Filament/Resources/AttributeResource.php                            fork-only:13  upstream-only:75
  plugins/webkul/projects/src/Filament/Clusters/Configurations/Resources/MilestoneResource.php    fork-only:5   upstream-only:7
  plugins/webkul/projects/src/Filament/Resources/ProjectResource.php                              fork-only:27  upstream-only:30
  plugins/webkul/projects/src/Filament/Resources/TaskResource.php                                 fork-only:33  upstream-only:36
  plugins/webkul/purchases/src/Filament/Admin/Clusters/Orders/Resources/OrderResource.php         fork-only:31  upstream-only:66
  plugins/webkul/purchases/src/Filament/Admin/Clusters/Orders/Resources/PurchaseAgreementResource.php fork-only:10  upstream-only:17
  plugins/webkul/purchases/src/Filament/Admin/Clusters/Orders/Resources/PurchaseOrderResource.php fork-only:1   upstream-only:2
  plugins/webkul/purchases/src/Filament/Admin/Clusters/Orders/Resources/QuotationReceiptResource.php fork-only:2   upstream-only:3
  plugins/webkul/purchases/src/Filament/Admin/Clusters/Products/Resources/ProductResource.php     fork-only:3   upstream-only:2
  plugins/webkul/purchases/src/Filament/Customer/Clusters/Account/Resources/OrderResource.php     fork-only:2   upstream-only:4
  plugins/webkul/recruitments/src/Filament/Clusters/Applications/Resources/ApplicantResource.php  fork-only:6   upstream-only:9
  plugins/webkul/recruitments/src/Filament/Clusters/Applications/Resources/CandidateResource.php  fork-only:6   upstream-only:9
  plugins/webkul/sales/src/Filament/Clusters/Configuration/Resources/TeamResource.php             fork-only:1   upstream-only:4
  plugins/webkul/sales/src/Filament/Clusters/Orders/Resources/QuotationDeliveryResource.php       fork-only:2   upstream-only:3
  plugins/webkul/sales/src/Filament/Clusters/Orders/Resources/QuotationResource.php               fork-only:7   upstream-only:16
  plugins/webkul/sales/src/Filament/Clusters/Products/Resources/ProductResource.php               fork-only:3   upstream-only:2
  plugins/webkul/security/src/Filament/Resources/RoleResource.php                                 fork-only:6   upstream-only:8
  plugins/webkul/security/src/Filament/Resources/UserResource.php                                 fork-only:4   upstream-only:7
  plugins/webkul/support/src/Filament/Resources/ActivityTypeResource.php                          fork-only:1   upstream-only:4
  plugins/webkul/support/src/Filament/Resources/CompanyResource.php                               fork-only:10  upstream-only:17
  plugins/webkul/support/src/Filament/Resources/CurrencyResource.php                              fork-only:3   upstream-only:12
  plugins/webkul/time-off/src/Filament/Clusters/Management/Resources/TimeOffResource.php          fork-only:5   upstream-only:8
  plugins/webkul/timesheets/src/Filament/Resources/TimesheetResource.php                          fork-only:5   upstream-only:7
  plugins/webkul/website/src/Filament/Admin/Resources/PageResource.php                            fork-only:6   upstream-only:9

No extracted classes upstream (not a restructure): 0
