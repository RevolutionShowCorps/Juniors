<?php

class Validation{
	static function checkRequiredFields($fields, $array){
		$validationErrors = array();
	
		foreach($fields as $field){
			if(!isset($array[$field]) || trim($array[$field]) == ""){
				$validationErrors[$field] = "This field is required";
			}
		}
	
		return $validationErrors;
	}

	static function checkDateFields($fields, $array){
		$validationErrors = array();

		foreach($fields as $field){
			if(isset($array[$field])){
				$test = new DateTime($array[$field]);
				if($test == null){
					$validationErrors[$field] = "Invalid date";
				} else {
					$array[$field] = $test;
				}
			}
		}

		return $validationErrors;
	}
}