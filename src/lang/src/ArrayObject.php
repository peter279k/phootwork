<?php
namespace phootwork\lang;

class ArrayObject implements \ArrayAccess {
	
	private $array;
	
	public function __construct($contents = []) {
		$this->array = $contents;
	}

	/**
	 * Joins the array with a string
	 *
	 * @param string $glue Defaults to an empty string.
	 * @return String
	 * 		Returns a string containing a string representation of all the array elements in the
	 * 		same order, with the glue string between each element.
	 */
	public function join($glue = '') {
		return new String(implode($this->array, $glue));
	}

	/**
	 * Applies the callback to the elements of the given arrays
	 * 
	 * @param callable $callback Callback function to run for each element in each array. 
	 * @return ArrayObject
	 */
	public function map(callable $callback) {
		return new ArrayObject(array_map($callback, $this->collection));
	}
	
	/**
	 * Filters elements of an array using a callback function
	 * 
	 * @param callable $callback The callback function to use
	 * 		If no callback is supplied, all entries of array equal to false will be removed.
	 * @return ArrayObject
	 */
	public function filter(callable $callback) {
		return new ArrayObject(array_values(array_filter($this->collection, $callback)));
	}
	
	/**
	 * Iteratively reduce the array to a single value using a callback function
	 * 
	 * @param callable $callback callback function
	 * 		`mixed callback (mixed $carry , mixed $item)`
	 * 		$carry - Holds the return value of the previous iteration; in the case of the first iteration it instead holds the value of initial.
	 * 		$item - Holds the value of the current iteration.  
	 * @param mixed $initial If the optional initial is available, it will be used at the beginning of the process, or as a final result in case the array is empty.
	 * @return mixed
	 */
	public function reduce(callable $callback, $initial = null) {
		return array_reduce($this->array, $callback, $initial);
	}

	/**
	 * Merges in other values
	 * 
	 * @param mixed ... Variable list of arrays to merge.
	 * @return ArrayObject $this 
	 */
	public function merge() {
		$this->array = array_merge($this->array, func_get_args());
		return $this;
	}
	
	/**
	 * Returns the keys of the array
	 * 
	 * @return ArrayObject the keys
	 */
	public function keys() {
		return new ArrayObject(array_keys($this->array));
	}
	
	/**
	 * Returns the values of the array
	 * 
	 * @return ArrayObject the values
	 */
	public function values() {
		return new ArrayObject(array_values($this->array));
	}
	
	/**
	 * Flips keys and values
	 * 
	 * @return ArrayObject $this
	 */
	public function flip() {
		$this->array = array_flip($this->array);
		return $this;
	}
	
	/**
	 * @internal
	 */
	public function offsetSet($offset, $value) {
		if (!is_null($offset)) {
			$this->array[$offset] = $value;
		}
	}
	
	/**
	 * @internal
	 */
	public function offsetExists($offset) {
		return isset($this->array[$offset]);
	}
	
	/**
	 * @internal
	 */
	public function offsetUnset($offset) {
		unset($this->array[$offset]);
	}
	
	/**
	 * @internal
	 */
	public function offsetGet($offset) {
		return isset($this->array[$offset]) ? $this->array[$offset] : null;
	}
	
}