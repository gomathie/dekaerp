# Rebrand: Webkul → 365 Systems — Plan

Status: **plan written 2026-11-13 (user: "change anything Webkul to
365Systems to maintain ownership of our app"); all four recommendations
accepted by the user the same day (section 6). Licence notice fixed 2026-11-13;
Option A (visible brand) queued after accounting A2b; B after the Logistics
merge.**

---

## 1. What "Webkul" is in this repo today (measured 2026-11-13)

| Where | Size | Example |
| --- | --- | --- |
| PHP namespace | ~16,200 occurrences in ~3,700 files | `Webkul\Account\Models\Move` |
| Folder | every plugin | `plugins/webkul/<plugin>/` |
| Composer packages / autoload | one `composer.json` per plugin, merged by `wikimedia/composer-merge-plugin` | `"webkul/accounts"`, `"Webkul\\Account\\": "src/"` |
| Strings that hold class names | seeders, Shield config, discovery paths | `'Webkul\\Logistics\\Database\\Seeders\\DatabaseSeeder'` |
| **Database rows that hold class names** | no morph map is used, so every polymorphic column stores the full class | `chatter_messages.messageable_type`, `causer_type`, custom fields, table views, notifications, media, activity, queued job payloads |
| Docs, README, CHANGELOG | many | "Webkul" in prose |
| Upstream | AureusERP is Webkul's product | merges/cherry-picks from upstream |

## 2. Two things to know first

**A PHP namespace cannot start with a digit.** `365Systems\Account\...` is a
syntax error. The namespace needs a name that starts with a letter:
`Systems365`, `ThreeSixtyFive`, `S365`, or the product name `Deka`. The
folder, Composer vendor and every visible text can still say **365 Systems**
(`plugins/365systems/`, `"365systems/accounts"`), because those are not PHP
identifiers.

**The MIT licence requires keeping the original copyright notice.** The
`LICENSE` file's line was changed from "Webkul Software" to "365 Integrated
Systems" (commit 29c0e7e80). MIT lets you rename, fork, sell and relicense
your own changes, but its one condition is that the original notice stays
in copies of the software. The safe form is both lines:

    Copyright (c) 2010-2025 Webkul Software (original AureusERP)
    Copyright (c) 2025-2026 365 Integrated Systems (DEKA ERP modifications)

Ownership of DEKA comes from your own additions (Logistics, platform
billing, SaaS administration, the fork's changes) and your brand, not from
removing Webkul's line. Removing it is the one thing that could weaken the
position this plan is meant to strengthen. This is not legal advice: a
lawyer should confirm the wording before investors see it.

## 3. Options

| | What changes | Effort | Risk |
| --- | --- | --- | --- |
| **A. Visible brand only** (recommended first) | Every name a user, client or investor sees: UI, emails, PDFs, API docs, README, landing page, Composer `description`/`authors`, package names in docs. Code namespace stays | 1 day | Very low; no data or code paths change |
| **B. Full namespace and folders** | A + `Webkul\` → `Systems365\` (or chosen name), `plugins/webkul` → `plugins/365systems`, every `composer.json`, plus a **data migration** of every stored class name | 4–6 days, then a frozen-branch merge | High: one missed stored class name breaks records silently (chatter history, custom fields, views, queued jobs); upstream patches no longer apply as patches |
| C. Morph map first, then B | Register `Relation::enforceMorphMap()` with short aliases (`'account.move'`), migrate stored types to aliases once, then rename freely later | +1 day before B | Lowest-risk route to B; future renames never touch data again |

## 4. Recommended path

1. **Now — Option A** (visible brand, licence notice fixed as in §2).
2. **Later, after Logistics merges and the restructure branch lands** —
   C then B in one dedicated branch, when nothing else is in flight:
   - C1: add a morph map for every model stored polymorphically; migration
     rewrites `*_type` columns from class names to aliases; tests that old
     records still load.
   - B1: rename with a scripted, reviewed codemod (namespace + `use` lines +
     string class names), not by hand; regenerate autoload; move folders.
   - B2: drain queues before deploy (job payloads hold class names); clear
     caches (`optimize:clear`, Filament component cache, route cache).
   - B3: full suite on every testsuite, one per database; smoke in
     production-like data copy before release.
3. Upstream: after B, AureusERP fixes are ported by hand (already the rule:
   never merge upstream wholesale). Record this in
   `docs/upstream-fix-adoption-plan.md`.

## 5. Not doing

Rewriting git history, removing Webkul from commit authorship, or deleting
the original licence notice.

## 6. Decisions needed (user)

| ID | Question | Recommendation |
| --- | --- | --- |
| RB-D1 | Visible brand now, namespace later (A, then C+B)? | **Accepted (user, 2026-11-13)** |
| RB-D2 | Namespace name (must start with a letter) | **Accepted: `Systems365`** |
| RB-D3 | Licence notice: keep Webkul's line and add yours? | **Accepted**; done in `LICENSE` 2026-11-13; lawyer to confirm |
| RB-D4 | When to run B | **Accepted:** after the Logistics merge and the restructure branch, in a quiet week |
| RB-D5 | Names (user, 2026-11-13) | Company: **365 Systems** (not "365 Integrated Systems"). App: **DEKA Central** (was "DEKA ERP"). Applied to `LICENSE`, every plugin `composer.json` author, the root description, the web-app name and title, the in-app guide and the remaining translations. Production's `APP_NAME` is an environment variable: set it to `DEKA Central` in Laravel Cloud |
| RB-D6 | Domains (user, 2026-11-13) | **`dekaerp.com` and its subdomains (`cloud.dekaerp.com`, …) stay as they are for now; they change later.** Do not rename URLs, mail addresses, `APP_URL` or links to them in this sweep |
