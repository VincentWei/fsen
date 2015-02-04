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

class FseSettingsApplicationsController extends Controller {
	public function view () {
		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
		}
	}

	public function new_app_key () {
		$app_name = $this->post('appName');
		$app_desc = $this->post('appDesc');
		$app_url = $this->post('appURL');
		$app_icon_url = $this->post('appIconURL');

		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
		}

		if (strlen ($app_name) > 32 || strlen ($app_name) < 3) {
			$this->set ('error', t('Too short or too long app name!'));
			return;
		}

		if (strlen ($app_desc) > 255 || strlen ($app_desc) < 5) {
			$this->set ('error', t('Too short or too long app description!'));
			return;
		}

		$urls = array (&$app_url, &$app_icon_url);
		foreach ($urls as &$url) {
			if ($url == "") {
				$url = NULL;
			}
			else if (!preg_match ("/^(http|https):\/\/[^\s]*$/", $url)) {
				$this->set ('error', t('Bad URL!'));
				return;
			}
		}
		unset ($url);

		$fse_id = $_SESSION['FSEInfo']['fse_id'];

		$db = Loader::db ();
		$res = $db->getOne ("SELECT COUNT(*) FROM fse_app_keys WHERE fse_id=?", array ($fse_id));
		if ($res >= 5) {
			$this->set ('error', t('You have created too manay apps! Only 5 apps allowed.'));
			return;
		}

		$res = $db->getOne ("SELECT app_key FROM fse_app_keys WHERE fse_id=? AND app_name=?",
			array ($fse_id, $app_name));
		if ($res != NULL) {
			$this->set ('error', t('Duplicated app name!'));
			return;
		}

		$app_key = hash_hmac ("sha256", $app_name . microtime (), $fse_id);
		$res = $db->Execute ("INSERT INTO fse_app_keys (app_key, fse_id, app_name, app_desc, app_url, app_icon_url, create_time) VALUES (?, ?, ?, ?, ?, ?, NOW())",
			array ($app_key, $fse_id, $app_name, $app_desc, $app_url, $app_icon_url));

		$this->set ('success', t('New app key has been created!'));
	}

	public function delete_app_key () {
		$app_key = $this->post('appKey');

		if (!fse_try_to_login ()) {
			header ("location:/fse_login");
		}

		if (!preg_match ("/^[a-f0-9]{64}$/", $app_key)) {
			$this->set ('error', t('Bad app key!'));
			return;
		}

		$db = Loader::db ();
		$res = $db->Execute ("DELETE FROM fse_app_keys WHERE app_key=?", array ($app_key));
		if ($db->Affected_Rows () == 0) {
			$this->set ('error', t('No such app key!'));
			return;
		}

		$this->set ('success', t('App key deleted!'));
	}
}

