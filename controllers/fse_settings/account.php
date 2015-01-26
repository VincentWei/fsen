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

require_once ('helpers/check_login.php');
require_once ('helpers/fsen/ProjectInfo.php');

class FseSettingsAccountController extends Controller {
	public function view () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
		}
	}

	public function change_password () {
		$user_name = $this->post('userName');
		$old_hashed_passwd = $this->post('oldHashedPasswd');
		$new_hashed_passwd = $this->post('newHashedPasswd');

		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
		}

		if ($_SESSION['FSEInfo']['user_name'] != $user_name) {
			$this->set ('error', t('Session expired or system error!'));
			return;
		}

		if (!preg_match ("/^[0-9a-f]{32}$/", $old_hashed_passwd)) {
			$this->set ('error', t('Bad request!'));
			return;
		}

		if (!preg_match ("/^[0-9a-f]{32}$/", $new_hashed_passwd)) {
			$this->set ('error', t('Bad new password!'));
			return;
		}

		if ($_SESSION['FSEInfo']['hashed_passwd'] != $old_hashed_passwd) {
			$this->set ('error', t('Bad old password!'));
			return;
		}

		if ($old_hashed_passwd == $new_hashed_passwd) {
			$this->set ('error', t('Same password!'));
			return;
		}

		$db = Loader::db ();
		$db->query ("UPDATE fse_basic_profiles SET hashed_passwd=? WHERE user_name=?",
			array ($new_hashed_passwd, $user_name));

		/* pass expired for setcookie as 0, this will force to login visiting the site next time */
		setcookie ("HashedPasswd", $new_hashed_passwd, 0, DIR_REL . '/');
		$_SESSION['FSEInfo']['hashed_passwd'] = $new_hashed_passwd;

		if (preg_match ("/^zh/i", $_SESSION['FSEInfo']['def_locale'])) {
			$doc_lang = 'zh';
		}
		else {
			$doc_lang = 'en';
		}
		$nick_name = $_SESSION['FSEInfo']['nick_name'];
		$email_box = $_SESSION['FSEInfo']['email_box'];
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

		$this->set ('success', t('Your password changed!'));
	}

	public function verify_email () {
		$email_box = $this->post('primaryEmail');

		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
		}

		if ($_SESSION['FSEInfo']['email_box'] != $email_box) {
			$this->set ('error', t('Session expired or system error!'));
			return;
		}

		if ($_SESSION['FSEInfo']['email_verified']) {
			$this->set ('error', t('Your primary email has been verified!'));
			return;
		}

		$db = Loader::db ();
		$res = $db->getOne ('SELECT UNIX_TIMESTAMP(update_time) AS ctime
	FROM fse_email_box_validation_hashes WHERE email_box=?', array ($email_box));
		if ($res != NULL && ($res + 3600*12) > time ()) {
			$this->set ('error', t('Duplicated request to validate email in 12 hours! Please try to find the validation email in your Trash mail folder'));
			return;
		}

		$hash_value = hash_hmac ("md5", microtime () . rand (), $email_box);
		$db->Execute ('REPLACE INTO fse_email_box_validation_hashes (email_box, hash_value, update_time)
	VALUES (?, ?, NOW())', array ($email_box, $hash_value));

		if (preg_match ("/^zh/i", $_SESSION['FSEInfo']['def_locale'])) {
			$doc_lang = 'zh';
		}
		else {
			$doc_lang = 'en';
		}
		$nick_name = $_SESSION['FSEInfo']['nick_name'];
		$user_name = $_SESSION['FSEInfo']['user_name'];
		$url_validate_email = BASE_URL . "/fse_validate_email/$hash_value";
		$url_profile = BASE_URL . "/$doc_lang/engineer/$user_name";
		$url_about = BASE_URL . "/$doc_lang/help/site-policy";

		$mail_subject = t('[FSEN] Verify your primary email address!');
		$mail_body = t('Dear %s,

We got a validating email address request at FSEN (FullStackEngineer.Net)!
Please click the following link to verify this email address:

	%s

You can click the following link to visit your personal homepage at FSEN:

	%s

Please also visit the following link to know the rules to use this site:

	%s

Thanks,
Your friends at FSEN', $nick_name, $url_validate_email, $url_profile, $url_about);

		$mh = Loader::helper ('mail');
		$mh->setSubject ($mail_subject);
		$mh->setBody ($mail_body);
		$mh->from (EMAIL_DEFAULT_FROM_ADDRESS, EMAIL_DEFAULT_FROM_NAME);
		if (defined ('EMAIL_DEFAULT_BCC_ADDRESS'))
			$mh->bcc (EMAIL_DEFAULT_BCC_ADDRESS, EMAIL_DEFAULT_BCC_NAME);
		$mh->to ($email_box, $nick_name);
		$mh->sendMail ();

		$this->set ('success', t('The email validating your primary email address has been sent! Please check your email box and follow the instruction in the email. You may need to find the email in your Trash folder.'));
	}

	public function change_email_settings () {
		$user_name = $this->post('userName');

		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
		}

		if ($_SESSION['FSEInfo']['user_name'] != $user_name) {
			$this->set ('error', t('Session expired or system error!'));
			return;
		}

		$email_keep_private = 0;
		if ($this->post('keepEmailPrivate') == 'on') {
			$email_keep_private = 1;
		}

		$db = Loader::db ();
		$db->query ("UPDATE fse_basic_profiles SET email_keep_private=? WHERE user_name=?",
			array ($email_keep_private, $user_name));

		$_SESSION['FSEInfo']['email_keep_private'] = $email_keep_private;

		$this->set ('success', t('Your email settings stored!'));
	}

	public function delete_account () {
		$delete_intent = $this->post('deleteIntent');
		$user_name = $this->post('userName');
		$hashed_passwd = $this->post('hashedPasswd');

		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
		}

		if ($delete_intent != 'delete my account') {
			$this->set ('error', t('You did not confirm your intent!'));
			return;
		}

		if ($_SESSION['FSEInfo']['user_name'] != $user_name) {
			$this->set ('error', t('Wrong username!'));
			return;
		}

		if ($_SESSION['FSEInfo']['hashed_passwd'] != $hashed_passwd) {
			$this->set ('error', t('Wrong password!'));
			return;
		}

		$db = Loader::db ();
		$projects = $db->getAll ('SELECT project_id, doc_lang FROM fsen_projects WHERE fse_id=?',
			array ($_SESSION['FSEInfo']['fse_id']));
		foreach ($projects as $p) {
			$db->Execute ("DELETE FROM fsen_projects WHERE project_id=?", array ($p['project_id']));

			/* delete project pages */
			$page = Page::getByPath (ProjectInfo::assemblePath ($p['project_id'], 'home'));
			if ($page->getCollectionID () > 0) {
				$page->delete ();
			}

			ProjectInfo::onDeleteProject ($p['project_id']);
		}

		if (preg_match ("/^zh/i", $_SESSION['FSEInfo']['def_locale'])) {
			$doc_lang = 'zh';
		}
		else {
			$doc_lang = 'en';
		}

		ProjectInfo::deleteProjectDocPart (SYSTEM_PROJECT_SHORTNAME . '-' . $doc_lang, 'document', 'blog', $user_name);

		$page = Page::getByPath ("/$doc_lang/engineer/$user_name");
		if ($page->getCollectionID () > 0) {
			$page->delete ();
		}

		$db->query ("DELETE FROM fse_basic_profiles WHERE user_name=?", array ($user_name));

		$nick_name = $_SESSION['FSEInfo']['nick_name'];
		$email_box = $_SESSION['FSEInfo']['email_box'];
		$url_register = BASE_URL . '/fse_register';

		$mail_subject = t('[FSEN] Your account have been deleted!');
		$mail_body = t('Dear %s,

We have deleted your account at FSEN (FullStackEngineer.Net)!

We welcome you to sign up a new account at FSEN at any time:

	%s

Good luck and regards,
Your friends at FSEN', $nick_name, $url_register);

		$mh = Loader::helper ('mail');
		$mh->setSubject ($mail_subject);
		$mh->setBody ($mail_body);
		$mh->from (EMAIL_DEFAULT_FROM_ADDRESS, EMAIL_DEFAULT_FROM_NAME);
		if (defined ('EMAIL_DEFAULT_BCC_ADDRESS'))
			$mh->bcc (EMAIL_DEFAULT_BCC_ADDRESS, EMAIL_DEFAULT_BCC_NAME);
		$mh->to ($email_box, $nick_name);
		$mh->sendMail ();

		unset ($_SESSION['FSEInfo']);
		setcookie ("FSEID", null, time()-3600*24*365, DIR_REL . '/');
		setcookie ("HashedPasswd", null, time()-3600*24*365, DIR_REL . '/'); 

		header ("location:/");
		exit (0);
	}
}

