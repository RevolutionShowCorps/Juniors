<?php

class RoleDto{
	public $ID;
	public $name;
	public $isStaff;

	function __construct($id, $name, $staff){
		$this->ID = $id;
		$this->name = $name;
		$this->isStaff = $staff;
	}
}