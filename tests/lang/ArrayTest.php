<?php declare(strict_types=1);
/**
 * This file is part of the Phootwork package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 * @copyright Thomas Gossmann
 */

namespace phootwork\lang\tests;

use phootwork\lang\ArrayObject;
use phootwork\lang\Text;
use phootwork\lang\tests\fixtures\Item;
use phootwork\lang\ComparableComparator;
use phootwork\lang\StringComparator;
use PHPUnit\Framework\TestCase;

class ArrayTest extends TestCase {

	public function testArray(): void {
		$base = ['a' => 'b', 'c' => 'd'];
		$arr = new ArrayObject($base);

		$this->assertEquals(new ArrayObject(['b', 'd']), $arr->values());
		$this->assertEquals(new ArrayObject(['a', 'c']), $arr->keys());
		$this->assertEquals($base, $arr->toArray());

		$new = [];
		foreach ($arr as $k => $v) {
			$new[$k] = $v;
		}
		$this->assertEquals($new, $arr->toArray());

		$this->assertEquals(new ArrayObject(['b' => 'a', 'd' => 'c']), $arr->flip());

		$arr = new ArrayObject(['these', 'are', 'my', 'items']);
		$this->assertEquals(new Text('these are my items'), $arr->join(' '));
		$arr->clear();
		$this->assertEquals(0, $arr->count());

		$arr = new ArrayObject();
		$this->assertTrue($arr->isEmpty());
	}

	public function testCount(): void {
		$arr = new ArrayObject(['these', 'are', 'my', 'items']);

		$this->assertEquals(4, $arr->count());
		$this->assertEquals(4, count($arr));

		$arr->merge('a', 'b');

		$this->assertEquals(6, $arr->count());
	}

	public function testArrayAccess(): void {
		$arr = new ArrayObject(['a' => 'b', 'c' => 'd']);

		$this->assertEquals('b', $arr['a']);
		$this->assertTrue(isset($arr['c']));
		$this->assertFalse(isset($arr['x']));
		unset($arr['c']);
		$this->assertFalse(isset($arr['c']));
		$arr['a'] = 'x';
		$this->assertEquals('x', $arr['a']);
	}

	public function testSerialization(): void {
		$arr = new ArrayObject(['these', 'are', 'my', 'items']);
		$serialized = $arr->serialize();

		$brr = new ArrayObject();
		$brr->unserialize($serialized);

		$this->assertEquals($arr, $brr);
	}

	public function testReduce(): void {
		$list = new ArrayObject(range(1, 10));
		$sum = $list->reduce(function($a, $b) {return $a + $b;});

		$this->assertEquals(55, $sum);
	}

	public function testFilter(): void {
		$arr = new ArrayObject(['a' => 'a', 'b' => 'b', 'c' => 'c']);
		$arr = $arr->filter(function ($item) {
			return $item != 'b';
		});

		$this->assertSame(['a' => 'a', 'c' => 'c'], $arr->toArray());
	}

	public function testMap(): void {
		$arr = new ArrayObject(['a' => 'a', 'b' => 'b', 'c' => 'c']);
		$arr = $arr->map(function ($item) {
			return $item . 'val';
		});

		$this->assertSame(['a' => 'aval', 'b' => 'bval', 'c' => 'cval'], $arr->toArray());
	}

	public function testSort(): void {
		$unsorted = [5, 2, 8, 3, 9, 4, 6, 1, 7, 10];
		$list = new ArrayObject($unsorted);

		$this->assertEquals(range(1, 10), $list->sort()->toArray());
		$this->assertEquals(range(10, 1), $list->reverse()->toArray());

		$list = new ArrayObject($unsorted);
		$cmp = function ($a, $b) {
			if ($a == $b) {
				return 0;
			}
			return ($a < $b) ? -1 : 1;
		};
		$this->assertEquals(range(1, 10), $list->sort($cmp)->toArray());

		$items = ['x', 'c', 'a', 't', 'm'];
		$list = new ArrayObject();
		foreach ($items as $item) {
			$list->append(new Item($item));
		}

		$list->sort(new ComparableComparator());
		$this->assertEquals(['a', 'c', 'm', 't', 'x'], $list->map(function ($item) {return $item->getContent();})->toArray());
	}

	public function testSortNotComparableThrowsException(): void {
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('ComparableComparator can compare only objects implementing phootwork\lang\Comparable interface');

		$items = [(object) 'x', (object) 'c', (object) 'a'];
		$list = new ArrayObject();
		foreach ($items as $item) {
			$list->append($item);
		}

		$list->sort(new ComparableComparator());
	}

