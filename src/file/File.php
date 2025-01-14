<?php declare(strict_types=1);
/**
 * This file is part of the Phootwork package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 * @copyright Thomas Gossmann
 */

namespace phootwork\file;

use \DateTime;
use phootwork\file\exception\FileException;
use phootwork\lang\Text;

class File {
 
	use FileOperationTrait;

	/**
	 * File constructor.
	 *
	 * @param string|Text $filename
	 */
	public function __construct($filename) {
		$this->init($filename);
	}

	/**
	 * Reads contents from the file
	 * 
	 * @throws FileException
	 * @return string contents
	 */
	public function read(): string {
		if (!$this->exists()) {
			throw new FileException(sprintf('File does not exist: %s', $this->getFilename()));
		}

		if (!$this->isReadable()) {
			throw new FileException(sprintf('You don\'t have permissions to access %s file', $this->getFilename()));
		}

		return file_get_contents($this->pathname);
	}

	/**
	 * Writes contents to the file
	 *
	 * @param string $contents
	 * @return $this
	 * @throws FileException
	 */
	public function write(string $contents): self {
		$dir = new Directory($this->getDirname());
		$dir->make();
	
		file_put_contents($this->pathname, $contents);

		return $this;
	}
	
	/**
	 * Touches the file
	 * 
	 * @param int|DateTime $created
	 * @param int|DateTime $lastAccessed
	 * @throws FileException when something goes wrong
	 */
	public function touch($created = null, $lastAccessed = null): void {
		$created = $created instanceof DateTime
			? $created->getTimestamp() 
			: ($created === null ? time() : $created);
		$lastAccessed = $lastAccessed instanceof DateTime
			? $lastAccessed->getTimestamp()
			: ($lastAccessed === null ? time() : $lastAccessed);

		if (!@touch($this->pathname, $created, $lastAccessed)) {
			throw new FileException(sprintf('Failed to touch file at %s', $this->pathname));
		}
	}
	
	/**
	 * Deletes the file
	 *
	 * @throws FileException when something goes wrong
	 */
	public function delete(): void {
		if (!@unlink($this->pathname)) {
			throw new FileException(sprintf('Failed to delete file at %s', $this->pathname));
		}
	}
	
	/**
	 * String representation of this file as pathname
	 */
	public function __toString(): string {
		return $this->pathname;
	}
}

