<?php

class AssetHireDto{
	public $ID;
	public $member;
	public $start;
	public $end;
	public $deposit;

	function __construct($id, $member, $start, $end, $deposit){
		$this->ID = $id;
		$this->member = $member;
		$this->start = $start;
		$this->end = $end;
		$this->deposit = $deposit;
	}
}