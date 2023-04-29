<?php

require_once(__DIR__ . '/RelationshipDto.php');

class ContactDto{
	public $ID;
	public $firstName;
	public $lastName;
	public $mobile;
	public $landline;
	public $email;
	public $relationship;

	function __construct($id, $fname, $lname, $mob, $land, $email, $relID, $relName){
		$this->ID = $id;
		$this->firstName = $fname;
		$this->lastName = $lname;
		$this->mobile = $mob;
		$this->landline = $land;
		$this->email = $email;
		$this->relationship = new RelationshipDto($relID, $relName);
	}

	static function createFromDataset($row){
		return new ContactDto($row['ID'], $row['FirstName'], $row['LastName'], $row['Mobile'], $row['Landline'], $row['Email'], $row['RelationshipTypeID'], $row['Relationship']);
	}

	function fullName(){
		return $this->firstName . " " . $this->lastName;
	}
}