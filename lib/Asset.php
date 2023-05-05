<?php

require_once('BaseAccessor.php');

class Asset extends BaseAccessor {
	static function getAll($con = null){
		$openedConnection = self::ensureConnected($con);

		$data = DB::executeQuery("SELECT * FROM Assets");

		if($openedConnection){
			DB::close($con);
		}

		return $data;
	}
}