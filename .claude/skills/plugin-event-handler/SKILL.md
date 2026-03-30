---
name: plugin-event-handler
description: Adds a new static event-handler method to src/Plugin.php and registers it in getHooks(). Use when the user says 'add hook', 'new event handler', 'register event', or wants to add a method to Plugin.php. Do NOT use for modifying existing handler logic or editing non-Plugin.php files.
---
# plugin-event-handler

## Critical

- All handler methods MUST be `public static` — never instance methods.
- The single parameter MUST be typed `GenericEvent $event` (not untyped).
- `getHooks()` keys use dot-notation event names (e.g. `'ui.menu'`, `'system.settings'`, `'plugin.requirements'`).
- `getHooks()` values MUST be `[__CLASS__, 'methodName']` — not `[Plugin::class, ...]` or a string callable.
- Indentation is **tabs**, not spaces (enforced by `.scrutinizer.yml` `use_tabs: true`).
- After editing `src/Plugin.php`, the test `testPublicMethodCount` in `tests/PluginTest.php` asserts exactly 5 public methods. Update that count if you add a method.

## Instructions

1. **Identify the event name and method name** the user wants to handle (e.g. event `'analytics.track'` → method `getTrack`).

2. **Open `src/Plugin.php`** and add the new `public static` method before the closing `}` of the class:

```php
	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getTrack(GenericEvent $event)
	{
		$subject = $event->getSubject();
		// handler logic here
	}
```

   - Retrieve the event payload with `$event->getSubject()` and assign it to a descriptively named variable (`$menu`, `$loader`, `$settings`, `$subject`).
   - Verify: method is `public static`, accepts exactly one `GenericEvent $event` parameter, uses tab indentation.

3. **Register the hook in `getHooks()`** — add the new entry to the returned array:

```php
public static function getHooks()
{
	return [
		'analytics.track' => [__CLASS__, 'getTrack'],
	];
}
```

   - Key: dot-notation event string. Value: `[__CLASS__, 'methodName']` (two-element array).
   - Verify: the array key is a string, the value has exactly 2 elements, second element matches the method name added in Step 2.

4. **Update the method-count assertion** in `tests/PluginTest.php`:
   - Find `testPublicMethodCount` → `$this->assertCount(5, $ownMethods)` and increment by 1 for each method added.

5. **Run tests** to confirm nothing is broken:
```bash
vendor/bin/phpunit tests/ -v
```
   All tests must pass before the work is done.

## Examples

**User says:** "Add a hook for the `analytics.pageview` event."

**Actions taken:**
1. Add method to `src/Plugin.php`:
```php
	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getPageview(GenericEvent $event)
	{
		$subject = $event->getSubject();
	}
```
2. Register in `getHooks()`:
```php
	return [
		'analytics.pageview' => [__CLASS__, 'getPageview'],
	];
```
3. Update `tests/PluginTest.php` line asserting method count from `5` to `6`.
4. Run `vendor/bin/phpunit tests/ -v` → all green.

**Result:** `src/Plugin.php` now has `getPageview` wired to `analytics.pageview`, tests pass.

## Common Issues

- **`testPublicMethodCount` fails with `Failed asserting that 6 matches expected 5`**: You added a method but forgot to update the `assertCount` in `tests/PluginTest.php:303`. Increment the count by the number of methods added.
- **`testGetHooksReturnFormat` fails**: Hook value is not a 2-element array, or the key is not a string. Ensure format is `'event.name' => [__CLASS__, 'methodName']`.
- **`Call to undefined method` at runtime**: The method name in `getHooks()` does not exactly match the defined method name — PHP method names are case-insensitive but typos cause fatal errors. Double-check spelling.
- **CS errors from `.scrutinizer.yml`**: Space-indented code will fail the `use_tabs: true` check. Run a tab-conversion on your editor, or use `make php-cs-fixer` if available in the parent project.
- **`getSubject()` returns unexpected type**: Each event contract passes a different subject type (`$menu` array, `$loader` Loader object, `$settings` Settings object). Check existing handlers (`getMenu`, `getRequirements`, `getSettings`) to understand what a given event passes as its subject.
