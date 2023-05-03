<?php

require_once('RoleDto.php');

class MemberSectionDto{
	public $ID;
	public $name;
	public $role;

	function __construct($id, $name, $role){
		$this->ID = $id;
		$this->name = $name;
		$this->role = $role;
	}

	static function createFromDataset($row){
		$role = new RoleDto($row['RoleID'], $row['RoleName'], $row['RoleIsStaff']);

		return new MemberSectionDto($row['SectionID'], $row['SectionName'], $role);
	}
}