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
require_once ('helpers/misc.php');
require_once ('helpers/fsen/FSEInfo.php');
require_once ('helpers/fsen/ProjectInfo.php');

class ReturnInfo {
	public $status;
}

class FseSettingsProfileController extends Controller {

	public function view () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
		}
	}

	public function update () {
		$txt = Loader::helper ('text');
		$user_name = $this->post('userName');
		$email_box = $txt->sanitize ($this->post ('emailBox'));
		$nick_name = $txt->sanitize ($this->post ('nickName'));
		$avatar_file_id = (int)$txt->sanitize ($this->post ('avatarFileID'));
		$self_desc = $txt->sanitize ($this->post ('selfDesc'));
		$public_email = $txt->sanitize ($this->post ('publicEmail'));
		$public_url = $txt->sanitize ($this->post ('publicURL'));
		$public_org = $txt->sanitize ($this->post ('publicORG'));
		$location_country = $txt->sanitize ($this->post ('locationCountry'));
		$location_province = $txt->sanitize ($this->post ('locationProvince'));
		$location_district = $txt->sanitize ($this->post ('locationDistrict'));

		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
		}

		if ($_SESSION['FSEInfo']['user_name'] != $user_name) {
			$this->set ('error', t('Session expired or system error!'));
			return;
		}

		if (!preg_match ("/^[\w-]+([.+][\w-]+)*@[\w-]+(\.[\w-]+)+$/", $email_box)) {
			$this->set ('error', t('Bad email address!'));
			return;
		}

		if (!preg_match ("/^[\x{2E80}-\x{9FFF}\x{A000}-\x{A4FF}\x{AC00}-\x{D7FF}\x{F900}-\x{FFFD}\w_]{2,30}$/u",
				$nick_name)) {
			$this->set ('error', t('Bad nickname!'));
			return;
		}

		if ($self_desc == "") {
			$self_desc = NULL;
		}

		if ($public_email == "") {
			$public_email = NULL;
		}
		else if (!preg_match ("/^[\w-]+([.+][\w-]+)*@[\w-]+(\.[\w-]+)+$/", $public_email)) {
			$this->set ('error', t('Bad public email!'));
			return;
		}

		if ($public_url == "") {
			$public_url = NULL;
		}
		else if (!preg_match ("/^(http|https):\/\/[^\s]*$/", $public_url)) {
			$this->set ('error', t('Bad public URL!'));
			return;
		}

		if ($public_org == "") {
			$public_org = NULL;
		}

		foreach (array ($location_country, $location_province, $location_district) as $location) {
			$fragments = explode (":", $location, 2);
			if (!preg_match ("/^[0-9]*$/", $fragments[0]) || strlen ($fragments[1]) < 2) {
				$this->set ('error', t('Bad location!'));
				return;
			}
		}

		$db = Loader::db ();
		if ($email_box != $_SESSION ['FSEInfo']['email_box']) {
			$res = $db->getOne ("SELECT user_name FROM fse_basic_profiles WHERE email_box=?",
				array ($email_box));
			if ($res !== NULL) {
				$this->set ('error', t('Duplicated email address!'));
				return;
			}

			$res = $db->Execute ("UPDATE fse_basic_profiles SET email_verified=0 WHERE user_name=?",
				array ($user_name));
			$hash_value = hash_hmac ("md5", microtime () . rand (), $email_box);
			$db->Execute ("REPLACE INTO fse_email_box_validation_hashes (email_box, hash_value, update_time)
	VALUES (?, ?, NOW())",
				array ($email_box, $hash_value));
			$url_validate_email = BASE_URL . "/fse_validate_email/$hash_value";
			$text_validate_email = t('
You have changed your primary email address, please click the following link to verify the new email address:

	%s
', $url_validate_email);

		}
		else {
			$text_validate_email = "";
		}

		$res = $db->Execute ("UPDATE fse_basic_profiles
	SET email_box=?, nick_name=?, avatar_file_id=?, self_desc=?, public_email=?, public_url=?, public_org=?,
		location_country=?, location_province=?, location_district=?
	WHERE user_name=?",
			array ($email_box, $nick_name, $avatar_file_id, $self_desc, $public_email, $public_url, $public_org,
				$location_country, $location_province, $location_district, $user_name));

		if (preg_match ("/^zh/i", $_SESSION['FSEInfo']['def_locale'])) {
			$doc_lang = 'zh';
		}
		else {
			$doc_lang = 'en';
		}

		$_SESSION ['FSEInfo']['email_box'] = $email_box;
		$_SESSION ['FSEInfo']['nick_name'] = $nick_name;
		$_SESSION ['FSEInfo']['avatar_file_id'] = $avatar_file_id;
		$_SESSION ['FSEInfo']['location_country'] = $location_country;
		$_SESSION ['FSEInfo']['location_province'] = $location_province;
		$_SESSION ['FSEInfo']['location_district'] = $location_district;
		$_SESSION ['FSEInfo']['self_desc'] = $self_desc;
		$_SESSION ['FSEInfo']['public_email'] = $public_email;
		$_SESSION ['FSEInfo']['public_url'] = $public_url;
		$_SESSION ['FSEInfo']['public_org'] = $public_org;
		$_SESSION ['FSEInfo']['avatar_url'] = get_url_from_file_id ($avatar_file_id,
				'/files/images/icon-def-avatar.png');
		$_SESSION ['FSEInfo']['small_avatar_url'] = get_thumbnail_url_from_file_id ($avatar_file_id,
				'/files/images/icon-def-avatar-small.png');

		FSEInfo::onUpdateProfile ($_SESSION ['FSEInfo'], $doc_lang);
		ProjectInfo::onUpdatePersonalProfile ($_SESSION ['FSEInfo'], $doc_lang);

		$url_profile = BASE_URL . "/$doc_lang/engineer/$user_name";

		$location_country = substr (strstr ($location_country, ':'), 1);
		$location_province = substr (strstr ($location_province, ':'), 1);
		$location_district = substr (strstr ($location_district, ':'), 1);

		$email_subject = t('[FSEN] Your profile has changed!');
		$email_body = t('Dear %s,

This is a notification from FSEN  (FullStackEngineer.Net) for the change of your profile:

	* Nickname: %s
	* Primary Email: %s
	* Public Email: %s
	* URL: %s
	* Orgnization: %s
	* Location: %s %s %s
	* Self Description: %s
%s
You can click the following link to visit your personal homepage at FSEN:

	%s

Thanks,
Your friends at FSEN', $nick_name, $nick_name, $email_box, $public_email, $public_url, $public_org, $location_country, $location_province, $location_district, $self_desc, $text_validate_email, $url_profile);

		$mh = Loader::helper ('mail');
		$mh->setSubject ($email_subject);
		$mh->setBody ($email_body);
		$mh->from (EMAIL_DEFAULT_FROM_ADDRESS, EMAIL_DEFAULT_FROM_NAME);
		if (defined ('EMAIL_DEFAULT_BCC_ADDRESS'))
			$mh->bcc (EMAIL_DEFAULT_BCC_ADDRESS, EMAIL_DEFAULT_BCC_NAME);
		$mh->to ($email_box, $nick_name);
		$mh->sendMail ();

		$this->set ('success', t('Your personal profile changed!'));
	}

	public function get_public_profile ($user_name = false) {
		$ret_info = new ReturnInfo;
		$ret_info->status = 'bad';
		$ret_info->fse_info = array ();

		$js = Loader::helper ('json');
		if (!fse_try_to_login ()) {
				echo $js->encode ($ret_info);
				exit (0);
		}

		if ($user_name) {
			$ret_info->status = 'ok';
			$ret_info->fse_info = FSEInfo::getPublicProfile ($user_name);
		}
		else {
			$ret_info->status = 'ok';

			$fse_info = $_SESSION['FSEInfo'];
			unset ($fse_info['fse_id']);
			unset ($fse_info['hashed_passwd']);
			# we return email box for the logged in user
			# unset ($fse_info['email_box']);
			# unset ($fse_info['avatar_file_id']);
			if (!isset ($fse_info['avatar_url'])) {
				$fse_info['avatar_url'] = get_url_from_file_id ($fse_info['avatar_file_id']);
			}

			$ret_info->fse_info = $fse_info;
		}

		echo $js->encode ($ret_info);
		exit (0);
	}
}

