<?php

require_once('BaseAccessor.php');
require_once('Member.php');

require_once('DTO/SectionDto.php');
require_once('DTO/MemberSectionDto.php');

class Section extends BaseAccessor{
	
	static function getAll($includeMembers = false, $con = null){
		$openedConnection = self::ensureConnected($con);
		
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
		$openedConnection = self::ensureConnected($con);

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

	static function getCurrentForMember($memberID, $con = null){
		$openedConnection = self::ensureConnected($con);

		$section = null;
		$result = DB::executeQueryForSingle("SELECT s.ID AS SectionID, s.Name AS SectionName, r.ID AS RoleID, r.Name AS RoleName, r.IsStaff AS RoleIsStaff FROM MemberSections ms INNER JOIN Sections s ON s.ID = ms.SectionID INNER JOIN Roles r ON r.ID = ms.RoleID WHERE ms.MemberID = ? AND ms.StartDate <= NOW() AND IFNULL(ms.EndDate, NOW()) >= NOW()", $con, "s", $memberID);

		if($result != null){
			$section = MemberSectionDto::createFromDataset($result);
		}

		if($openedConnection){
			DB::close($con);
		}

		return $section;
	}

	static function create($name, $con = null){
		$openedConnection = self::ensureConnected($con);
		
		DB::executeQuery("INSERT INTO Sections (Name) VALUES (?)", $con, "s", $name);
		$section = self::getById($con->insert_id);
		
		if($openedConnection){
			DB::close($con);
		}
		
		return $section;
	}

	static function addMember($sectionID, $roleID, $memberID, $con = null){
		$openedConnection = self::ensureConnected($con);
		$skip = false;

		$section = self::getCurrentForMember($memberID, $con);
		if($section != null){
			if($section->ID == $sectionID && $section->role->ID == $roleID){
				$skip = true;
			} else {
				self::removeMember($section->ID, $memberID, $con);
			}
		}

		if(!$skip){
			DB::executeQuery("INSERT INTO MemberSections (MemberID, RoleID, SectionID, StartDate) VALUES (?, ?, ?, NOW())", $con, "sii", $memberID, $roleID, $sectionID);
		}

		if($openedConnection){
			DB::close($con);
		}
	}

	static function removeMember($sectionID, $memberID, $con = null){
		$openedConnection = self::ensureConnected($con);

		DB::executeQuery("UPDATE MemberSections SET EndDate = DATE_SUB(NOW(), INTERVAL 1 MINUTE) WHERE SectionID = ? AND MemberID = ? AND (EndDate IS NULL OR EndDate > NOW())", $con, "is", $sectionID, $memberID);

		if($openedConnection){
			DB::close($con);
		}
	}
}