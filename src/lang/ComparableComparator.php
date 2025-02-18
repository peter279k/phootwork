<?php declare(strict_types=1);
/**
 * This file is part of the Phootwork package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 * @copyright Thomas Gossmann
 */

namespace phootwork\lang;

/**
 * Class ComparableComparator
 *
 * Comparator for objects implementing phootwork\lang\Comparable interface.
 *
 * @author Thomas Gossmann
 */
class ComparableComparator implements Comparator {

	/**
	 * @param mixed $a
	 * @param mixed $b
	 * @return int
	 * @throws \InvalidArgumentException If the objects don't implement phootwork\lang\Comparable interface.
	 */
	public function compare($a, $b): int {
		if (! $a instanceof Comparable) {
			throw new \InvalidArgumentException(
				"ComparableComparator can compare only objects implementing phootwork\lang\Comparable interface");
		}

		return $a->compareTo($b);
	}
}
