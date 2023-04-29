<?php

class DB{
	static function connect(){
		require(__DIR__ . '/config.php');
		return new mysqli($config["DB_HOST"], $config["DB_USER"], $config["DB_PASS"], $config["DB_NAME"]);
	}

	static function close($con){
		if($con != null){
			$con->close();
		}
	}

	static function executeQuery($query, $con = null, $paramTypes = "", ...$params){
		$opened = false;
		if($con == null){
			$con = self::connect();
			$opened = true;
		}

		if(count($params) > 0){
			$result = self::executePreparedQuery($con, $query, $paramTypes, ...$params);
		} else {
			$result = self::executeNonPreparedQuery($con, $query);
		}

		$data = [];
		while($row = $result->fetch_assoc()){
			$data[] = $row;
		}

		if($opened){
			self::close($con);
		}

		return $data;
	}

	static function executeQueryForSingle($query, $con = null, $paramTypes = "", ...$params){
		$data = self::executeQuery($query, $con, $paramTypes, ...$params);
		if(count($data) > 0){
			return $data[0];
		}

		return null;
	}

	private static function executePreparedQuery($con, $query, $paramTypes, ...$params){
		$stmt = $con->prepare($query);
		$stmt->bind_param($paramTypes, ...$params);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}

	private static function executeNonPreparedQuery($con, $query){
		return $con->query($query);
	}
}

?>