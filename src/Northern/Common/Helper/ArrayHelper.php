<?php
/*!
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * Portions copyrighted by Kohana team.
 */

namespace Northern\Common\Helper;

/**
 * Array helper functionality.
 */
abstract class ArrayHelper {

	/**
	 * Returns the value from an array by specifying its key or path for a
	 * nested key. By default, the path delimiter is a . (dot) but other path 
	 * separators can be specified.
	 * 
	 * To return the value of a key:
	 * 
	 *    $arr = array('foo' => 'bar');
	 *    
	 *    $value = get( $arr, 'foo' );
	 *    
	 *    // $value == 'foo'
	 *    
	 * To return the value of a nested key:
	 * 
	 *    $arr = array('foo' => array('bar', array('baz' => 21) ) );
	 * 
	 *    $value = getPath($arr, 'foo.bar.baz');
	 *    
	 *    // $value == 21
	 * 
	 * @param  array $arr
	 * @param  string $path
	 * @param  mixed $default
	 * @param  string $delimiter
	 * @return mixed
	 */
	public static function get( &$arr, $path, $default = NULL, $delimiter = '.'  )
	{
		if( empty( $arr ) )
		{
			return $default;
		}
		
		if( isset( $arr[ $path ] ) )
		{
			return $arr[ $path ];
		} 
		
		$segments = explode( $delimiter, $path );
		
		$cur = $arr;
		
		foreach( $segments as $segment )
		{
			if( ! is_array( $cur ) OR ! array_key_exists( $segment, $cur ) )
			{
				return $default;
			}
			
			$cur = $cur[ $segment ];
		}
		
		return $cur;
	}
	
	/**
	 * Sets the value in an array by specifing its key or path. By default the 
	 * path delimiter is a . (dot) but the other path separators can be specified.
	 * 
	 *    $arr = array();
	 * 
	 *    set($arr, 'foo.bar.bar', 21);
	 * 
	 * @param array   $arr
	 * @param string  $path
	 * @param mixed   $value
	 * @param string  $delimiter
	 */
	public static function set( &$arr, $path, $value, $delimiter = '.' )
	{
		$segments = explode( $delimiter, $path );
		
		if( count( $segments ) === 1 )
		{
			$arr[ $path ] = $value;
			
			return;
		}
		
		$cur = &$arr;
		
		foreach( $segments as $segment )
		{
			if( ! array_key_exists( $segment, $cur ) )
			{
				$cur[ $segment ] = array();
			}
			
			$cur = &$cur[ $segment ];
		}
		
		$cur = $value;
	}
	
	/**
	 * Deletes an entry in the an array by specifing its key or path. By default the 
	 * path delimiter is a . (dot) but the other path separators can be specified.
	 * 
	 * @param array  $arr
	 * @param string $path
	 * @param string $delimiter
	 */
	public static function delete( &$arr, $path, $delimiter = '.' )
	{
		if( isset( $arr[ $path ] ) )
		{
			unset( $arr[ $path ] );
			
			return;
		} 
		
		$segments = explode( $delimiter, $path );
		
		$lastSegment = end( $segments );
		$segments = array_slice( $segments , 0, -1 );
		
		$cur = &$arr;
		
		foreach( $segments as $segment )
		{
			if( ! array_key_exists( $segment, $cur ) )
			{
				return;
			}
			
			$cur = &$cur[ $segment ];
		}
		
		unset( $cur[ $lastSegment ] );
	}
	
	/**
	 * Removes an array of unallowed $paths from given $arr.
	 * 
	 * @param array  $arr
	 * @param array  $paths
	 * @param string $delimiter
	 */
	public static function deletePaths( &$arr, $paths, $delimiter = '.' )
	{
		foreach( $paths as $path )
		{
			static::delete( $arr, $path, $delimiter );
		}
	}
	
	/**
	 * Tests if a key or path exists in a specified array. If the specified key or
	 * path exists this method will return TRUE otherwise FALSE.
	 *
	 * @param  array  $arr
	 * @param  string $path
	 * @param  string $delimiter
	 * @return boolean
	 */
	public static function exists( array &$arr, $path, $delimiter = '.' )
	{
		if( array_key_exists( $path, $arr ) )
		{
			return TRUE;
		}
		
		$segments = explode( $delimiter, $path );
			
		$cur = $arr;
	
		foreach( $segments as $segment )
		{
			if( ! is_array( $cur ) OR ! array_key_exists( $segment, $cur ) )
			{
				return FALSE;
			}
	
			$cur = $cur[$segment];
		}
	
		return TRUE;
	}

