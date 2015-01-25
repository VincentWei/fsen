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

if (!preg_match ("/^[\w-]+([.+][\w-]+)*@[\w-]+(\.[\w-]+)+$/", $email_box)) {
	echo "bad: bad email address!";
	exit (0);
}

$db = Loader::db ();
$row = $db->getRow ("SELECT fse_id, nick_name, email_verified FROM fse_basic_profiles WHERE email_box=?",
	array ($email_box));
if ($row === false || count ($row) == 0) {
	echo "bad: not registered email address!";
	exit (0);
}

if ($row['email_verified'] == 0) {
	echo "bad: not verified email address!";
	exit (0);
}

$res = $db->getOne ("SELECT UNIX_TIMESTAMP(update_time) AS ctime
		FROM fse_reset_password_validation_hashes WHERE email_box=?", array ($email_box));
if ($res != NULL && ($res + 3600*12) > time ()) {
	echo "error: duplicated request to rest password in 12 hours!";
	exit (0);
}

$hash_value = hash_hmac ("md5", microtime () . rand (), $email_box);
$db->Execute ("REPLACE INTO fse_reset_password_validation_hashes (email_box, hash_value, update_time)
		VALUES (?, ?, NOW())",
	array ($email_box, $hash_value));

$url_reset_password = BASE_URL . "/fse_reset_password?hashValue=$hash_value";
$url_request_to_reset_password = BASE_URL . "/fse_request_to_reset_password";

$mail_subject = t ('[FSEN] Please reset your password');
$mail_body = t ("Dear %s,

We heard that you lost your FSEN (FullStackEngineer.Net) password. Sorry about that!

But don't worry! You can use the following link within the next day to reset your password:

%s

If you don't use this link within 24 hours, it will expire. To get a new password reset link, please visit

%s

Thanks,
Your friends at FSEN",
	$row['nick_name'], $url_reset_password, $url_request_to_reset_password);

$mh = Loader::helper ('mail');
$mh->setSubject ($mail_subject);
$mh->setBody ($mail_body);
$mh->from (EMAIL_DEFAULT_FROM_ADDRESS, EMAIL_DEFAULT_FROM_NAME);
if (defined ('EMAIL_DEFAULT_BCC_ADDRESS'))
	$mh->bcc (EMAIL_DEFAULT_BCC_ADDRESS, EMAIL_DEFAULT_BCC_NAME);
$mh->to ($email_box, $row['nick_name']);
$mh->sendMail ();

echo "ok";
?>

