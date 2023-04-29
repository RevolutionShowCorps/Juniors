<?php

require_once(__DIR__ . '/../Utils.php');

class MemberDto{
	public $ID;
	public $firstName;
	public $lastName;
	public $genderID;
	public $DOB;
	public $medicalConditions;
	public $allergies;
	public $lastTetanus;
	public $canDressWounds;
	public $canAdministerMedication;
	
	public $doctor = null;
	public $contacts = array();

	function __construct($id, $fname, $lname, $gender, $dob, $medical, $allergies, $tetanus, $wounds, $medication){
		$this->ID = $id;
		$this->firstName = $fname;
		$this->lastName = $lname;
		$this->genderID = $gender;
		$this->medicalConditions = $medical;
		$this->allergies = $allergies;
		$this->canDressWounds = $wounds;
		$this->canAdministerMedication = $medication;
		
		$this->DOB = Utils::toDateTime($dob);
		$this->lastTetanus = Utils::toDateTime($tetanus);
	}

	static function createFromDataset($row){
		return new MemberDto($row['ID'], $row['FirstName'], $row['LastName'], $row['GenderID'], $row['DateOfBirth'], $row['MedicalDetails'], $row['Allergies'], $row['LastTetanus'], $row['CanDressWounds'], $row['CanAdministerMedication']);
	}

	function fullName(){
		return $this->firstName . " " . $this->lastName;
	}

	function addContact($contact){
		$this->contacts[] = $contact;
	}
}