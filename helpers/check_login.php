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

function fse_get_info ($fse_id, $hashed_passwd)
{
	$db = Loader::db ();
	$fse_info = $db->getRow ("SELECT * FROM fse_basic_profiles WHERE fse_id=?", array ($fse_id));
	if (count ($fse_info) == 0 || $fse_info ['hashed_passwd'] != $hashed_passwd) {
		return null;
	}
	return $fse_info;
}

function fse_try_to_login ()
{
	if (empty ($_SESSION['FSEInfo'])) {
		if (empty ($_COOKIE['FSEID']) || empty ($_COOKIE['HashedPasswd'])) {
			return false;
		}
		else {
			$fse_info = fse_get_info ($_COOKIE['FSEID'], $_COOKIE['HashedPasswd']);
			if (empty ($fse_info)) {
				return false;
			}
			else {
				$_SESSION ['FSEInfo'] = $fse_info;
				return true;
			}
		}
	}

	return true;
}

function fse_logout () {
	unset ($_SESSION['FSEInfo']);
	if (!empty ($_COOKIE['FSEID']) || !empty ($_COOKIE['HashedPasswd'])) {
		setcookie ("FSEID", null, time()-3600*24*365, DIR_REL . '/');
		setcookie ("HashedPasswd", null, time()-3600*24*365, DIR_REL . '/'); 
	}
}

?>
