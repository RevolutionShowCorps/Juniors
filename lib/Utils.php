<?php

class Utils{
	static function get_guid() {
		$data = PHP_MAJOR_VERSION < 7 ? openssl_random_pseudo_bytes(16) : random_bytes(16);
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // Set version to 0100
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // Set bits 6-7 to 10
		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

	static function toDateTime($val){
		if($val == null || trim($val) == ""){
			return null;
		}

		return new DateTime($val);
	}

	static function dateTimeForDB($dateTime){
		if($dateTime == null){
			return null;
		}

		return $dateTime->format("Y-m-d H:i:s");
	}

	static function truncateText($text, $len = 50){
		if(strlen($text) > $len){
			$wrapped = wordwrap($text, $len, ";_;");
			$parts = explode(";_;", $wrapped);
			$text = $parts[0] . "...";
		}

		return $text;
	}

	static function getRandomColour(){
		return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
	}
}