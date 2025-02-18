<?php declare(strict_types=1);
/**
 * This file is part of the Phootwork package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 * @copyright Thomas Gossmann
 */

namespace phootwork\tokenizer\tests;

use PHPUnit\Framework\TestCase;

abstract class TokenizerTest extends TestCase {
	
	protected function getSample($file) {
		return file_get_contents(sprintf(__DIR__.'/fixtures/samples/%s.php', $file));
	}
}
