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

class FseValidateEmailController extends Controller {

	public function view ($hash_value = false) {
		if ($hash_value == false) {
			$this->set ('error', t('Bad request.'));
			return;
		}

		if (!preg_match ("/^[0-9a-f]{32}$/", $hash_value)) {
			$this->set ('error', t('Bad parameter.'));
			return;
		}

		$db = Loader::db ();
		$query = "SELECT A.email_box, B.update_time
	FROM fse_basic_profiles AS A, fse_email_box_validation_hashes AS B
	WHERE B.hash_value=? AND A.email_box=B.email_box";
		$row = $db->getRow ($query, array ($hash_value));
		if (empty ($row) || count ($row) == 0) {
			$this->set ('error', t('Invalid parameter.'));
			return;
		}

		$email_box = $row['email_box'];
		$db->Execute ("UPDATE fse_basic_profiles SET email_verified=1 WHERE email_box=?", array ($email_box));
		$db->Execute ("DELETE FROM fse_email_box_validation_hashes WHERE email_box=?", array ($email_box));
		$_SESSION['FSEInfo']['email_verified'] = 1;
		$this->set ('success', t('Your email address is verified: %s', $email_box));
	}
}