	public function testSortAssoc(): void {
		$arr = new ArrayObject(['b' => 'bval', 'a' => 'aval', 'c' => 'cval']);
		$arr->sortAssoc();
		$this->assertEquals(['a' => 'aval', 'b' => 'bval', 'c' => 'cval'], $arr->toArray());

		$arr = new ArrayObject(['b' => 'bval', 'a' => 'aval', 'c' => 'cval']);
		$arr->sortAssoc(function ($a, $b) {
			if ($a == $b) {
				return 0;
			}
			return ($a < $b) ? -1 : 1;
		});
		$this->assertEquals(['a' => 'aval', 'b' => 'bval', 'c' => 'cval'], $arr->toArray());

		$arr = new ArrayObject(['b' => new Item('bval'), 'a' => new Item('aval'), 'c' => new Item('cval')]);
		$arr->sortAssoc(new ComparableComparator());
		$this->assertEquals(['a' => 'aval', 'b' => 'bval', 'c' => 'cval'], $arr
				->map(function (Item $elem) {return $elem->getContent();})
				->toArray());
	}

	public function testSortKeys(): void {
		$arr = new ArrayObject(['b' => 'bval', 'a' => 'aval', 'c' => 'cval']);
		$arr->sortKeys();
		$this->assertEquals(['a' => 'aval', 'b' => 'bval', 'c' => 'cval'], $arr->toArray());

		$arr = new ArrayObject(['b' => 'bval', 'a' => 'aval', 'c' => 'cval']);
		$arr->sortKeys(function ($a, $b) {
			if ($a == $b) {
				return 0;
			}
			return ($a < $b) ? -1 : 1;
		});
		$this->assertEquals(['a' => 'aval', 'b' => 'bval', 'c' => 'cval'], $arr->toArray());

		$arr = new ArrayObject(['b' => 'bval', 'a' => 'aval', 'c' => 'cval']);
		$arr->sortKeys(new StringComparator());
		$this->assertEquals(['a' => 'aval', 'b' => 'bval', 'c' => 'cval'], $arr->toArray());
	}

	public function testMutators(): void {
		$base = ['b', 'c', 'd'];
		$arr = new ArrayObject($base);
		$arr->append('e', 'f');

		$this->assertEquals(['b', 'c', 'd', 'e', 'f'], $arr->toArray());
		$this->assertEquals('f', $arr->pop());
		$this->assertEquals('e', $arr->pop());
		$arr->prepend('a');
		$this->assertEquals(['a', 'b', 'c', 'd'], $arr->toArray());
		$this->assertEquals('a', $arr->shift());
		$this->assertEquals($base, $arr->toArray());
		$arr->prepend('a');
		$this->assertEquals(['a', 'b', 'c', 'd'], $arr->toArray());
	}

	public function testEach(): void {
		$result = [];
		$list = new ArrayObject(range(1, 10));
		$list->each(function ($value) use (&$result) {
			$result[] = $value;
		});
		$this->assertEquals($list->toArray(), $result);
	}

	public function testIndex(): void {
		$item1 = 'item 1';
		$item2 = 'item 2';
		$item3 = 'item 3';
		$items = [$item1, $item2];

		$list = new ArrayObject($items);

		$index1 = $list->indexOf($item1);
		$this->assertEquals(0, $index1);
		$this->assertEquals(1, $list->indexOf($item2));
		$this->assertNull($list->indexOf($item3));

		$list->removeAll($items);
		$list->addAll($items);

		$this->assertEquals(2, $list->count());
		$this->assertEquals($index1, $list->indexOf($item1));

		$list->add($item3, 1);
		$this->assertEquals($item3, $list->get(1));
		$this->assertEquals($item2, $list->get(2));
	}

	public function testContains(): void {
		$item1 = 'item 1';
		$item2 = 'item 2';
		$item3 = 'item 3';
		$items = [$item1, $item2];

		$list = new ArrayObject($items);

		$this->assertTrue($list->contains($item2));
		$this->assertFalse($list->contains($item3));
	}

	public function testFind(): void {
		$list = new ArrayObject(range(1, 10));
		$list = $list->map(function (int $item) {
			return new Item((string) $item);
		});

		$search = function (Item $i, $query) {
			return $i->getContent() == $query;
		};

		$item = $list->find(4, $search);
		$this->assertTrue($item instanceof Item);
		$this->assertEquals(4, $item->getContent());
		$this->assertEquals(3, $list->findIndex(4, $search));
		$this->assertNull($list->find(20, $search));

		$fruits = new ArrayObject(['apple', 'banana', 'pine', 'banana', 'ananas']);
		$fruits = $fruits->map(function ($item) {
			return new Item($item);
		});
		$this->assertEquals(1, $fruits->findIndex(function (Item $elem) {
			return $elem->getContent() == 'banana';
		}));
		$this->assertEquals(3, $fruits->findLastIndex(function (Item $elem) {
			return $elem->getContent() == 'banana';
		}));
		$this->assertEquals(3, $fruits->findLastIndex('banana', function (Item $elem, $query) {
			return $elem->getContent() == $query;
		}));
		$this->assertNull($fruits->findLast('mango', function (Item $elem, $query) {
			return $elem->getContent() == $query;
		}));

		$apples = $fruits->findAll('apple', function (Item $elem, $query) {
			return $elem->getContent() == $query;
		});
		$this->assertEquals(1, $apples->count());

		$bananas = $fruits->findAll(function (Item $elem) {
			return $elem->getContent() == 'banana';
		});
		$this->assertEquals(2, $bananas->count());
	}

