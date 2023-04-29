<?php

require_once(__DIR__ . "/DB.php");
require_once(__DIR__ . "/DTO/MemberDto.php");
require_once(__DIR__ . "/Utils.php");

require_once(__DIR__ . "/Contact.php");

class Member{
	static function getByID($id, $con = null){
		$openedConnection = false;
		if($con == null){
			$openedConnection = true;
			$con = DB::connect();
		}

		$member = null;
		$result = DB::executeQueryForSingle("SELECT * FROM Members WHERE ID = ?", $con, "s", $id);

		if($result != null){
			$member = MemberDto::createFromDataset($result);

			$contacts = Contact::getForMemberID($member->ID);
			foreach($contacts as $contact){
				$member->addContact($contact);
			}

			$member->doctor = DB::executeQueryForSingle("SELECT * FROM Doctors WHERE ID = ?", $con, "i", $result['DoctorID']);
		}

		if($openedConnection){
			DB::close($con);
		}

		return $member;
	}

	static function update($member, $con = null){
		$openedConnection = false;
		if($con == null){
			$openedConnection = true;
			$con = DB::connect();
		}

		DB::executeQuery("UPDATE Members SET FirstName = ?, LastName = ?, GenderID = ?, DateOfBirth = ?, MedicalDetails = ?, Allergies = ?, LastTetanus = ?, CanDressWounds = ?, CanAdministerMedication = ? WHERE ID = ?", $con, "ssissssiis", trim($member->firstName), trim($member->lastName), $member->genderID, Utils::dateTimeForDB($member->DOB), trim($member->medicalConditions), trim($member->allergies), Utils::dateTimeForDB($member->lastTetanus), $member->canDressWounds, $member->canAdministerMedication, $member->ID);

		if($openedConnection){
			DB::close($con);
		}
	}
}