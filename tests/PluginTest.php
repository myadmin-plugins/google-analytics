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
     * Test that getHooks returns an array.
     *
     * @return void
     */
    public function testGetHooksReturnsArray(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertIsArray($hooks);
    }

    /**
     * Test that getHooks returns an empty array (all hooks are commented out).
     *
     * @return void
     */
    public function testGetHooksReturnsEmptyArray(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertEmpty($hooks);
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
     * Test that getHooks return value contains only valid hook formats when non-empty.
     *
     * Hooks should be string keys mapping to callable arrays.
     *
     * @return void
     */
    public function testGetHooksReturnFormat(): void
    {
        $hooks = Plugin::getHooks();
        foreach ($hooks as $key => $value) {
            $this->assertIsString($key, 'Hook keys must be strings');
            $this->assertIsArray($value, 'Hook values must be arrays');
            $this->assertCount(2, $value, 'Hook callable arrays must have exactly 2 elements');
        }
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
