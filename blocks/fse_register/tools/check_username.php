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

$txt = Loader::helper ('text');
$org_user_name = strtolower ($_POST ['userName']);
$user_name = $txt->urlify ($org_user_name);

if ($user_name != $org_user_name) {
	echo 'bad';
	exit (0);
}

if (preg_match ('/^[\w][\w-]{3,29}$/', $user_name)) {
	$db = Loader::db ();
	$query = 'SELECT fse_id FROM fse_basic_profiles WHERE user_name=?';
	$res = $db->getOne ($query, array ($user_name));

	if ($res === NULL)
		echo 'none';
	else
		echo $res;
}
else {
	echo 'bad';
}
?>

