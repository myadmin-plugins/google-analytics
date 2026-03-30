---
name: phpunit-plugin-test
description: Writes PHPUnit 9.6 tests in `tests/PluginTest.php` matching the existing ReflectionClass pattern: checks method existence, static/public modifiers, parameter names and types via `$reflection->getMethod()`. Use when user says 'add test', 'write test for', 'test this method', or adds methods to src/Plugin.php. Do NOT use for integration tests or tests outside the Plugin class.
---
# PHPUnit Plugin Test

## Critical

- All tests live in `tests/PluginTest.php` — never create separate test files for Plugin methods
- Namespace must be `Detain\MyAdminGoogle\Tests`; class must extend `PHPUnit\Framework\TestCase`
- Use tabs for indentation (`.scrutinizer.yml` enforces `use_tabs: true`)
- `@covers \Detain\MyAdminGoogle\Plugin` docblock is required on the class
- Never instantiate `GenericEvent` in tests — use `ReflectionClass` to inspect signatures without invoking side effects
- Run tests with: `vendor/bin/phpunit tests/ -v`

## Instructions

1. **Open both files before writing.** Read `src/Plugin.php` to confirm the method signature (param name, type hint, return type). Read `tests/PluginTest.php` to see existing tests and the current `testPublicMethodCount` assertion count.

2. **For each new `public static` hook method** (accepts `GenericEvent $event`), add one test method named `test{MethodName}MethodExists`. Pattern:
   ```php
   public function testGetFooMethodExists(): void
   {
       $reflection = new ReflectionClass(Plugin::class);
       $this->assertTrue($reflection->hasMethod('getFoo'));

       $method = $reflection->getMethod('getFoo');
       $this->assertTrue($method->isStatic());
       $this->assertTrue($method->isPublic());

       $params = $method->getParameters();
       $this->assertCount(1, $params);
       $this->assertSame('event', $params[0]->getName());

       $paramType = $params[0]->getType();
       $this->assertNotNull($paramType);
       $this->assertSame('Symfony\\Component\\EventDispatcher\\GenericEvent', $paramType->getName());
   }
   ```
   Verify: the method name in the test matches exactly the method name in `src/Plugin.php`.

3. **If the method is `getHooks`** (returns `array`, no `GenericEvent` param), use this pattern instead:
   ```php
   public function testGetHooksReturnsArray(): void
   {
       $hooks = Plugin::getHooks();
       $this->assertIsArray($hooks);
   }
   ```
   Add a second test for the expected contents (empty array or specific key/value pairs).

4. **For new static string properties** (`$name`, `$description`, `$help`, `$type`), add:
   ```php
   public function testFooProperty(): void
   {
       $this->assertSame('Expected Value', Plugin::$foo);
   }
   ```
   Also update `testClassHasExpectedStaticProperties` — increment `assertCount(N, $staticProperties)` to match the new total.

5. **Update `testPublicMethodCount`** whenever a method is added or removed. Count only methods declared in `Plugin` (not inherited): `__construct`, `getHooks`, plus all hook methods. Update the inline comment and `assertCount(N, $ownMethods)`.

6. **Verify** by running:
```bash
vendor/bin/phpunit tests/ -v
```
All tests must pass before finishing.

## Examples

**User says:** "Add a `getNavigation(GenericEvent $event)` method to Plugin."

**Actions taken:**
1. Read `src/Plugin.php` — confirm `getNavigation(GenericEvent $event)` exists and is `public static`
2. Read `tests/PluginTest.php` — note current `assertCount(5, $ownMethods)` in `testPublicMethodCount`
3. Add to `tests/PluginTest.php`:
   ```php
   /**
    * Test that the getNavigation method exists and accepts a GenericEvent parameter.
    *
    * @return void
    */
   public function testGetNavigationMethodExists(): void
   {
       $reflection = new ReflectionClass(Plugin::class);
       $this->assertTrue($reflection->hasMethod('getNavigation'));

       $method = $reflection->getMethod('getNavigation');
       $this->assertTrue($method->isStatic());
       $this->assertTrue($method->isPublic());

       $params = $method->getParameters();
       $this->assertCount(1, $params);
       $this->assertSame('event', $params[0]->getName());

       $paramType = $params[0]->getType();
       $this->assertNotNull($paramType);
       $this->assertSame('Symfony\\Component\\EventDispatcher\\GenericEvent', $paramType->getName());
   }
   ```
4. Update `testPublicMethodCount`: change `assertCount(5, $ownMethods)` → `assertCount(6, $ownMethods)` and update the comment
5. Run `vendor/bin/phpunit tests/ -v` — confirm green

**Result:** One new test method added, count assertion updated, all tests pass.

## Common Issues

- **`ReflectionException: Method Plugin::getFoo does not exist`** — method name in test doesn't match `src/Plugin.php`. Check exact casing: `getMenu` not `GetMenu`.
- **`Failed asserting that 6 matches expected 5` in `testPublicMethodCount`** — forgot to update `assertCount` after adding a method. Count `__construct` + `getHooks` + all hook methods declared in Plugin.
- **`Failed asserting that 5 matches expected 4` in `testClassHasExpectedStaticProperties`** — added a static property but didn't update `assertCount` in `testClassHasExpectedStaticProperties`.
- **`TypeError: $paramType->getName()` on null** — method has no type hint in `src/Plugin.php`. Add `GenericEvent` type hint to the method signature first, or assert `assertNull($paramType)` if intentionally untyped.
- **Tests not found** — bootstrap path wrong. Always run from repo root: `vendor/bin/phpunit tests/ -v`.
