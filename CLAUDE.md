# CLAUDE.md — moosh2

## Project Overview

**moosh2** is a rewrite of [Moosh (Moodle Shell)](https://github.com/tmuras/moosh) using Symfony Console 7.x. It provides CLI commands for managing Moodle installations. Licensed under GNU GPL v3+.

- **PHP**: >= 8.2
- **Main dependency**: symfony/console ^7.0
- **Entry points**: `php moosh.php` or `php bin/moosh`

## Repository Structure

```
src/
├── Application.php              # Main Symfony Application, command registration
├── Attribute/
│   └── SinceVersion.php         # PHP attribute for Moodle version gating
├── Bootstrap/
│   ├── BootstrapLevel.php       # Enum: None, Config, Full, FullNoCli, DbOnly, FullNoAdminCheck
│   ├── MoodleBootstrapper.php   # Handles Moodle bootstrap lifecycle
│   ├── MoodlePathResolver.php   # Walks directory tree to find Moodle root
│   └── MoodleVersion.php        # Parses version.php, provides version comparison
├── Command/
│   ├── BaseCommand.php          # Abstract base — bootstraps Moodle then calls handle()
│   ├── BaseHandler.php          # Abstract base for version-specific handlers
│   └── Course/
│       ├── CourseListCommand.php     # course:list command
│       ├── CourseList51Handler.php   # Moodle 5.1 implementation
│       ├── CourseList52Handler.php   # Moodle 5.2 implementation
│       └── CourseListHelperTrait.php # Shared course query helpers
└── Output/
    └── ResultFormatter.php      # Renders table/CSV/JSON output
tests/
    └── test_course_list.sh      # Integration test (requires live Moodle + PostgreSQL)
```

## Common Commands

```bash
# Install dependencies
composer install

# Run the tool against a Moodle installation
php moosh.php course:list --moodle-path=/path/to/moodle

# Run integration tests (requires MOODLE_PATH pointing to a working Moodle)
MOODLE_PATH=/path/to/moodle bash tests/test_course_list.sh
```

There is no unit test suite or linter configured yet. No Makefile.

## Architecture & Conventions

### Command Pattern

Every command follows this structure:

1. **Command class** extends `BaseCommand` — sets name, description, bootstrap level
2. **Handler classes** extend `BaseHandler` — implement version-specific logic
3. Command delegates `configure()` and `handle()` to the appropriate handler based on detected Moodle version
4. `BaseCommand::execute()` handles Moodle bootstrapping before calling `handle()`

### Version-Specific Dispatch

- `Application` detects Moodle version early (constructor, before command registration)
- Commands select a handler based on `MoodleVersion::isAtLeast()`
- Handler naming: `{CommandName}{MajorMinor}Handler.php` (e.g., `CourseList52Handler.php`)

### Bootstrap Levels

Commands declare a `BootstrapLevel` enum value controlling how deeply Moodle is initialized:
- `None` — no Moodle includes
- `Config` — config.php only (ABORT_AFTER_CONFIG)
- `Full` — standard full bootstrap
- `FullNoCli` — browser context
- `DbOnly` — database only
- `FullNoAdminCheck` — full without admin check

Handlers can override the command's bootstrap level by implementing `getBootstrapLevel()` on `BaseHandler` (returns `?BootstrapLevel`, default `null`). When a handler returns a non-null value, it takes precedence over the command's `$bootstrapLevel` property. Commands must override `getActiveHandler()` on `BaseCommand` for this to work.

### Output Formatting

`ResultFormatter` supports three formats via `--output` / `-o`:
- `table` — ASCII table (default)
- `csv` — quoted CSV
- `json` — pretty-printed JSON

### Global CLI Options

- `--moodle-path` / `-p` — path to Moodle directory
- `--user` / `-u` — Moodle user (default: admin)
- `--no-login` / `-l` — skip login
- `--no-user-check` — skip data ownership check
- `--performance` / `-t` — show timing info
- `--output` / `-o` — output format

## Coding Style

- PHP 8.2+ features: enums, readonly properties, named arguments, match expressions, attributes
- Type hints on all parameters and return types
- One class per file, PSR-4 autoloading (`Moosh2\` namespace)
- PascalCase classes, camelCase methods, colon-separated command names (`course:list`)
- Command names use **singular nouns**: `course:list`, `user:info`, `plugin:usage` (not `courses:list` or `plugins:usage`)
- PHPDoc copyright/license headers on all files
- No dev tooling (phpunit, phpcs) configured yet — keep changes manually consistent

## Adding a New Command

1. Create a directory under `src/Command/` for the command group (e.g., `User/`)
2. Create `{Name}Command.php` extending `BaseCommand` — set bootstrap level, name, description
3. Create version-specific handlers `{Name}{Version}Handler.php` extending `BaseHandler`
4. Optionally create a helper trait for shared logic
5. Register the command in `Application::registerCommands()`
6. Add integration tests in `tests/`

## Testing

Integration tests can be run using local Moodle instance and running test_course_list.sh script.
Always run test_course_list.sh after doing a change, to make sure there are no regressions.


## Bash Command Style

Never chain commands with && or ; operators. Run them as separate bash calls instead.

