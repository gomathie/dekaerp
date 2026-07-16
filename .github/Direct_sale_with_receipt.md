
IMPORTANT:

Before creating any new controller, model, migration, service, or database field:

1. Search the entire codebase for existing functionality.
2. Reuse existing invoice creation logic.
3. Reuse existing payment recording logic.
4. Reuse existing inventory deduction logic.
5. Reuse existing receipt/invoice printing logic.
6. Reuse existing validation rules.
7. Reuse existing permissions and role checks.

Only create new code when existing functionality cannot be reused.

The Direct Sale feature should act as a thin layer on top of the existing invoice system, not a separate sales system.
---

# Task: Add Direct Sale + Receipt Workflow to Aureus ERP (Laravel)

## Objective

Implement a new **Direct Sale** feature that allows users to:

1. Create a sale without first creating a quotation.
2. Record payment immediately.
3. Reduce inventory.
4. Generate and print a receipt.
5. Reuse existing invoice, stock, accounting, and reporting logic.
6. Store the transaction as a normal paid invoice in the database.

Do NOT create a separate sales engine. Reuse the existing invoice workflow wherever possible.

---

## Phase 1 – Analyze Existing System

### Step 1: Discover Sales Architecture

Inspect and document:

- Invoice models
- Invoice controllers
- Payment models
- Payment controllers
- Inventory deduction logic
- Accounting/journal logic
- Receipt or invoice printing logic

Produce a report showing:

```text
InvoiceController
Invoice Model
Payment Model
Stock Update Logic
Invoice Print View
Routes
```

Do not modify code yet.

---

### Step 2: Trace Invoice Creation Flow

Determine:

```text
User submits invoice
↓
Invoice created
↓
Invoice items created
↓
Stock deducted
↓
Payment recorded
↓
Invoice marked paid
```

Identify:

- Methods
- Services
- Traits
- Events
- Observers

used during invoice creation.

Document all findings.

---

## Phase 2 – Design Direct Sale Workflow

### Step 3: Design New Flow

Create:

```text
Direct Sale Screen
↓
Create Invoice Automatically
↓
Create Payment Automatically
↓
Mark Invoice Paid
↓
Print Receipt
```

Requirements:

- No quotation required.
- No separate inventory logic.
- No duplicated accounting logic.
- Must reuse existing invoice functionality.

Provide implementation plan before coding.

---

## Phase 3 – Database Review

### Step 4: Verify Existing Tables

Inspect:

```text
invoices
invoice_items
payments
customers
products
inventory tables
```

Determine whether schema changes are required.

Preferred approach:

Add minimal fields only if necessary.

Examples:

```php
sale_source
receipt_number
```

Avoid creating new sales tables unless absolutely required.

---

## Phase 4 – Backend Implementation

### Step 5: Create DirectSaleController

Create:

```php
app/Http/Controllers/DirectSaleController.php
```

Responsibilities:

- Validate request
- Create invoice
- Create invoice items
- Create payment
- Mark invoice paid
- Trigger stock updates
- Redirect to receipt

Use transactions:

```php
DB::transaction(...)
```

to ensure consistency.

---

### Step 6: Reuse Existing Services

Before writing new code:

Search for existing methods such as:

```php
createInvoice()
storeInvoice()
recordPayment()
updateInventory()
```

Reuse them.

Do not duplicate logic already present in the ERP.

---

### Step 7: Walk-In Customer Support

Create configuration for:

```text
Walk-In Customer
Cash Customer
Retail Customer
```

Requirements:

- Allow sale without selecting a customer.
- Automatically assign default walk-in customer.
- Preserve customer reporting.

---

## Phase 5 – User Interface

### Step 8: Add Menu Entry

Add:

```text
Sales
├── Quotations
├── Invoices
└── Direct Sale
```

Use existing menu patterns.

---

### Step 9: Build Direct Sale Form

Fields:

```text
Customer (optional)
Product Search
Quantity
Unit Price
Discount
Tax
Payment Method
Paid Amount
Notes
```

Buttons:

```text
Save Sale
Save & Print Receipt
```

Use existing invoice UI components whenever possible.

---

### Step 10: Product Selection

Support:

```text
Search products
Select products
Adjust quantity
Auto calculate totals
```

Reuse invoice product components if available.

---

## Phase 6 – Receipt System

### Step 11: Create Receipt View

Create:

```text
resources/views/sales/receipt.blade.php
```

Receipt should display:

```text
Receipt Number
Date
Cashier
Customer
Items
Quantities
Prices
Taxes
Total
Paid Amount
Payment Method
```

Format for thermal printer compatibility.

---

### Step 12: Print Workflow

After successful sale:

```text
Save Sale
↓
Generate Receipt
↓
Open Print Dialog
```

Implement:

```javascript
window.print();
```

where appropriate.

---

## Phase 7 – Reporting Compatibility

### Step 13: Verify Reports

Ensure Direct Sales appear in:

```text
Sales Reports
Revenue Reports
Profit Reports
Tax Reports
Customer Reports
```

without additional reporting code.

Because Direct Sales are stored as invoices.

---

### Step 14: Verify Inventory

Confirm:

```text
Sale created
↓
Inventory reduced
↓
Stock reports updated
```

using existing inventory mechanisms.

---

## Phase 8 – Testing

### Step 15: Functional Tests

Test:

### Cash Sale

```text
Create direct sale
Pay full amount
Print receipt
```

Expected:

```text
Invoice created
Payment created
Inventory reduced
Receipt printed
```

---

### Partial Payment

If supported:

```text
Total = 100
Paid = 50
```

Expected:

```text
Invoice status = Partial
```

---

### Walk-In Customer

Expected:

```text
Sale completes successfully
Assigned to default customer
```

---

### Multi-Item Sale

Expected:

```text
All stock levels updated correctly
Totals accurate
```

---

## Phase 9 – Deliverables

Provide:

### 1. Architecture Summary

Document:

```text
Files modified
Controllers added
Routes added
Views added
Models affected
```

---

### 2. Migration Scripts

If any schema changes were necessary.

---

### 3. Testing Results

Show:

```text
Successful Direct Sale
Receipt Generation
Inventory Deduction
Report Visibility
```

---

## Important Constraints

- Do not break existing invoice workflow.
- Do not remove quotation functionality.
- Reuse existing business logic whenever possible.
- Minimize database changes.
- Follow existing Aureus ERP coding conventions.
- Use Laravel best practices.
- Wrap financial operations in database transactions.
- Ensure inventory and payment records remain consistent.

**Success Criteria:**
A cashier can open "Direct Sale", select products, receive payment, save the transaction, and immediately print a receipt, while the ERP records everything as a standard paid invoice behind the scenes.


Analyze the codebase and generate a report.
Wait for approval.
Build backend functionality.
Wait for approval.
Build UI.
Wait for approval.
Build receipt printing.
Run tests.
Generate a deployment guide.


