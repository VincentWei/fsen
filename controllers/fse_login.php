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
require_once ('helpers/fsen/FSEInfo.php');
require_once ('helpers/fsen/ProjectInfo.php');

class ReturnInfo {
	public $status;
	public $user_name;
	public $nick_name;
	public $project_rights;
}

class FseLoginController extends Controller {

	public function view () {
		if (fse_try_to_login ()) {
			header ("Location:/");
		}
	}

	public function login ($is_mobile_theme = false, $project_id = false) {
		$ret_info = new ReturnInfo;
		$ret_info->status = 'bad';
		$ret_info->detail = '';
		$ret_info->user_name = 'na';
		$ret_info->nick_name = 'na';
		$ret_info->project_rights = '0123456789abcdef';

		if (fse_try_to_login ()) {
			$ret_info->status = 'ok';
			$ret_info->user_name = $_SESSION ['FSEInfo']['user_name'];
			$ret_info->nick_name = $_SESSION ['FSEInfo']['nick_name'];

			if ($project_id && ProjectInfo::getDomainName ($project_id, 'home')) {
				$res = ProjectInfo::getUserRights ($project_id, $_SESSION['FSEInfo']['fse_id']);
				if ($res) {
					$ret_info->project_rights = $res;
				}
			}

			$link = FSEInfo::getPersonalHomeLink ();

			if ($is_mobile_theme == 'true') {
				$ret_info->detail = '
<li>
	<a class="menu-item with-icon" href="' . $link . '" title="Personal homepage">
		<span class="glyphicon glyphicon-user"></span> ' . $ret_info->nick_name . '</a>
</li>
<li>
	<a class="menu-item with-icon" href="/fse_settings">
		<span class="glyphicon glyphicon-cogwheel"></span>
		' . t('Settings') . '
	</a>
</li>
<li>
	<a class="menu-item with-icon" href="/fse_logout/logout">
		<span class="glyphicon glyphicon-log-out"></span>
		' . t('Sign out') . '
	</a>
</li>';
			}
			else {
				$ret_info->detail = '
<li>
	<a class="inline-list" href="' . $link . '" title="Personal homepage">
		<span class="glyphicon glyphicon-user"></span> ' . $ret_info->nick_name . '</a>
</li>
<li>
	<a class="inline-list only-icon" href="/fse_settings" title="Settings">
		<span class="glyphicon glyphicon-cogwheel"></span></a>
</li>
<li>
		<a class="inline-list only-icon" href="/fse_logout/logout" title="Sign out">
			<span class="glyphicon glyphicon-log-out"></span></a>
</li>';
			}
		}
		else {
			if ($is_mobile_theme == 'true') {
				$ret_info->detail = '
<li>
	<a class="menu-item" href="/fse_login">' . t('Sign in') . '</a>
</li>
<li>
	<a class="menu-item" href="/fse_register">' . t('Sign up') . '</a>
</li>';
			}
			else {
				$ret_info->detail = '
<li>
	<a class="button" href="/fse_login">' . t('Sign in') . '</a>
</li>
<li>
	<a class="button button-blue" href="/fse_register">' . t('Sign up') . '</a>
</li>';
			}
		}

		$js = Loader::helper ('json');
		echo $js->encode ($ret_info);
		exit (0);
	}
}

