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

Loader::model ('fsen_localization');
FSENLocalization::setupInterfaceLocalization4AjaxRequest ();

$txt = Loader::helper ('text');
$email_box = $txt->sanitize ($_POST ['emailBox']);
$hash_value = $txt->sanitize ($_POST ['hashValue']);
$hashed_passwd = $txt->sanitize ($_POST ['hashedPasswd']);

if (!preg_match ("/^[\w-]+([.+][\w-]+)*@[\w-]+(\.[\w-]+)+$/", $email_box)) {
	echo "bad: bad email address!";
	exit (0);
}

if (!preg_match ("/^[0-9a-f]{32}$/", $hashed_passwd)) {
	echo "bad: bad password!";
	exit (0);
}

$db = Loader::db ();
$query = "SELECT A.fse_id, A.nick_name, A.def_locale, B.update_time
	FROM fse_basic_profiles AS A, fse_reset_password_validation_hashes AS B
	WHERE B.hash_value=? AND A.email_box=B.email_box";
$row = $db->getRow ($query, array ($hash_value));
if (empty ($row) || count ($row) == 0) {
	echo "bad: bad request!";
	exit (0);
}

$fse_id = $row ['fse_id'];
$nick_name = $row ['nick_name'];
if (preg_match ("/^zh/i", $row['def_locale'])) {
	$doc_lang = 'zh';
}
else {
	$doc_lang = 'en';
}

$query = "UPDATE fse_basic_profiles SET hashed_passwd=? WHERE fse_id=?";
$res = $db->Execute ($query, array ($hashed_passwd, $fse_id));

$query = "DELETE FROM fse_reset_password_validation_hashes WHERE hash_value=?";
$res = $db->Execute ($query, array ($hash_value));

if ($_SESSION['FSEInfo']['fse_id'] == $fse_id) {
	setcookie ("HashedPasswd", $new_hashed_passwd, 0, DIR_REL . '/');
	$_SESSION['FSEInfo']['hashed_passwd'] = $new_hashed_passwd;
}

$url_request_to_reset_password = BASE_URL . "/fse_request_to_reset_password";
$url_contact = BASE_URL . "/$doc_lang/misc/contact";

$mail_subject = t('[FSEN] Your password has changed');
$mail_body = t ("Dear %s,

We wanted to let you know that your FSEN (FullStackEngineer.Net) password was changed.

If you did not perform this action, you can recover access by entering %s into the form at

%s

If you run into problems, please contact support by visiting %s.

Thanks,
Your friends at FSEN",
	$nick_name, $email_box, $url_request_to_reset_password, $url_contact);

$mh = Loader::helper ('mail');
$mh->setSubject ($mail_subject);
$mh->setBody ($mail_body);
$mh->from (EMAIL_DEFAULT_FROM_ADDRESS, EMAIL_DEFAULT_FROM_NAME);
if (defined ('EMAIL_DEFAULT_BCC_ADDRESS'))
	$mh->bcc (EMAIL_DEFAULT_BCC_ADDRESS, EMAIL_DEFAULT_BCC_NAME);
$mh->to ($email_box, $nick_name);
$mh->sendMail ();

echo "ok";

?>

