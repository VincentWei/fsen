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

require_once ('helpers/fsen/FSEInfo.php');

Loader::model ('fsen_localization');
FSENLocalization::setupInterfaceLocalization4AjaxRequest ();

$txt = Loader::helper ('text');
$user_name = $txt->sanitize ($_POST ['userName']);
$hashed_passwd = $txt->sanitize ($_POST ['hashedPasswd']);
$save_passwd = $txt->sanitize ($_POST ['savePasswd']);
$redirect_url = $txt->sanitize ($_POST ['redirectURL']);

class ReturnInfo {
	public $status;
	public $detail;
}

$ret_info = new ReturnInfo;
$ret_info->status = 'bad';
$ret_info->detail = 'na';

$json = Loader::helper ('json');
if (!preg_match ("/^[\w][\w-]{3,29}$/", $user_name)) {
	$ret_info->detail = t('Bad username!');
	echo $json->encode ($ret_info);
	exit (0);
}

if (!preg_match ("/^[0-9a-f]{32}$/", $hashed_passwd)) {
	$ret_info->detail = t('Bad password!');
	echo $json->encode ($ret_info);
	exit (0);
}

$db = Loader::db ();
$row = $db->getRow ("SELECT * FROM fse_basic_profiles WHERE user_name=?", array ($user_name));
if (count ($row) == 0 || $row ['hashed_passwd'] != $hashed_passwd) {
	$ret_info->detail = t('Bad user or password!');
	echo $json->encode ($ret_info);
	exit (0);
}

$_SESSION ['FSEInfo'] = $row;
$db->Execute ("UPDATE fse_basic_profiles SET last_login_time=NOW() WHERE user_name=?", array ($user_name));

$ret_info->status = 'ok';
if (strlen ($redirect_url)) {
	$ret_info->detail = $redirect_url;
}
else {
	$ret_info->detail = FSEInfo::getPersonalHomeLink ($row);
}

if ($save_passwd != "on") {
	echo $json->encode ($ret_info);
	exit (0);
}

setcookie ("FSEID", $row ['fse_id'], time()+3600*24*7, DIR_REL . '/');
setcookie ("HashedPasswd", $hashed_passwd, time()+3600*24*7, DIR_REL . '/');

echo $json->encode ($ret_info);
exit (0);

?>

