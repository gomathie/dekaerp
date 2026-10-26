# Harness² — assessment and what we adopted

**Decision:** documentation and a repository-owned evaluation process adopted
2026-10-26; the framework itself is **not installed**.

[Harness²](https://github.com/google-research/harness2) is Google Research's
framework for *recursive agent harnessing*: it optimises the system around a
model (prompts, tools, memory, skills, orchestration) while the model weights
stay fixed. It is Apache-2.0, Python, released 2026-10-06.

Everyone who reads this should know up front: it cannot be wired into this
repository or into the agent that works on it. The rest of this document records
why, with evidence, and what was adopted instead.

## What it actually is — verified 2026-10-26

Read from the `main` branch on 2026-10-26, not from its marketing.

| Property | Value | Source |
|---|---|---|
| Platform | **POSIX only** — `Operating System :: POSIX`, no Windows classifier | `pyproject.toml` |
| Python | `requires-python = ">=3.12,<3.14"` | `pyproject.toml` |
| Executors | **OpenCode and Codex only** — `SUBSTRATES = ("oc", "cx")` | `harness2/cli/_common.py` |
| Benchmarks | **JobBench, LAB, WorkBuddy-Bench only** — `BENCHES = ("jb", "wb", "lab")`, enforced by `--bench` `choices=BENCHES` | `harness2/cli/_common.py` |
| Custom repository support | **none** — no argument, adapter hook, or config path accepts a task suite other than the three above | `harness2/cli/_common.py`, `harness2/core/spec.py` |
| Model access | Vertex AI (Gemini, or Claude as improver) via Application Default Credentials; `gcloud auth application-default login`. Codex additionally through `harness2-bridge`, a local LiteLLM shim | `docs/SETUP.md` |
| Credential-free entry point | `harness2-demo --output runs/demo --k 2` — both recursion modes, deterministic editors, real Python executions | `README.md` |
| System prerequisites for benchmarks | Node 20+, `uv`, Google Cloud CLI, Docker; LAB also needs Pandoc and Poppler | `docs/SETUP.md` |

Two consequences matter more than the rest:

1. **`--bench` is a closed set.** Harness² measures a harness on its own
   benchmarks. There is no `--repo`, no "point it at my application", and the
   tasks are Google's, not ours. It cannot be told to run `LogisticsFeature`.
2. **`--substrate` is a closed set of two.** An executor is the thing that
   actually does the work. Ours is the VS Code Copilot agent; neither OpenCode
   nor Codex is installed here, and no adapter exists to register another one.

## Compatibility with DEKA ERP

| Requirement | This machine / repo | Verdict |
|---|---|---|
| Linux | Windows host; Sail is Linux but PHP-only | **missing** |
| Python 3.12 | none on host (`python` not installed) | **missing** |
| `uv` | absent | **missing** |
| Node 20+ | v22.15.0 | ✅ |
| Docker Engine | 29.8.1 (Desktop) | ✅ |
| Google Cloud CLI + Vertex credentials | absent; an agent must not create or copy these | **blocked** |
| OpenCode or Codex executor | not installed; this agent is VS Code Copilot | **no adapter** |
| A supported benchmark that exercises this repo | none exists | **not supported** |

**Verdict: direct integration is not possible.** Installing it would yield a
benchmark lab for JobBench/LAB/WorkBuddy-Bench — a real thing, but not an
improvement to how this repository is developed, and it would bring a second
LLM stack, a GCP project, and a Python toolchain into a PHP shop for no measured
gain in return.

## What was adopted instead

Three layers, in order of value to this repository.

### 1. Repository-owned evaluation process (adopted — this is the valuable part)

The point of Harness² is that harness changes should be judged by measured
results rather than by opinion. That part transfers directly, and needs no
Google tooling: the project already has sixteen Pest suites (`phpunit.xml`), and
a change to an agent instruction can be judged against them.

Eight scenarios, each tied to a suite that already exists. Run the command, and
the pass/fail counts are the measurement:

| # | Scenario | Command | What a pass proves |
|---|---|---|---|
| 1 | Fixing an existing bug | `php artisan test plugins/webkul/accounts/tests/Feature/Workflows/InvoiceTest.php` | Payment-state and totals maths still hold |
| 2 | Reusing existing components | `LogisticsFeature` → `Sales/ShipmentFromOrderTest.php` | The sale-to-shipment path reuses `Sale` rather than a parallel copy |
| 3 | Avoiding duplicate code | `SupportFeature` → `Structure/ResourceStructureSnapshotTest.php` | Every resource still declares the same form/infolist/table shape and field order, rather than a hand-rolled variant |
| 4 | Correcting API contract failures | `php artisan test plugins/webkul/accounts/tests/Feature/API/V1/InvoiceTest.php` | API resources and status codes are unchanged |
| 5 | Preventing database regressions | `SupportFeature` → `Plugins/MigrationRegistrationTest.php` | Every migration is registered in its plugin's `hasMigrations()` |
| 6 | Preserving multi-tenant isolation | `SecurityFeature` → `Authorization/CompanyUserIsolationTest.php` | One company's user cannot reach another company's records |
| 7 | Improving maintainability | `ChatterFeature` + `vendor/bin/pint --dirty --format agent` | Behaviour unchanged and style enforced |
| 8 | Finding the root cause first | `LogisticsFeature` → `Documents/WaybillTest.php` | Regressions are pinned by the test that would have caught them |

A baseline is whatever those commands return today. Record it before changing
any instruction, and again afterwards, on the same database:

```bash
docker compose up -d pgsql
docker compose exec -T pgsql psql -U sail -d postgres \
  -c "CREATE DATABASE aureuserp_testing_eval OWNER sail;"

docker compose run --rm --no-deps -e DB_DATABASE=aureuserp_testing_eval \
  laravel.test php artisan test --testsuite=SupportFeature --compact
```

One suite at a time, one database, as [running-tests.md](running-tests.md)
requires. See that document for why two `migrate:fresh` runs on one database
corrupt each other's results.

### 2. Isolated sandbox (documented, not installed)

If you ever do want to try the framework, this keeps it entirely out of the
project: a throwaway container, a directory **outside** the repository, and no
change to `composer.json`, `package.json`, or anything under `vendor/`. Nothing
below touches application code, and deleting the directory undoes all of it.

```bash
# A directory outside the repo. Never mount the repo into this container.
mkdir -p ~/harness2-sandbox

docker run --rm -it -v ~/harness2-sandbox:/work -w /work python:3.12-slim bash

# inside the container
apt-get update
apt-get install -y git curl unzip ca-certificates
curl -LsSf https://astral.sh/uv/install.sh | sh
git clone https://github.com/google-research/harness2.git
cd harness2
uv venv --python 3.12 .venv && . .venv/bin/activate
uv pip install -e '.[render]'
harness2-demo --output runs/demo --k 2      # needs no credentials
```

`harness2-demo` is the only credential-free run. Everything else needs Node 20+,
the Google Cloud CLI, a Vertex AI project, and `gcloud auth application-default
login` performed by **you**, in that sandbox. Credentials for a model provider
are not something an agent should obtain, store, or pass around.

### 3. Controlled hand-off of lessons

A lesson learned while evaluating anything — Harness², a review, or a failed
run — reaches [AGENTS.md](../AGENTS.md) only through an approval gate:

1. Record the original problem.
2. Record what was attempted.
3. Record the test results and any regression observed.
4. State the candidate lesson, and the evidence that supports it.
5. Compare with the recorded baseline.
6. Propose the exact instruction or skill change.
7. **You approve it.** No agent edits global instructions, security policy, or
   deployment settings on its own, and no unverified assumption is stored as a
   permanent lesson.

This is the same discipline the project already applies per session, in
[change-log.md](change-log.md) and [agent-reminders.md](agent-reminders.md).

## What this document does not claim

- That Harness² can improve this repository's agent. It cannot be pointed at it.
- That any of its published gains (WorkBuddy-Bench +11.5, JobBench +4.5, LAB
  +11.8pp, on GLM-5 and Gemini 3.5 Flash) transfer to Copilot or to Laravel
  work. Those numbers are from its own benchmarks and its own models.
- That the scenarios above are a Harness² evaluation. They are this project's
  regression measurement, chosen so that an instruction change has something
  concrete to fail against.

## Rollback

This change is documentation only.

- Delete this file and the REFERENCES line in [AGENTS.md](../AGENTS.md).
- Nothing else was added: no dependency, no container, no config, no migration,
  no test.
- If the sandbox route is ever used, `rm -rf ~/harness2-sandbox` removes the
  clone, the virtualenv and any run output together.

## Reusing this across your other repositories

Keep five things separate, and copy them per repository rather than sharing by
reference:

| Layer | Where it lives | Reusable as-is |
|---|---|---|
| Working method (inspect first, root cause, reuse, verify) | each repo's `AGENTS.md` | yes, wording |
| PHP/Laravel standards | each repo's `AGENTS.md` + `pint.json` | yes if the stack matches |
| Database and tenant-isolation policy | each repo's `AGENTS.md` | adapt per tenancy model |
| Project architecture and its own quirks | that repo's project memory and `docs/` | never portable |
| Evaluation scenarios, baselines and results | that repo's `docs/harness2-integration.md` | re-derive per test layout |

Runtime dependencies, tests and deployment configuration always stay with the
project they belong to. No application code is copied between repositories.

## References

- Harness² repository: <https://github.com/google-research/harness2>
- Its setup guide: `docs/SETUP.md` in that repository
- Its benchmark guides: `docs/BENCHMARKS.md` in that repository
- This project's test procedure: [running-tests.md](running-tests.md)
