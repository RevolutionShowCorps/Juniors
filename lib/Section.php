<?php

require_once('DB.php');
require_once('Member.php');

require_once('DTO/SectionDto.php');

class Section{
	
	static function getAll($includeMembers = false, $con = null){
		$openedConnection = false;
		if($con == null){
			$openedConnection = true;
			$con = DB::connect();
		}
		
		$sections = array();
		$result = DB::executeQuery("SELECT * FROM Sections ORDER BY Name", $con);
		
		foreach($result as $row){
			$section = SectionDto::createFromDataset($row);

			if($includeMembers){
				$section->members = Member::getBySection($section->ID, $con);
			}

			$sections[] = $section;
		}
		
		if($openedConnection){
			DB::close($con);
		}
		
		return $sections;
	}

	static function getById($id, $includeMembers = false, $con = null){
		$openedConnection = false;
		if($con == null){
			$openedConnection = true;
			$con = DB::connect();
		}

		$section = null;
		$result = DB::executeQueryForSingle("SELECT * FROM Sections WHERE ID = ?", $con, "i", $id);

		if($result != null){
			$section = SectionDto::createFromDataset($result);

			if($includeMembers){
				$section->members = Member::getBySection($section->ID, $con);
			}
		}

		if($openedConnection){
			DB::close($con);
		}
	}

	static function create($name, $con = null){
		$openedConnection = false;
		if($con == null){
			$openedConnection = true;
			$con = DB::connect();
		}

		DB::executeQuery("INSERT INTO Sections (Name) VALUES (?)", $con, "s", $name);
		$section = self::getById($con->insert_id);

		if($openedConnection){
			DB::close($con);
		}

		return $section;
	}
}