<?php

namespace Detain\MyAdminGoogle\Tests;

use Detain\MyAdminGoogle\Plugin;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Tests for the Plugin class.
 *
 * @covers \Detain\MyAdminGoogle\Plugin
 */
class PluginTest extends TestCase
{
    /**
     * Test that the Plugin class can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated(): void
    {
        $plugin = new Plugin();
        $this->assertInstanceOf(Plugin::class, $plugin);
    }

    /**
     * Test that the $name static property is set correctly.
     *
     * @return void
     */
    public function testNameProperty(): void
    {
        $this->assertSame('Google Plugin', Plugin::$name);
    }

    /**
     * Test that the $description static property is set correctly.
     *
     * @return void
     */
    public function testDescriptionProperty(): void
    {
        $this->assertSame('Allows handling of Google based Analytics', Plugin::$description);
    }

    /**
     * Test that the $help static property is an empty string.
     *
     * @return void
     */
    public function testHelpProperty(): void
    {
        $this->assertSame('', Plugin::$help);
    }

    /**
     * Test that the $type static property is set to 'plugin'.
     *
     * @return void
     */
    public function testTypeProperty(): void
    {
        $this->assertSame('plugin', Plugin::$type);
    }

    /**
     * Test that every hook registration in getHooks() names a real handler.
     *
     * Every registration in this plugin's getHooks() is currently commented out,
     * so nothing it declares reaches the event dispatcher. Asserting that the
     * returned array is empty would only bless that state; what is worth
     * asserting is that the registrations written in the method body - live or
     * commented - still name public static handlers on this class, so a rename
     * fails here instead of silently breaking whoever re-enables the lines.
     *
     * @return void
     */
    public function testGetHooksRegistrationsNameLiveHandlers(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $method = $reflection->getMethod('getHooks');
        $lines = file((string) $method->getFileName());
        $this->assertNotFalse($lines, 'Plugin source should be readable');
        $body = implode('', array_slice(
            $lines,
            $method->getStartLine() - 1,
            $method->getEndLine() - $method->getStartLine() + 1
        ));

        preg_match_all(
            "/'([^']+)'\s*=>\s*\[\s*(?:__CLASS__|self::class|static::class|[A-Za-z_\\\\]+::class)\s*,\s*'([^']+)'\s*\]/",
            $body,
            $registrations,
            PREG_SET_ORDER
        );

        $this->assertNotEmpty(
            $registrations,
            'getHooks() should declare at least one "event.name" => [__CLASS__, "method"] registration'
        );

        foreach ($registrations as $registration) {
            [, $eventName, $handlerName] = $registration;

            $this->assertNotSame('', trim($eventName), 'Hook event names must not be empty');
            $this->assertTrue(
                $reflection->hasMethod($handlerName),
                "getHooks() registers '{$eventName}' => {$handlerName}() but that method does not exist on ".Plugin::class
            );

            $handler = $reflection->getMethod($handlerName);
            $this->assertTrue($handler->isPublic(), "Handler {$handlerName}() for '{$eventName}' must be public");
            $this->assertTrue($handler->isStatic(), "Handler {$handlerName}() for '{$eventName}' must be static");
        }
    }

    /**
     * Test that the getMenu method exists and accepts a GenericEvent parameter.
     *
     * @return void
     */
    public function testGetMenuMethodExists(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $this->assertTrue($reflection->hasMethod('getMenu'));

        $method = $reflection->getMethod('getMenu');
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());

        $params = $method->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('event', $params[0]->getName());

