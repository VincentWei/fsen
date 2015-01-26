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
require_once ('helpers/check_passwd.php');
require_once ('helpers/fsen/ProjectInfo.php');

class FseRegisterController extends Controller {

	public function view () {
		if (fse_try_to_login ()) {
			header ("Location:/");
		}
	}

	private function add_blog_zone_page ($db, $user_name, $nick_name, $doc_lang) {
		$all_blogs_page = Page::getByPath ("/$doc_lang/blog");
		if ($all_blogs_page->getCollectionID() == false) {
			$this->set ('error', t('System error: no blog homepage!'));
			return false;
		}

		$page_desc = t('Blogs of %s', $nick_name);
		$sys_project_id = SYSTEM_PROJECT_SHORTNAME . '-' . $doc_lang;

		$db->Execute ("INSERT IGNORE fsen_project_doc_volume_parts
    (project_id, domain_handle, volume_handle, part_handle, part_name, part_desc, required, display_order)
VALUES (?, 'document', 'blog', ?, ?, ?, 1, ?)",
			array ($sys_project_id, $user_name, $nick_name, $page_desc, time()));

		$page = Page::getByPath ("/$doc_lang/blog/$user_name");
		if ($page->getCollectionID() > 0) {
			return true;
		}

		return ProjectInfo::addPartPage ($sys_project_id, 'document', $all_blogs_page,
			$user_name, $nick_name, $page_desc);
	}

	private function add_personal_homepage ($user_name, $nick_name, $doc_lang) {
		$page = Page::getByPath ("/$doc_lang/engineer/$user_name");
		if ($page->getCollectionID() > 0) {
			$this->set ('error', t('Existed username: %s!', $user_name));
			return false;
		}

		$page_type = CollectionType::getByHandle ('personal_homepage');
		$parent_page = Page::getByPath ("/$doc_lang/engineer");
		$page = $parent_page->add ($page_type, array ('cName' => $nick_name, 'cHandle' => $user_name));
		if ($page instanceof Page) {
			$block_type = BlockType::getByHandle ("fse_public_profile");
			$area = new Area('Side Bar');
			$page->addBlock ($block_type, $area, array ("fseUsername" => $user_name));
		}
		else {
			$this->set ('error', t('Failed to create personal homepage!'));
			return false;
		}

		return true;
	}

