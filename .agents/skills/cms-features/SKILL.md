---
name: cms-features
description: "Apply this skill whenever writing, reviewing, or refactoring any CMS module code — page builder sections (schemas, templates, blade views), the registry, or related Filament resources."
license: Proprietary
metadata:
  author: Michal Škoula
---

# CMS Features

This skill is a dispatcher. Read the module doc that matches the task before editing anything:

- **Page Builder** (sections, schemas, templates, registry) → `modules/page-builder.md`

If the task spans multiple modules, read each relevant file. Do not touch `app/Contracts/` or `app/Concerns/` — those are the CMS's internal surface and agents should not modify them.
