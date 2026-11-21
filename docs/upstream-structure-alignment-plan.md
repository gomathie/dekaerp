# Upstream Structure Alignment Plan

> **Status:** Conversion done 2026-11-17 (all plugins in one pass, by script,
> at the user's request rather than one plugin per commit); verification in
> progress. See docs/change-log.md 2026-11-17 for what moved, what stayed
> inline and why.
> **Scope:** Filament resource file layout across all plugins
> **Starts:** after the Logistics plugin is finished and merged
> **Branch:** `chore/resource-structure` - created 2026-09-20 from
> `feature/logistics` at `1dbd81718`, not from `main`. `main` is dozens of
> commits behind and lacks WP-1/WP-2, the security work and upstream Package A;
> basing a whole-codebase refactor on it would guarantee conflicts. When
> Logistics merges, merge that result into this branch before continuing.
> Never committed straight to production.
> **Companion register:** `docs/upstream-fix-adoption-plan.md` (UF-001…UF-016)

## Is this possible?

Yes. It is a large but mechanical change, and it is the kind of refactor that
can be verified rather than hoped at, because it is meant to change **no
behaviour at all**. That property is what makes it safe, and it is also the
thing every step below is designed to prove.

It is not a merge from upstream. See the next section - that distinction is the
whole plan.

## The rule that governs everything here

**Restructure our files into upstream's shape. Never copy upstream's files
over ours.**

Our resources have diverged from upstream, measured on 2026-09-20:

| Fork-specific content | Resource files carrying it |
|---|---:|
| Company scoping (`company_id` / `CompanyScope` / `BelongsToCompany`) | 59 |
| Custom fields (`HasCustomFields` / `getCustomFormFields`) | 33 |
| Responsive columns (`visibleFrom`, from the mobile work) | 14 |

Upstream's `ProjectForm.php` is not our project form. Dropping theirs in place
of ours would delete multi-company scoping from dozens of resources - a
security regression that would not necessarily fail a test, because most tests
assert what a permitted user *can* see, not what a scoped-out user cannot.

Every file in this project is therefore a **move**, not a replacement: cut the
existing `form()` / `infolist()` / `table()` bodies out of our resource class
and into `Schemas/XForm.php`, `Schemas/XInfolist.php`, `Tables/XsTable.php`,
changing nothing inside them.

## Why do it at all

1. **Filament generates this layout.** `MakeResourceCommand` in the installed
   package builds `{namespace}\Schemas\{Model}Form`,
   `{namespace}\Schemas\{Model}Infolist` and `{namespace}\Tables\{Plural}Table`.
   Anything scaffolded today already disagrees with our hand-written resources.
2. **It is the main obstacle to backporting.** Upstream v1.6 moved every
   resource to this layout. While ours are monolithic, every future upstream
   fix touching a form or table has to be re-implemented by hand instead of
   applied as a patch. That cost recurs on every sync, forever.
3. File size: `ProjectResource.php` is 723 lines here; upstream's equivalent
   resource class is 125.

Point 2 is the only one that justifies the effort. Points 1 and 3 are nice.

## Sizing

**Decided 2026-09-20: all 227 resources are in scope**, not only the 139 with
inline schemas. Converting a subset would leave the plugin permanently
half-aligned, which defeats the point - a later upstream patch would still land
in the wrong shape for whichever resources were skipped.

| | Count |
|---|---:|
| Resource classes total - **all in scope** | **227** |
| Of those, defining `form()`/`table()` inline (the bulk of the work) | 139 |
| Already using `Schemas/`/`Tables/` (verify shape, then leave) | 13 |
| Remainder: thin resources, mostly delegation already | 75 |
| Lines across all resource classes | 57,143 |

Roughly 139 resources × up to 3 extracted classes ≈ **400 new files**, with the
resource classes shrinking correspondingly. The other 88 are an audit pass, not
a rewrite: confirm each already matches the layout, and leave it alone if it
does.

**The editing is not the expensive part. The verification is.** Plan the
calendar around test runs, not around file moves.

## Verification strategy

Existing suites are the baseline but they are not enough on their own: coverage
is uneven (the Employees plugin has no suite at all - see UF-002), and a
refactor that silently drops a column or a scope can pass a suite that never
asserted that column.

### 1. Structural snapshot harness - build this first

Before moving a single file, write a test that boots every registered resource
and serialises its schema: field names, component types, nesting, column names,
visibility, and the query the table builds. Store the output as a fixture.

After each plugin is converted, re-run it. A behaviour-preserving refactor
produces a **byte-identical** snapshot. Any diff is either a real regression or
a deliberate change that must be explained in the commit.

This is the cornerstone. It gives coverage to the 139 resources regardless of
whether their plugin has a feature suite, and it is far more sensitive than the
existing tests to exactly the failure mode that matters here - something quietly
lost in the move.

### 2. Per-plugin suite run

Convert one plugin, run its suite, stop on any failure. One plugin per commit
so a bad conversion reverts cleanly.

### 3. Full regression before merge

Every suite, serially. Historically ~2,479 tests; expect **8+ hours** of wall
time (AccountFeature alone was 3,861s; the four-suite inventory/sale/purchase/
manufacturing run was 15,699s).

**This belongs in CI, not on the laptop.** UF-001 is already done - the PHP
8.4.1 / PostgreSQL 17 lanes can now install the committed lockfile - so CI can
carry this burden. Before UF-001 this plan would not have been viable.

### 4. Manual spot-check

The snapshot harness cannot see CSS or rendering. Open, at minimum: one list
with responsive columns (one of the 14), one form with custom fields (one of
the 33), one resource with a summary row (the Filament vendor override), and
one company-scoped list as a restricted user.

## Order of work

Convert in descending order of test coverage, so the technique is proven on the
best-protected code and the thin-coverage plugins are done last with a harness
already trusted.

| Phase | Plugins | Why here |
|---|---|---|
| 0 | none - build the snapshot harness | Nothing moves until the net exists |
| 1 | Pilot: **one** resource in accounts | Calibrate real effort per resource; settle the commit shape |
| 2 | accounts, accounting | Best coverage (521 + suite) |
| 3 | inventories, sales, purchases, manufacturing | Large suites, 1,243 tests between them |
| 4 | products, partners, projects, support, security | Moderate coverage |
| 5 | employees, recruitments, time-off, timesheets, website, blogs, contacts, fields, maintenance | Thin or no coverage - hardest, do last. Consider closing UF-002 first |
| 6 | Logistics | Already partly compliant: WP-3 onward are written in the new layout, so only `ShipmentResource` needs converting |

Stop after any phase. Each is independently valuable and independently
revertable.

## Explicitly out of scope

This plan is **layout only**. It does not adopt upstream features or fixes.
Those stay in `docs/upstream-fix-adoption-plan.md` and keep their own decisions:

- Sales order price lists (UF-010) - separate initiative, changes financial behaviour
- Translatable Website/Blog content (UF-011) - deferred, converts production text to JSON
- Employee resume attachments (UF-012) - deferred pending tenant-S3 authorization design
- Installer country/currency (UF-009)

Mixing any of these into a mechanical refactor destroys the one property that
makes it safe: that the snapshot should not change.

## Risk controls

- **Own branch** (`chore/resource-structure`), never committed straight to
  `production`. Merge to `main` only after a full green regression.
- **One plugin per commit**, so a bad conversion is a single revert.
- **No behaviour changes smuggled in.** If a bug is spotted mid-refactor, write
  it down and fix it separately - a fix inside this branch is invisible among
  400 moved files.
- **No retrofit during feature work.** Converting a resource is this project's
  job, not a side effect of another package.
- **Do not start before Logistics is merged.** Both touch resource files; two
  streams in one working tree cost us three contaminated test runs on
  2026-09-18 alone. Serialise them, or use separate worktrees.

## Rollback

Per-commit revert during the work. After merge, the branch is a single
`git revert` of the merge commit - which is only true if no behaviour change
was mixed in, which is why the section above is strict.

## Working on this branch alongside Logistics

The branch exists so the work can be staged, but the two streams must not share
a working tree. Both edit resource files, and on 2026-09-18 two concurrent
streams in one tree produced three contradictory test runs before the cause was
found - the tree moved underneath each run.

Use a separate checkout rather than switching branches back and forth:

```bash
git worktree add ../dekaerp-structure chore/resource-structure
```

That gives `chore/resource-structure` its own directory, so Logistics work in
this tree is unaffected and neither stream sees the other's half-finished edits.

**The test database is still shared.** Every suite uses `aureuserp_testing` and
runs `migrate:fresh`, so two runs from two worktrees still collide. Either
serialise runs, or give the second worktree its own database name in its `.env`
before running anything.

## Open questions for the user

1. ~~Convert all 227 resources, or only the 139 with inline schemas?~~
   **Answered 2026-09-20: all 227.**
2. Close UF-002 (Employees factories + suite) before phase 5, so the
   thinnest-coverage plugins gain a real safety net first?
3. Is CI wall-clock time acceptable for a full regression per phase, or should
   full runs happen only at phase boundaries?
