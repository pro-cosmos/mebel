<?php
namespace liveopencart\lib\v0004\traits;

trait cache {
	
	protected $_cache = array();
	protected $_cache_simple = array();
	
	protected function hasCache($cache_name, $key, $subkey='') {
		return ( $subkey ? isset($this->_cache[$cache_name][$key][$subkey]) : isset($this->_cache[$cache_name][$key]) );
	}
	
	protected function getCache($cache_name, $key, $subkey='') {
		if ( $this->hasCache($cache_name, $key, $subkey) ) {
			return $subkey ? $this->_cache[$cache_name][$key][$subkey] : $this->_cache[$cache_name][$key];
		}
	}
	
	protected function setCache($cache_name, $key, $value, $subvalue=null) {
		if ( func_num_args() == 4 ) {
			$this->_cache[$cache_name][$key][$value] = $subvalue;
			return $subvalue;
		} else {
			$this->_cache[$cache_name][$key] = $value;
			return $value;
		}
	}
	
	protected function clearCache($cache_name, $key='', $subkey='') {
		if ( $key ) {
			if ( $subkey ) {
				unset($this->_cache[$cache_name][$key]);
			} else {
				unset($this->_cache[$cache_name][$key][$subkey]);
			}
		} else {
			unset($this->_cache[$cache_name]);
		}
	}
	
	protected function hasCacheSimple($key) {
		return isset($this->_cache_simple[$key]);
	}
	
	protected function getCacheSimple($key) {
		if ( $this->hasCacheSimple($key) ) {
			return $this->_cache_simple[$key];
		}
	}
	
	protected function setCacheSimple($key, $value) {
		if ( !isset($this->_cache_simple) ) {
			$this->_cache_simple = array();
		}
		$this->_cache_simple[$key] = $value;
		return $value;
	}
	
	
	
}