	public function testSearch(): void {
		$list = new ArrayObject(range(1, 10));
		$search = function ($elem, $query) {return $elem == $query;};

		$this->assertTrue($list->search(4, $search));
		$this->assertFalse($list->search(20, $search));

		$this->assertTrue($list->search(function ($elem) {
			return $elem == 4;
		}));
		$this->assertFalse($list->search(function ($elem) {
			return $elem == 20;
		}));
	}

	public function testSome(): void {
		$list = new ArrayObject(range(1, 10));

		$this->assertTrue($list->some(function ($item) {
			return $item % 2 === 0;
		}));

		$this->assertFalse($list->some(function ($item) {
			return $item > 10;
		}));

		$list = new ArrayObject();
		$this->assertFalse($list->some(function () {
			return true;
		}));
	}

	public function testEvery(): void {
		$list = new ArrayObject(range(1, 10));

		$this->assertTrue($list->every(function ($item) {
			return $item <= 10;
		}));

		$this->assertFalse($list->every(function ($item) {
			return $item > 10;
		}));

		$list = new ArrayObject();
		$this->assertTrue($list->every(function () {
			return true;
		}));
	}

	public function testSlice(): void {
		$fruits = new ArrayObject(['apple', 'banana', 'pine', 'banana', 'ananas']);

		$this->assertEquals(['banana', 'pine'], $fruits->slice(1, 2)->toArray());
	}


	public function testSplice(): void {
		// delete
		$fruits = new ArrayObject(['apple', 'banana', 'pine', 'banana', 'ananas']);
		$this->assertEquals(['apple', 'banana'], $fruits->splice(2)->toArray());

		// cut
		$fruits = new ArrayObject(['apple', 'banana', 'pine', 'banana', 'ananas']);
		$this->assertEquals(['apple', 'ananas'], $fruits->splice(1, -1)->toArray());

		// replace to end
		$fruits = new ArrayObject(['apple', 'banana', 'pine', 'banana', 'ananas']);
		$this->assertEquals(['apple', 'orange'], $fruits->splice(1, $fruits->count(), ['orange'])->toArray());

		// replace
		$fruits = new ArrayObject(['apple', 'banana', 'pine', 'banana', 'ananas']);
		$this->assertEquals(['apple', 'strawberry', 'blackberry', 'banana', 'ananas'], $fruits->splice(1, 2, ['strawberry', 'blackberry'])->toArray());

		// insert array
		$fruits = new ArrayObject(['apple', 'banana', 'pine', 'banana', 'ananas']);
		$this->assertEquals(['apple', 'banana', 'pine', 'orange', 'strawberry', 'banana', 'ananas'], $fruits->splice(3, 0, ['orange', 'strawberry'])->toArray());

		// insert string
		$fruits = new ArrayObject(['apple', 'banana', 'pine', 'banana', 'ananas']);
		$this->assertEquals(['apple', 'banana', 'pine', 'orange', 'banana', 'ananas'], $fruits->splice(3, 0, ['orange'])->toArray());
	}

	public function testClone(): void {
		$fruits = new ArrayObject(['apple', 'banana', 'pine', 'banana', 'ananas']);
		$clonedFruits = clone $fruits;
		$this->assertNotSame($fruits, $clonedFruits);
		$this->assertSame($fruits->toArray(), $clonedFruits->toArray());
	}

	public function testAppend(): void {
		$fruits = new ArrayObject(['apple', 'banana']);
		$fruits->append('pine', 'ananas');
		$this->assertEquals(['apple', 'banana', 'pine', 'ananas'], $fruits->toArray());
		$fruits->append(['peach', 'pear']);
		$this->assertEquals(['apple', 'banana', 'pine', 'ananas', ['peach', 'pear']], $fruits->toArray());
		$fruits->append($obj = new ArrayObject(['wathermelon']));
		$this->assertEquals(['apple', 'banana', 'pine', 'ananas', ['peach', 'pear'], $obj], $fruits->toArray());
	}

	public function testGetWithNotExistentIndex(): void {
		$fruits = new ArrayObject(['apple', 'banana']);
		$element = $fruits->get(4);
		$this->assertNull($element);
	}
}
