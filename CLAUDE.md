# MyAdmin Google Analytics Plugin

Composer plugin package for the MyAdmin control panel framework. Provides Google Analytics integration via the MyAdmin event system.

## Commands

```bash
composer install
vendor/bin/phpunit tests/ -v
vendor/bin/phpunit tests/ -v --coverage-clover coverage.xml --whitelist src/
```

```bash
# Generate HTML coverage report
vendor/bin/phpunit tests/ -v --coverage-html coverage/
```

## Architecture

- **Plugin class**: `src/Plugin.php` · namespace `Detain\MyAdminGoogle\` · all methods static
- **Tests**: `tests/PluginTest.php` · namespace `Detain\MyAdminGoogle\Tests\` · PHPUnit 9.6
- **Event system**: uses `Symfony\Component\EventDispatcher\GenericEvent` — `$event->getSubject()` retrieves the payload
- **Autoload**: PSR-4 `Detain\MyAdminGoogle\` → `src/` · dev `Detain\MyAdminGoogle\Tests\` → `tests/`
- **CI**: `.scrutinizer.yml` · `.travis.yml` · `.codeclimate.yml` · `.bettercodehub.yml` · `.github/` (contains CI/CD workflows for automated testing and deployment pipelines)
- **IDE config**: `.idea/` (contains inspectionProfiles, deployment.xml, encodings.xml for JetBrains IDE project settings)

## Plugin Pattern

All hook methods in `src/Plugin.php` are `public static` and accept `GenericEvent $event`:

```php
public static function getHooks(): array {
    return ['hook.name' => [get_class(), 'methodName']];
}

public static function getMenu(GenericEvent $event): void {
    $menu = $event->getSubject();
    if ($GLOBALS['tf']->ima == 'admin') {
        function_requirements('has_acl');
        if (has_acl('client_billing')) { /* add menu items */ }
    }
}

public static function getRequirements(GenericEvent $event): void {
    $loader = $event->getSubject();
    $loader->add_requirement('class.ClassName', '/../vendor/path/to/src/ClassName.php');
}

public static function getSettings(GenericEvent $event): void {
    $settings = $event->getSubject();
    // register settings fields
}
```

## Conventions

- Static properties on `Plugin`: `$name`, `$description`, `$help`, `$type = 'plugin'`
- Commit messages: lowercase, descriptive (`fix hook registration`, `add analytics event`)
- Coding style: tabs for indentation (see `.scrutinizer.yml` `use_tabs: true`)
- `getHooks()` returns `['event.name' => [ClassName::class, 'methodName']]` or empty array
- Requirements use path relative to vendor root

<!-- caliber:managed:pre-commit -->
## Before Committing

**IMPORTANT:** Before every git commit, you MUST ensure Caliber syncs agent configs with the latest code changes.

First, check if the pre-commit hook is already installed:
```bash
grep -q "caliber" .git/hooks/pre-commit 2>/dev/null && echo "hook-active" || echo "no-hook"
```

- If **hook-active**: the hook handles sync automatically — just commit normally. Tell the user: "Caliber will sync your agent configs automatically via the pre-commit hook."
- If **no-hook**: run Caliber manually before committing:
  1. Tell the user: "Caliber: Syncing agent configs with your latest changes..."
  2. Run: `caliber refresh && git add CLAUDE.md .claude/ .cursor/ .cursorrules .github/copilot-instructions.md .github/instructions/ AGENTS.md CALIBER_LEARNINGS.md 2>/dev/null`
  3. After it completes, briefly tell the user what Caliber updated. Then proceed with the commit.

If `caliber` is not found, tell the user: "This project uses Caliber for agent config sync. Run /setup-caliber to get set up."
<!-- /caliber:managed:pre-commit -->

<!-- caliber:managed:learnings -->
## Session Learnings

Read `CALIBER_LEARNINGS.md` for patterns and anti-patterns learned from previous sessions.
These are auto-extracted from real tool usage — treat them as project-specific rules.
<!-- /caliber:managed:learnings -->
