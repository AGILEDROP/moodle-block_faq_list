# Changelog

## Version 2.0.0 (2026111200)

- Support Moodle 5.0–5.3 and update the accordion and carousel for Bootstrap 5. Use buttons for carousel slide indicators.
- Expand the CI matrix to cover Moodle 5.0–5.3 with MariaDB and PostgreSQL.

This release includes the fixes listed under 1.2.1. The title translation upgrade note also applies when upgrading directly to 2.0.0.

## Version 1.2.1 — Moodle 4.0–4.5

- Render blocks without a selected FAQ list without PHP warnings.
- Give each block instance its own HTML IDs so multiple blocks can display the same FAQ list on one page.
- Apply Moodle formatting to FAQ titles, questions, and answers.
- Give carousel card titles consistent spacing.
- Keep FAQ items in a consistent order and use locked transactions for reordering and updates.
- Give each move-up and move-down form a unique identifier so submissions target the correct item and direction.
- Preserve the selected list and language when editing FAQ items and handle records deleted while a form is open.
- Show FAQ management pages and actions according to the user's permissions.
- Enforce one title translation per FAQ list and language.
- Correct the FAQ item examples in all three Mustache templates and run Mustache lint in CI.
- Add PHPUnit coverage for ordering, duplicate title translations, and updates to missing records. Fix transaction rollback conflicts in the PostgreSQL tests.
- Test Moodle 4.0–4.5 on MariaDB and PostgreSQL for the Moodle 4 release.

### Upgrade note

Each FAQ list can have one title translation per language. If an existing list has duplicate translations for a language, the upgrade keeps the record with the lowest ID and removes the others.
