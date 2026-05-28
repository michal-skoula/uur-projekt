# Page Builder Workflow

Read this file before opening a pull request or when hitting a blocker.

## Branch naming

```
section/{slug}
```

Examples: `section/hero`, `section/news-feature`

## Verification checklist

Run all three before pushing. Non-negotiable:

```bash
vendor/bin/pint --dirty --format agent
composer analyze
php artisan test --compact
```

All must pass. Do not push with failures.

## Pull request format

Every PR must follow this format:

```markdown
## What this section does
[One paragraph explaining the section's purpose and where it's used]

## Schema fields
[List of admin-facing fields with their types and purpose]

## Frontend behavior
[What the visitor sees, responsive behavior, any interactivity]

## Screenshots
[Desktop and mobile screenshots if available]
```

If the Puppeteer MCP server is available, capture screenshots of the section at desktop and mobile viewports and attach them. If unavailable, note "Screenshots: not available — no browser access in this session" and move on.

## When blocked

If a task requires touching off-limits files, making ugly workarounds, or fighting the system — **stop**.

1. Push the feature branch to remote.
2. Open a draft PR with a clear description of the blocker.
3. Label the PR `blocked`.
4. Explain what was tried, what failed, and what needs to change in the CMS internals.

A human will review and either unblock or adjust the internals. Do not hack around it.

## Context

This is a new CMS being built incrementally. The page builder internals (contracts, helpers, rendering pipeline, scaffolding command) are still being refined by a human. Sections are the stable, well-defined surface — they plug into a system designed for them.

The tight constraints exist because:
- Internals aren't battle-tested yet. An agent modifying the rendering pipeline could introduce subtle bugs that break every section.
- Sections are self-contained — a broken section only breaks that section. Low blast radius.
- The agentic workflow itself is being validated incrementally.

If a rule feels wrong for a specific situation, explain why in the PR or blocker. Constraints will evolve as the system matures.
