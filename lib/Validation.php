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
			if(isset($array[$dateField])){
				$test = new DateTime($array[$dateField]);
				if($test == null){
					$validationErrors[$dateField] = "Invalid date";
				} else {
					$array[$dateField] = $test;
				}
			}
		}

		return $validationErrors;
	}
}