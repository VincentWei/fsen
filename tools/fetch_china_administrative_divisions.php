<?php
/**
 * This file is a part of FullStackEngineer.Net Project.
 *
 * FullStackEngineer.Net is a web site for hosting webpages
 * (especially the documents, forums) of open source projects.
 *
 * FullStackEngineer project itself is an open source project.
 *
 * For more information, please refer to:
 *
 *		http://www.fullstackengineer.net/
 *
 * Copyright (C) 2015 WEI Yongming
 * <http://www.fullstackengineer.net/zh/engineer/weiyongming>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
defined('C5_EXECUTE') or die('Access Denied.');

class ReturnInfo {
	public $status;
	public $divisions;
}

$txt = Loader::helper ('text');
$js = Loader::helper ('json');

$division_id = (int)$txt->sanitize($_GET['divisionID']);
if ($division_id < 1)
	$division_id = 1;

$ret_info = new ReturnInfo;
$ret_info->status = "error";
$ret_info->divisions = null;

$db = Loader::db ();
if ($division_id == 1) {
	/* return the level 1 divisions */
	$res = Cache::get ('CNAdministrativeDivisions', "L1");
	if ($res == false) {
		$query = "SELECT id, name FROM cm_china_administrative_divisions WHERE lvl=1 AND hidden=0 ORDER BY id";
		$res = $db->getAll ($query);
		Cache::set ('CNAdministrativeDivisions', "L1", $res);
	}
	$ret_info->divisions = $res;
	$ret_info->status = "ok";
	echo $js->encode ($ret_info);
	exit (0);
}

$row = $db->getRow ('SELECT lft, rgt, lvl FROM cm_china_administrative_divisions WHERE id=? AND hidden=0',
	array ($division_id));
if (count ($row) == 0) {
	echo $js->encode ($ret_info);
	exit (0);
}

/* return the next level divisions */
$res = Cache::get ('CNAdministrativeDivisions', "ID$division_id");
if ($res == false) {
	$left   = $row['lft'];
	$right  = $row['rgt'];
	$level  = $row['lvl'] + 1;
	$query = "SELECT id, name FROM cm_china_administrative_divisions 
	WHERE lvl=? AND lft>? AND rgt<? AND hidden=0 ORDER BY id";
	$res = $db->getAll ($query, array ($level, $left, $right));
	Cache::set ('CNAdministrativeDivisions', "ID$division_id", $res);
}

$ret_info->status = "ok";
$ret_info->divisions = $res;
echo $js->encode ($ret_info);

