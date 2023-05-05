<?php

require_once('DB.php');

class BaseAccessor{
	protected static $con;
	protected static $openedConnection;

	protected static function ensureConnected(&$con){
		if($con == null){
			$con = DB::connect();
			return true;
		}

		return false;
	}
}