	/**
	 * This method will prefix all values of a specified array with a given value.
	 * 
	 * @param array  $values
	 * @param string $prefix
	 */
	static public function prefix( &$values, $prefix )
	{
		foreach( $values as &$value )
		{
			$value = "{$prefix}{$value}";
		}
		
		return $values;
	}
	
	/**
	 * This method 'flattens' an associative array to a list array. The key/value
	 * pair of the associative array are combined with $glue.
	 * 
	 * @param  array  $array
	 * @param  string $glue
	 * @return array
	 */
	static public function flatten( $arr, $glue = '=' )
	{
		$list = array();
		
		foreach( $arr as $key => $value )
		{
			$list[] = "{$key}{$glue}{$value}";
		}
		
		return $list;
	}
	
	/**
	 * This method will return all the keys of a given array and optionally
	 * prefix each key with a specified prefix.
	 * 
	 * @param  array  $array
	 * @param  string $prefix
	 * @return array
	 */
	static public function keys( $array, $prefix = NULL )
	{
		$keys = array_keys( $array );
		
		if( ! empty( $prefix ) )
		{
			$keys = static::prefix( $keys, $prefix );
		}
		
		return $keys;
	}
	
	/**
	 * This method will return all values of a given array and optionally prefix
	 * each value with a specified prefix.
	 * 
	 * @param  array $array
	 * @param  string $prefix
	 * @return array
	 */
	static public function values( $array, $prefix = NULL )
	{
		$values = array_values( $array );
		
		if( ! empty( $prefix ) )
		{
			$values = static::prefix( $values, $prefix );
		}
		
		return $values;
	}
	
	/**
	 * Recursive version of array_map, applies one or more callbacks to all 
	 * elements in an array, including sub-arrays.
	 * 
	 * Apply "strip_tags" to every element in the array
	 *    $array = ArrayHelper::map('strip_tags', $array);
 	 * 
 	 * Apply $this->filter to every element in the array
 	 *    $array = ArrayHelper::map(array(array($this,'filter')), $array);
 	 *    
 	 * Apply strip_tags and $this->filter to every element
 	 *    $array = ArrayHelper::map(array('strip_tags',array($this,'filter')), $array);
	 * 
	 * @param  mixed $callbacks
	 * @param  array $array
	 * @param  mixed $keys
	 * @return array
	 */
	public static function map( $callbacks, $arr, $keys = NULL )
	{
		foreach( $arr as $key => $val )
		{
			if( is_array( $val ) )
			{
				$arr[ $key ] = static::map( $callbacks, $arr[ $key ] );
			}
			elseif( ! is_array( $keys ) OR in_array( $key, $keys ) )
			{
				if( is_array( $callbacks ) )
				{
					foreach( $callbacks as $callback )
					{
						$arr[ $key ] = call_user_func( $callback, $arr[ $key ] );
					}
				}
				else
				{
					$arr[ $key ] = call_user_func( $callbacks, $arr[ $key ] );
				}
			}
		}
	 	
		return $arr;
	}

	/**
	 * Remaps an array structure to another array structure. The $createMode
	 * parameter indicates that if a entry doesn't exists in the $values
	 * array if it should be created with a default value in the results.
	 * 
	 * @param  array $values
	 * @param  array $map
	 * @param  boolean $createMode
	 * @return array
	 */
	static public function remap( &$values, array $map, $createMode = TRUE )
	{
		$results = array();
		
		foreach( $map as $keyfrom => $keyto )
		{
			if( self::exists( $values, $keyfrom ) === TRUE OR $createMode === TRUE )
			{
				self::set( $results, $keyto, self::get( $values, $keyfrom ) );
			}
		}
		
		return $results;
	}
	
	/**
	 * Remaps a collection.
	 * 
	 * @param  array $collection
	 * @param  array $map
	 * @return array
	 */
	static public function remapCollection( array $collection, array $map, $createMode = TRUE )
	{
		$results = array();
		
		foreach( $collection as $item )
		{
			$results[] = self::remap( $item, $map, $createMode );
		}
		
		return $results;
	}
	
}