        $paramType = $params[0]->getType();
        $this->assertNotNull($paramType);
        $this->assertSame('Symfony\Component\EventDispatcher\GenericEvent', $paramType->getName());
    }

    /**
     * Test that the getRequirements method exists and accepts a GenericEvent parameter.
     *
     * @return void
     */
    public function testGetRequirementsMethodExists(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $this->assertTrue($reflection->hasMethod('getRequirements'));

        $method = $reflection->getMethod('getRequirements');
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());

        $params = $method->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('event', $params[0]->getName());

        $paramType = $params[0]->getType();
        $this->assertNotNull($paramType);
        $this->assertSame('Symfony\Component\EventDispatcher\GenericEvent', $paramType->getName());
    }

    /**
     * Test that the getSettings method exists and accepts a GenericEvent parameter.
     *
     * @return void
     */
    public function testGetSettingsMethodExists(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $this->assertTrue($reflection->hasMethod('getSettings'));

        $method = $reflection->getMethod('getSettings');
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());

        $params = $method->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('event', $params[0]->getName());

        $paramType = $params[0]->getType();
        $this->assertNotNull($paramType);
        $this->assertSame('Symfony\Component\EventDispatcher\GenericEvent', $paramType->getName());
    }

    /**
     * Test that all static properties are of type string.
     *
     * @return void
     */
    public function testStaticPropertiesAreStrings(): void
    {
        $this->assertIsString(Plugin::$name);
        $this->assertIsString(Plugin::$description);
        $this->assertIsString(Plugin::$help);
        $this->assertIsString(Plugin::$type);
    }

    /**
     * Test the class has exactly the expected static properties.
     *
     * @return void
     */
    public function testClassHasExpectedStaticProperties(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $staticProperties = $reflection->getStaticProperties();

        $this->assertArrayHasKey('name', $staticProperties);
        $this->assertArrayHasKey('description', $staticProperties);
        $this->assertArrayHasKey('help', $staticProperties);
        $this->assertArrayHasKey('type', $staticProperties);
        $this->assertCount(4, $staticProperties);
    }

    /**
     * Test that the class belongs to the correct namespace.
     *
     * @return void
     */
    public function testClassNamespace(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $this->assertSame('Detain\MyAdminGoogle', $reflection->getNamespaceName());
    }

    /**
     * Test that the constructor has no required parameters.
     *
     * @return void
     */
    public function testConstructorHasNoRequiredParameters(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor);
        $this->assertCount(0, $constructor->getParameters());
    }

    /**
     * Test that multiple instances can be created independently.
     *
     * @return void
     */
    public function testMultipleInstances(): void
    {
        $plugin1 = new Plugin();
        $plugin2 = new Plugin();

        $this->assertNotSame($plugin1, $plugin2);
        $this->assertInstanceOf(Plugin::class, $plugin1);
        $this->assertInstanceOf(Plugin::class, $plugin2);
    }

    /**
     * Test that every hook getHooks() returns is actually dispatchable.
     *
     * The host copies each entry straight into a Symfony EventDispatcher
     * listener, so an entry only does something if its event name is a non-empty
     * string and its handler resolves to a public static method that PHP will
     * accept as a callable. The closing assertion proves the loop covered every
     * entry, so this test always asserts something even while the plugin's
     * registrations are commented out.
     *
     * @return void
     */
    public function testGetHooksAreDispatchableCallables(): void
    {
        $hooks = Plugin::getHooks();
        $validated = [];

        foreach ($hooks as $eventName => $handler) {
            $this->assertIsString($eventName, 'Hook event names must be strings');
            $this->assertNotSame('', trim($eventName), 'Hook event names must not be empty');

            $this->assertIsArray($handler, "Handler for '{$eventName}' must be a [class, method] pair");
            $this->assertArrayHasKey(0, $handler, "Handler for '{$eventName}' is missing its class");
            $this->assertArrayHasKey(1, $handler, "Handler for '{$eventName}' is missing its method");
            $this->assertTrue(class_exists($handler[0]), "Handler class {$handler[0]} for '{$eventName}' does not exist");
            $this->assertTrue(
                method_exists($handler[0], $handler[1]),
                "Handler {$handler[0]}::{$handler[1]}() for '{$eventName}' does not exist"
            );

            $method = new \ReflectionMethod($handler[0], $handler[1]);
            $this->assertTrue($method->isPublic(), "Handler {$handler[1]}() for '{$eventName}' must be public");
            $this->assertTrue($method->isStatic(), "Handler {$handler[1]}() for '{$eventName}' must be static");
            $this->assertTrue(is_callable($handler), "Handler for '{$eventName}' must be callable as given");

            $validated[] = $eventName;
        }

        $this->assertSame(array_keys($hooks), $validated, 'Every hook returned by getHooks() must be validated');
    }

    /**
     * Test that the class is not abstract.
     *
     * @return void
     */
    public function testClassIsNotAbstract(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $this->assertFalse($reflection->isAbstract());
    }

    /**
     * Test that the class is not final.
     *
     * @return void
     */
    public function testClassIsNotFinal(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $this->assertFalse($reflection->isFinal());
    }

    /**
     * Test that the Plugin class does not extend any parent class.
     *
     * @return void
     */
    public function testClassHasNoParent(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $this->assertFalse($reflection->getParentClass());
    }

    /**
     * Test that the Plugin class does not implement any interfaces.
     *
     * @return void
     */
    public function testClassImplementsNoInterfaces(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $this->assertEmpty($reflection->getInterfaceNames());
    }

    /**
     * Test the total number of public methods on the class.
     *
     * @return void
     */
    public function testPublicMethodCount(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $publicMethods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        // Filter to only methods declared in Plugin (not inherited)
        $ownMethods = array_filter($publicMethods, function ($method) {
            return $method->getDeclaringClass()->getName() === Plugin::class;
        });

        // __construct, getHooks, getMenu, getRequirements, getSettings
        $this->assertCount(5, $ownMethods);
    }
}
