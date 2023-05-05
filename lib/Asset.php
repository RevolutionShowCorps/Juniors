<?php

require_once('BaseAccessor.php');
require_once("DTO/AssetDto.php");
require_once("DTO/AssetHireDto.php");

require_once("Member.php");

class Asset extends BaseAccessor {
	const LIST_SELECT = "SELECT a.*, s.Name AS SectionName, s.Colour AS SectionColour, ah.ID AS AssetHireID, ah.MemberID AS AssetHireMemberID, ah.HireStart as AssetHireStart, ah.HireEnd as AssetHireEnd, ah.Deposit as AssetDeposit FROM Assets a LEFT OUTER JOIN Sections s ON s.ID = a.SectionID LEFT OUTER JOIN AssetHire ah ON ah.ID = a.CurrentHireID";
	const LIST_DEFAULT_SORT = "ORDER BY s.Name, a.CurrentHireID";

	static function getAll($con = null){
		$openedConnection = self::ensureConnected($con);

		$assets = array();
		$data = DB::executeQuery(self::LIST_SELECT . " " . self::LIST_DEFAULT_SORT, $con);

		foreach($data as $row){
			$asset = AssetDto::createFromDataset($row);

			if($row["AssetHireID"] != null){
				$member = Member::getById($row["AssetHireMemberID"], $con);
				$asset->currentHire = new AssetHireDto($row["AssetHireID"], $member, Utils::toDateTime($row["AssetHireStart"]), Utils::toDateTime($row["AssetHireEnd"]), $row["AssetDeposit"]);
			}

			$assets[] = $asset;
		}

		if($openedConnection){
			DB::close($con);
		}

		return $assets;
	}

	static function getAvailable($con = null){
		$openedConnection = self::ensureConnected($con);

		$assets = array();
		$data = DB::executeQuery(self::LIST_SELECT . " WHERE CurrentHireID IS NULL " . self::LIST_DEFAULT_SORT, $con);

		foreach($data as $row){
			$asset = AssetDto::createFromDataset($row);
			$assets[] = $asset;
		}

		if($openedConnection){
			DB::close($con);
		}

		return $assets;
	}

	static function getLoanedOut($con = null){
		$openedConnection = self::ensureConnected($con);
	
		$assets = array();
		$data = DB::executeQuery(self::LIST_SELECT . " WHERE CurrentHireID IS NOT NULL " . self::LIST_DEFAULT_SORT, $con);
	
		foreach($data as $row){
			$asset = AssetDto::createFromDataset($row);

			$member = Member::getById($row["AssetHireMemberID"], $con);
			$asset->currentHire = new AssetHireDto($row["AssetHireID"], $member, Utils::toDateTime($row["AssetHireStart"]), Utils::toDateTime($row["AssetHireEnd"]), $row["AssetDeposit"]);
				
			$assets[] = $asset;
		}
	
		if($openedConnection){
			DB::close($con);
		}
	
		return $assets;
	}
}