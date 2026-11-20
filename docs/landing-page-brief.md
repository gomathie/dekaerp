# DEKA ERP — Landing Page Brief

**For:** the developer building `dekaerp.com`
**Deliverable:** a marketing landing page
**Status:** not started

---

## 1. What you are building

A marketing landing page for **DEKA ERP**, an ERP system for small and medium
enterprises. The page sells the product. It does not contain the product.

There are two domains, and keeping them straight matters:

| Domain | What lives there | Who builds it |
| --- | --- | --- |
| `dekaerp.com` | The marketing site — this brief | You |
| `cloud.dekaerp.com` | The running application | Already exists |

**Every call to action on the page links to `https://cloud.dekaerp.com`.**
Sign-in specifically is `https://cloud.dekaerp.com/admin/login`. No CTA should
link to another page on `dekaerp.com`.

---

## 2. Who it is written for

Operations managers, finance staff and business owners at SMEs — typically
10–200 employees, often running several legal entities.

They are **not developers**. They are people currently running the business on
spreadsheets, an accounting package and a stock list that disagree with each
other.

Write about closing the month and knowing what is in the warehouse. Do not
mention Laravel, PHP or Filament anywhere in the main copy. There is a technical
footnote section at the end if you want one.

---

## 3. The product — what you may claim

Everything below was verified against the source code. **Do not add claims that
are not on this list.** This is a real product being sold to real customers;
inventing a capability is a bug, not marketing.

### Modules that exist

**Sell**
- Sales — quotations, sales orders, order templates
- Invoicing — customer invoices, credit notes, refunds, payment terms
- Contacts — customers, vendors, bank accounts, industries
- Customer portal — customers log in to see their own orders and documents

**Buy**
- Purchasing — RFQs, purchase orders, vendor bills, vendor pricelists
- Accounting — chart of accounts, journals, taxes, fiscal positions, reporting
- Payments — payment methods, registration, reconciliation against invoices

**Make and move**
- Inventory — warehouses, locations, receipts, deliveries, internal transfers,
  dropship, lots and serial numbers, packages, scrap, putaway rules,
  replenishment
- Manufacturing — bills of materials, manufacturing orders, work orders, work
  centres, component availability
- Products — variants, categories, attributes, units of measure, pricelists
- Barcode — barcode scanning for inventory operations

**Run the business**
- Projects — projects, tasks, milestones, stages
- Timesheets — time recorded against projects and tasks
- Employees — records, departments, job positions, contracts, documents
- Time off — leave types, allocations, requests, approvals
- Recruitment — job postings, applicants, stages, offers
- Maintenance — equipment, maintenance requests, teams, scheduling

**Platform**
- Multi-company with per-company data separation and a company switcher
- Roles and permissions, controlled per module
- Discussion threads, mentions and file attachments on individual records
- Custom fields across most modules
- Command palette (Ctrl/Cmd-K) for keyboard navigation
- Dashboards with date-range filtering
- Website and blog pages managed from the same admin
- REST API with token authentication
- Import/export to Excel; PDF generation for invoices, orders, delivery notes
- Modules installable or removable per deployment

### The five things worth leading on

1. **Multi-company is built in.** One installation runs several legal entities,
   with data separated per company, users granted access to specific companies,
   and a switcher to move between them. Most systems at this level charge extra
   for this or do not offer it.
2. **You install only the modules you use.** A business that does not
   manufacture never sees Manufacturing.
3. **Hosted or self-hosted.** Runs on our infrastructure, or entirely on the
   customer's own — which matters where data residency does.
4. **The conversation lives on the record.** Discussion, mentions and
   attachments sit on the invoice or order itself, not scattered across email.
5. **Five languages ship today** — English, Arabic (including right-to-left
   PDFs), French, Spanish and Brazilian Portuguese.

### Benefits — say the outcome, not the feature

- One system from quotation through delivery, invoice and payment, with no
  re-keying between tools
- Stock figures that match reality, because inventory moves when orders are
  confirmed
- Month-end close on real transactions instead of reconciled spreadsheets
- Every document, discussion and approval attached to what it relates to
- Visibility across all companies in the group, without separate installations
- Staff see only what their role permits

---

## 4. What must NOT go on the page

Non-negotiable. Each of these would be false today:

- Customer names, logos or testimonials — there are none to show yet
- Customer counts, revenue, "trusted by N businesses"
- Uptime figures or SLA promises
- Certifications — SOC 2, ISO 27001 and similar have **not** been obtained
- Named integrations — Shopify, Stripe, QuickBooks, Xero and the like do
  **not** ship
- Mobile apps — there is groundwork, not a released app
- AI or "smart" features — there are none
- Pricing — do not invent numbers; use a "Talk to us" CTA until confirmed

If a section feels thin without one of these, leave it thin. An honest page that
converts less is better than a false one that gets challenged in a sales call.

---

## 5. Design direction

- **Accent colour: amber.** The application's admin panel uses amber as its
  primary colour, and the site should feel like the same product. Pair it with a
  considered neutral.
- **Avoid the default SaaS look** — purple-to-blue gradient hero, floating
  glassmorphic cards, stock photos of people pointing at laptops.
- **Show the product.** Screenshots of the real admin panel are worth more than
  any illustration. If you do not have them, ask — do not substitute stock
  imagery.
- Must work on mobile. Operations staff will open this on a phone.
- Respect light and dark preference if it is cheap; do not contort the design
  for it.

### Suggested section order

1. Hero — what it is, who it is for, primary CTA
2. The problem — spreadsheets and disconnected tools
3. Modules — grouped as above (Sell / Buy / Make and move / Run the business)
4. Benefits — outcomes
5. Multi-company — give this its own section; it is the differentiator
6. Deployment — hosted or self-hosted
7. Pricing — only once numbers are confirmed
8. Closing CTA

Deviate if you have a better idea. This is a starting point, not a specification.

---

## 6. Technical constraints

- **Static output.** Plain HTML/CSS, or any static site generator you prefer
  (Astro, Eleventy, Next static export). No server-side rendering is required —
  the page has no dynamic content.
- Fast on a mid-range phone over 4G. Inline critical CSS, compress images, and
  avoid heavy frameworks for what is a brochure page.
- Accessible: real semantic headings, visible keyboard focus, alt text,
  sufficient contrast in both themes.
- SEO basics: title, meta description, Open Graph tags for link previews,
  favicon.
- Analytics only if asked. Do not add a tracker on your own initiative.

---

## 7. Definition of done

- [ ] Renders correctly at 360px, 768px and 1440px
- [ ] Every CTA resolves to `https://cloud.dekaerp.com`
- [ ] No claim on the page falls outside section 3
- [ ] Nothing from section 4 appears anywhere
- [ ] Lighthouse performance and accessibility both at least 90 on mobile
- [ ] Open Graph preview renders correctly when the link is pasted into a chat
- [ ] Source is in a repository the team can access

---

## 8. Open questions — ask before starting

1. Is there a logo and brand asset set, or should the page use type only?
2. Are product screenshots available? If not, can a demo account be provided?
3. Is pricing confirmed, or is it "Talk to us" for now?
4. Where does "Book a demo" go — a calendar link, a form, or an email address?
5. Is a contact form needed? If so, what receives the submission?
6. Which repository, and which host?