	public function do_register () {
		$txt = Loader::helper ('text');
		$user_name = $txt->sanitize ($this->post ('userName'));
		$hashed_passwd = $txt->sanitize ($this->post ('hashedPasswd'));
		$email_box = $txt->sanitize ($this->post ('emailBox'));
		$nick_name = $txt->sanitize ($this->post ('nickName'));
		$user_locale = $txt->sanitize ($this->post ('userLocale'));
		$location_country = $txt->sanitize ($this->post ('locationCountry'));
		$location_province = $txt->sanitize ($this->post ('locationProvince'));
		$location_district = $txt->sanitize ($this->post ('locationDistrict'));

		# check captcha here
		$captcha = Loader::helper('validation/captcha');
		if (!$captcha->check("captchaCode")) {
			$this->set ('error', t('Wrong captcha code!'));
			return;
		}

		if (!preg_match ("/^[\w_]{4,30}$/", $user_name)) {
			$this->set ('error', t('Bad username!'));
			return;
		}
		$user_name = strtolower ($user_name);

		if (!preg_match ("/^[\w-]+([.+][\w-]+)*@[\w-]+(\.[\w-]+)+$/", $email_box)) {
			$this->set ('error', t ('Bad email address!'));
			return;
		}

		if (!preg_match ("/^[\x{2E80}-\x{9FFF}\x{A000}-\x{A4FF}\x{AC00}-\x{D7FF}\x{F900}-\x{FFFD}\w_]{2,30}$/u",
				$nick_name)) {
			$this->set ('error', t('Bad nickname!'));
			return;
		}

		if (!check_hashed_passwd ($user_name, $hashed_passwd)) {
			$this->set ('error', t('You are using too weak passsword or the password is same as your username!'));
			return;
		}

		foreach (array ($location_country, $location_province, $location_district) as $location) {
			$fragments = explode (":", $location, 2);
			if (!preg_match ("/^[0-9]*$/", $fragments[0]) || strlen ($fragments[1]) < 2) {
				$this->set ('error', t('Bad location!'));
				return;
			}
		}

		$db = Loader::db ();
		$fse_id = hash_hmac ("md5", $user_name, $email_box);
		$query = 'INSERT IGNORE fse_basic_profiles (fse_id, user_name, hashed_passwd, email_box, nick_name,
		location_country, location_province, location_district, email_verified,
		create_time, update_time, last_login_time, def_locale)
	VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, NOW(), NOW(), NOW(), ?)';
		$res = $db->Execute ($query, array ($fse_id, $user_name, $hashed_passwd, $email_box, $nick_name,
			$location_country, $location_province, $location_district, $user_locale));
		if ($db->Affected_Rows () == 0) {
			$this->set ('error', t('Duplicated user name or email address.'));
			return;
		}

		$res = $db->getOne ("SELECT fse_id FROM fsen_projects WHERE project_id='sys-en'");
		if (strlen ($res) == 0) {
			/* make this user as the owner of the system projects */
			$db->Execute ("UPDATE fsen_projects SET fse_id=? WHERE project_id LIKE 'sys-__'", array ($fse_id));
		}

		if (preg_match ("/^zh/i", $user_locale)) {
			$doc_lang = 'zh';
		}
		else {
			$doc_lang = 'en';
		}

		if (!$this->add_personal_homepage ($user_name, $nick_name, $doc_lang)) {
			return;
		}

		if (!$this->add_blog_zone_page ($db, $user_name, $nick_name, $doc_lang)) {
			return;
		}

		$hash_value = hash_hmac ("md5", microtime () . rand (), $email_box);
		$db->Execute ("REPLACE INTO fse_email_box_validation_hashes (email_box, hash_value, update_time)
	VALUES (?, ?, NOW())", array ($email_box, $hash_value));

		$location_country = substr (strstr ($location_country, ':'), 1);
		$location_province = substr (strstr ($location_province, ':'), 1);
		$location_district = substr (strstr ($location_district, ':'), 1);

		$url_validate_email = BASE_URL . "/fse_validate_email/$hash_value";
		$url_profile = BASE_URL . "/$doc_lang/engineer/$user_name";
		$url_about = BASE_URL . "/$doc_lang/help/site-policy";

		$mail_subject = t('[FSEN] Welcome to be a full stack engineer!');
		$mail_body = t ('Dear %s,

		Thanks for your registration at FSEN (FullStackEngineer.Net)!
		Here is your registration information:

			* Username: %s
			* Email Address: %s
			* Location: %s %s %s

		First, please click the following link to verify this email address:

			%s

		You can click the following link to visit your personal homepage at FSEN:

			%s

		Please also visit the following link to know the rules to use this site:

			%s

		Thanks,
		Your friends at FSEN',
			$nick_name, $user_name, $email_box, $location_country, $location_province, $location_district,
			$url_validate_email, $url_profile, $url_about);

		$mh = Loader::helper ('mail');
		$mh->setSubject ($mail_subject);
		$mh->setBody ($mail_body);
		$mh->from (EMAIL_DEFAULT_FROM_ADDRESS, EMAIL_DEFAULT_FROM_NAME);
		if (defined ('EMAIL_DEFAULT_BCC_ADDRESS'))
			$mh->bcc (EMAIL_DEFAULT_BCC_ADDRESS, EMAIL_DEFAULT_BCC_NAME);
		$mh->to ($email_box, $nick_name);
		$mh->sendMail ();

		$this->set ('success', t('Succeed to register. Welcome to be a Full Stack Engineer!'));
	}
}

