<?php

	function lastIndexOf($string,$item) {
		$index=strpos(strrev($string),strrev($item));
		if ($index) {
			$index = strlen($string) - strlen($item) - $index;
			return $index;
		} else return -1;
	}
	
	function getIP() {
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
			$ipAddress = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
		}
		
		return $ipAddress;
	}
