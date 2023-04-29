<?php

require_once(__DIR__ . '/DTO/ContactDTO.php');

class Contact{
	static function getForMemberID($id, $con = null){
		$openedConnection = false;
		if($con == null){
			$openedConnection = true;
			$con = DB::connect();
		}

		$contacts = array();
		$result = DB::executeQuery("SELECT c.*, r.Name AS Relationship, mc.RelationshipTypeID FROM MemberContacts mc INNER JOIN Contacts c ON c.ID = mc.ContactID INNER JOIN RelationshipTypes r ON r.ID = mc.RelationshipTypeID WHERE mc.MemberID = ? ORDER BY r.SortOrder, c.LastName, c.FirstName", $con, "s", $id);

		foreach($result as $row){
			$contact = ContactDto::createFromDataset($row);
			$contacts[] = $contact;
		}

		if($openedConnection){
			DB::close($con);
		}

		return $contacts;
	}

	static function getByID($id, $con = null){
		$openedConnection = false;
		if($con == null){
			$openedConnection = true;
			$con = DB::connect();
		}

		$contact = null;
		$result = DB::executeQueryForSingle("SELECT c.*, r.Name AS Relationship, mc.RelationshipTypeID FROM MemberContacts mc INNER JOIN Contacts c ON c.ID = mc.ContactID INNER JOIN RelationshipTypes r ON r.ID = mc.RelationshipTypeID WHERE mc.ContactID = ?", $con, "s", $id);

		if($result != null){
			$contact = ContactDto::createFromDataset($result);
		}

		if($openedConnection){
			DB::close($con);
		}

		return $contact;
	}

	static function createForMember($id, $fname, $lname, $mobile, $landline, $email, $con = null){
		$openedConnection = false;
		if($con == null){
			$openedConnection = true;
			$con = DB::connect();
		}

		$contactID = Utils::get_guid();
		DB::executeQuery("INSERT INTO Contacts (ID, FirstName, LastName, Mobile, Landline, Email) VALUES (?, ?, ?, ?, ?, ?)", $con, "ssssss", $contactID, $_POST['fname'], $_POST['lname'], $_POST['mobile'], $_POST['landline'], $_POST['email']);

		DB::executeQuery("INSERT INTO MemberContacts (MemberID, ContactID, RelationshipTypeID) VALUES (?, ?, ?)", $con, "ssi", $_GET['id'], $contactID, $_POST['relationship']);

		$contact = self::getByID($contactID);

		if($openedConnection){
			DB::close($con);
		}

		return $contact;
	}

	static function update($contact, $relationship = null, $memberID = null, $con = null){
		$openedConnection = false;
		if($con == null){
			$openedConnection = true;
			$con = DB::connect();
		}

		DB::executeQuery("UPDATE Contacts SET FirstName = ?, LastName = ?, Mobile = ?, Landline = ?, Email = ? WHERE ID = ?", $con, "ssssss", $contact->firstName, $contact->lastName, $contact->mobile, $contact->landline, $contact->email, $contact->ID);

		if($relationship != null){
			DB::executeQuery("UPDATE MemberContacts SET RelationshipTypeID = ? WHERE MemberID = ? AND ContactID = ?", $con, "iss", $relationship, $memberID, $contact->ID);
		}

		if($openedConnection){
			DB::close($con);
		}
	}
}