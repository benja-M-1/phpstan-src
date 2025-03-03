<?php declare(strict_types = 1);

namespace PHPStan\Rules\Properties;

use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Testing\RuleTestCase;
use const PHP_VERSION_ID;

/**
 * @extends RuleTestCase<TypesAssignedToPropertiesRule>
 */
class TypesAssignedToPropertiesRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new TypesAssignedToPropertiesRule(new RuleLevelHelper($this->createReflectionProvider(), true, false, true, false), new PropertyDescriptor(), new PropertyReflectionFinder());
	}

	public function testTypesAssignedToProperties(): void
	{
		$this->analyse([__DIR__ . '/data/properties-assigned-types.php'], [
			[
				'Property PropertiesAssignedTypes\Foo::$stringProperty (string) does not accept int.',
				29,
			],
			[
				'Property PropertiesAssignedTypes\Foo::$intProperty (int) does not accept string.',
				31,
			],
			[
				'Property PropertiesAssignedTypes\Foo::$fooProperty (PropertiesAssignedTypes\Foo) does not accept PropertiesAssignedTypes\Bar.',
				33,
			],
			[
				'Static property PropertiesAssignedTypes\Foo::$staticStringProperty (string) does not accept int.',
				35,
			],
			[
				'Static property PropertiesAssignedTypes\Foo::$staticStringProperty (string) does not accept int.',
				37,
			],
			[
				'Property PropertiesAssignedTypes\Ipsum::$parentStringProperty (string) does not accept int.',
				39,
			],
			[
				'Property PropertiesAssignedTypes\Foo::$unionPropertySelf (array<PropertiesAssignedTypes\Foo>|(iterable<PropertiesAssignedTypes\Foo>&PropertiesAssignedTypes\Collection)) does not accept PropertiesAssignedTypes\Foo.',
				44,
			],
			[
				'Property PropertiesAssignedTypes\Foo::$unionPropertySelf (array<PropertiesAssignedTypes\Foo>|(iterable<PropertiesAssignedTypes\Foo>&PropertiesAssignedTypes\Collection)) does not accept array<int, PropertiesAssignedTypes\Bar>.',
				45,
			],
			[
				'Property PropertiesAssignedTypes\Foo::$unionPropertySelf (array<PropertiesAssignedTypes\Foo>|(iterable<PropertiesAssignedTypes\Foo>&PropertiesAssignedTypes\Collection)) does not accept PropertiesAssignedTypes\Bar.',
				46,
			],
			[
				'Property PropertiesAssignedTypes\Ipsum::$parentStringProperty (string) does not accept int.',
				48,
			],
			[
				'Static property PropertiesAssignedTypes\Ipsum::$parentStaticStringProperty (string) does not accept int.',
				50,
			],
			[
				'Property PropertiesAssignedTypes\Foo::$intProperty (int) does not accept string.',
				60,
			],
			[
				'Property PropertiesAssignedTypes\Ipsum::$foo (PropertiesAssignedTypes\Ipsum) does not accept PropertiesAssignedTypes\Bar.',
				143,
			],
			[
				'Static property PropertiesAssignedTypes\Ipsum::$fooStatic (PropertiesAssignedTypes\Ipsum) does not accept PropertiesAssignedTypes\Bar.',
				144,
			],
			[
				'Property PropertiesAssignedTypes\AssignRefFoo::$stringProperty (string) does not accept int.',
				312,
			],
			[
				'Property PropertiesAssignedTypes\PostInc::$foo (int<min, 3>) does not accept int<min, 4>.',
				334,
			],
			[
				'Property PropertiesAssignedTypes\PostInc::$bar (int<3, max>) does not accept int<2, max>.',
				335,
			],
			[
				'Property PropertiesAssignedTypes\PostInc::$foo (int<min, 3>) does not accept int<min, 4>.',
				346,
			],
			[
				'Property PropertiesAssignedTypes\PostInc::$bar (int<3, max>) does not accept int<2, max>.',
				347,
			],
			[
				'Property PropertiesAssignedTypes\ListAssign::$foo (string) does not accept int.',
				360,
			],
			[
				'Property PropertiesAssignedTypes\AppendToArrayAccess::$collection2 (ArrayAccess<int, string>&Countable) does not accept Countable.',
				376,
			],
		]);
	}

	public function testBug1216(): void
	{
		$this->analyse([__DIR__ . '/data/bug-1216.php'], [
			[
				'Property Bug1216PropertyTest\Baz::$untypedBar (string) does not accept int.',
				35,
			],
			[
				'Property Bug1216PropertyTest\Dummy::$foo (Exception) does not accept stdClass.',
				59,
			],
		]);
	}

	public function testTypesAssignedToPropertiesExpressionNames(): void
	{
		$this->analyse([__DIR__ . '/data/properties-from-array-into-object.php'], [
			[
				'Property PropertiesFromArrayIntoObject\Foo::$lall (int) does not accept string.',
				42,
			],
			[
				'Property PropertiesFromArrayIntoObject\Foo::$lall (int) does not accept string.',
				54,
			],
			[
				'Property PropertiesFromArrayIntoObject\Foo::$test (int|null) does not accept stdClass.',
				66,
			],
			[
				'Property PropertiesFromArrayIntoObject\Foo::$float_test (float) does not accept float|int|string.',
				69,
			],
			[
				'Property PropertiesFromArrayIntoObject\Foo::$foo (string) does not accept float|int|string.',
				69,
			],
			[
				'Property PropertiesFromArrayIntoObject\Foo::$lall (int) does not accept float|int|string.',
				69,
			],
			[
				'Property PropertiesFromArrayIntoObject\Foo::$foo (string) does not accept (float|int).',
				73,
			],
			[
				'Property PropertiesFromArrayIntoObject\Foo::$foo (string) does not accept float.',
				83,
			],
			[
				'Property PropertiesFromArrayIntoObject\Foo::$foo (string) does not accept float|int|string.',
				97,
			],
			[
				'Property PropertiesFromArrayIntoObject\Foo::$lall (int) does not accept string.',
				110,
			],
			[
				'Property PropertiesFromArrayIntoObject\FooBar::$foo (string) does not accept float.',
				147,
			],
		]);
	}

	public function testTypesAssignedToStaticPropertiesExpressionNames(): void
	{
		$this->analyse([__DIR__ . '/data/properties-from-array-into-static-object.php'], [
			[
				'Static property PropertiesFromArrayIntoStaticObject\Foo::$lall (stdClass|null) does not accept string.',
				29,
			],
			[
				'Static property PropertiesFromArrayIntoStaticObject\Foo::$foo (string) does not accept float.',
				36,
			],
			[
				'Static property PropertiesFromArrayIntoStaticObject\FooBar::$foo (string) does not accept float.',
				72,
			],
		]);
	}

	public function testBug3777(): void
	{
		$this->analyse([__DIR__ . '/data/bug-3777.php'], [
			[
				'Property Bug3777\Bar::$foo (Bug3777\Foo<stdClass>) does not accept Bug3777\Fooo<object>.',
				58,
			],
			[
				'Property Bug3777\Ipsum::$ipsum (Bug3777\Lorem<stdClass, Exception>) does not accept Bug3777\Lorem<Exception, stdClass>.',
				95,
			],
			[
				'Property Bug3777\Ipsum2::$lorem2 (Bug3777\Lorem2<stdClass, Exception>) does not accept Bug3777\Lorem2<stdClass, object>.',
				129,
			],
			[
				'Property Bug3777\Ipsum2::$ipsum2 (Bug3777\Lorem2<stdClass, Exception>) does not accept Bug3777\Lorem2<Exception, object>.',
				131,
			],
			[
				'Property Bug3777\Ipsum3::$ipsum3 (Bug3777\Lorem3<stdClass, Exception>) does not accept Bug3777\Lorem3<Exception, stdClass>.',
				168,
			],
		]);
	}

	public function testAppendendArrayKey(): void
	{
		$this->analyse([__DIR__ . '/../Arrays/data/appended-array-key.php'], [
			[
				'Property AppendedArrayKey\Foo::$intArray (array<int, mixed>) does not accept array<int|string, mixed>.',
				27,
			],
			[
				'Property AppendedArrayKey\Foo::$intArray (array<int, mixed>) does not accept array<int|string, mixed>.',
				28,
			],
			[
				'Property AppendedArrayKey\Foo::$intArray (array<int, mixed>) does not accept array<int|string, mixed>.',
				30,
			],
			[
				'Property AppendedArrayKey\Foo::$stringArray (array<string, mixed>) does not accept array<int|string, mixed>.',
				31,
			],
			[
				'Property AppendedArrayKey\Foo::$stringArray (array<string, mixed>) does not accept array<int|string, mixed>.',
				33,
			],
			[
				'Property AppendedArrayKey\Foo::$stringArray (array<string, mixed>) does not accept array<int|string, mixed>.',
				38,
			],
			[
				'Property AppendedArrayKey\Foo::$stringArray (array<string, mixed>) does not accept array<int|string, mixed>.',
				46,
			],
			[
				'Property AppendedArrayKey\MorePreciseKey::$test (array<1|2|3, string>) does not accept non-empty-array<int, string>.',
				80,
			],
			[
				'Property AppendedArrayKey\MorePreciseKey::$test (array<1|2|3, string>) does not accept non-empty-array<1|2|3|4, string>.',
				85,
			],
		]);
	}

	public function testBug5372Two(): void
	{
		$this->analyse([__DIR__ . '/../Arrays/data/bug-5372_2.php'], []);
	}

	public function testBug5447(): void
	{
		$this->analyse([__DIR__ . '/../Arrays/data/bug-5447.php'], []);
	}

	public function testAppendedArrayItemType(): void
	{
		$this->analyse(
			[__DIR__ . '/../Arrays/data/appended-array-item.php'],
			[
				[
					'Property AppendedArrayItem\Foo::$integers (array<int>) does not accept array<int|string>.',
					18,
				],
				[
					'Property AppendedArrayItem\Foo::$callables (array<callable(): mixed>) does not accept non-empty-array<array{1, 2, 3}|(callable(): mixed)>.',
					20,
				],
				[
					'Property AppendedArrayItem\Foo::$callables (array<callable(): mixed>) does not accept non-empty-array<array{\'AppendedArrayItem\\\\Foo\', \'classMethod\'}|(callable(): mixed)>.',
					23,
				],
				[
					'Property AppendedArrayItem\Foo::$callables (array<callable(): mixed>) does not accept non-empty-array<array{\'Foo\', \'Hello world\'}|(callable(): mixed)>.',
					25,
				],
				[
					'Property AppendedArrayItem\Foo::$integers (array<int>) does not accept array<int|string>.',
					27,
				],
				[
					'Property AppendedArrayItem\Foo::$integers (array<int>) does not accept array<int|string>.',
					32,
				],
				[
					'Property AppendedArrayItem\Bar::$stringCallables (array<callable(): string>) does not accept non-empty-array<(callable(): string)|(Closure(): 1)>.',
					45,
				],
				[
					'Property AppendedArrayItem\Baz::$staticProperty (array<AppendedArrayItem\Lorem>) does not accept array<AppendedArrayItem\Baz>.',
					79,
				],
			],
		);
	}

	public function testBug5804(): void
	{
		$this->analyse([__DIR__ . '/data/bug-5804.php'], [
			[
				'Property Bug5804\Blah::$value (array<int>|null) does not accept array<int|string>.',
				12,
			],
			[
				'Property Bug5804\Blah::$value (array<int>|null) does not accept array<Bug5804\Blah|int>.',
				17,
			],
		]);
	}

	public function testBug6286(): void
	{
		if (PHP_VERSION_ID < 70400) {
			$this->markTestSkipped('Test requires PHP 7.4.');
		}
		$this->analyse([__DIR__ . '/data/bug-6286.php'], [
			[
				'Property Bug6286\HelloWorld::$details (array{name: string, age: int}) does not accept array{name: string, age: \'Forty-two\'}.',
				19,
			],
			[
				'Property Bug6286\HelloWorld::$nestedDetails (array<array{name: string, age: int}>) does not accept non-empty-array<array{name: string, age: \'Eleventy-one\'|int}>.',
				22,
			],
		]);
	}

	public function testBug4906(): void
	{
		$this->analyse([__DIR__ . '/data/bug-4906.php'], []);
	}

	public function testBug4910(): void
	{
		$this->analyse([__DIR__ . '/data/bug-4910.php'], []);
	}

	public function testBug3703(): void
	{
		$this->analyse([__DIR__ . '/data/bug-3703.php'], [
			[
				'Property Bug3703\Foo::$bar (array<string, array<string, array<int>>>) does not accept array<string, array<string, array<int|string>>>.',
				15,
			],
			[
				'Property Bug3703\Foo::$bar (array<string, array<string, array<int>>>) does not accept array<string, array<string, array<int|string>|int>>.',
				18,
			],
			[
				'Property Bug3703\Foo::$bar (array<string, array<string, array<int>>>) does not accept array<string, array<string, array<int>>|string>.',
				21,
			],
		]);
	}

	public function testBug6333(): void
	{
		if (PHP_VERSION_ID < 70400 && !self::$useStaticReflectionProvider) {
			$this->markTestSkipped('Test requires PHP 7.4.');
		}

		$this->analyse([__DIR__ . '/data/bug-6333.php'], []);
	}

	public function testBug3339(): void
	{
		$this->analyse([__DIR__ . '/data/bug-3339.php'], []);
	}

}
