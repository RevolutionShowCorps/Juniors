<?php

class RelationshipDto{
	public $ID;
	public $name;

	function __construct($id, $name){
		$this->ID = $id;
		$this->name = $name;
	}
}