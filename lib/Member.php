<?php

require_once(__DIR__ . "/DB.php");

class Member{
	static function getByID($id, $con = null){
		$openedConnection = false;
		if($con == null){
			$openedConnection = true;
			$con = DB::connect();
		}

		$member = DB::executeQueryForSingle("SELECT * FROM Members WHERE ID = ?", $con, "s", $id);

		if($member != null){
			$member['contacts'] = DB::executeQuery("SELECT c.*, r.Name AS Relationship, mc.RelationshipTypeID FROM MemberContacts mc INNER JOIN Contacts c ON c.ID = mc.ContactID INNER JOIN RelationshipTypes r ON r.ID = mc.RelationshipTypeID WHERE mc.MemberID = ? ORDER BY r.SortOrder, c.LastName, c.FirstName", $con, "s", $id);

			$member['doctor'] = DB::executeQueryForSingle("SELECT * FROM Doctors WHERE ID = ?", $con, "i", $member['DoctorID']);
		}

		if($openedConnection){
			DB::close($con);
		}

		return $member;
	}

	static function update($id, $fname, $lname, $genderID, $dob, $medical, $allergies, $lastTetanus, $canDressWounds, $canAdministerMedication, $con = null){
		$openedConnection = false;
		if($con == null){
			$openedConnection = true;
			$con = DB::connect();
		}

		DB::executeQuery("UPDATE Members SET FirstName = ?, LastName = ?, GenderID = ?, DateOfBirth = ?, MedicalDetails = ?, Allergies = ?, LastTetanus = ?, CanDressWounds = ?, CanAdministerMedication = ? WHERE ID = ?", $con, "ssissssiis", trim($fname), trim($lname), $genderID, $dob, trim($medical), trim($allergies), $lastTetanus, $canDressWounds, $canAdministerMedication, $id);

		if($openedConnection){
			DB::close($con);
		}
	}
}