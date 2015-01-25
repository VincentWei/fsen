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

$js = Loader::helper ('json');

class ReturnInfo {
        public $status;
        public $fse_info;
}

$ret_info = new ReturnInfo;
$ret_info->status = 'bad';
$ret_info->fse_info = NULL;

if (!fse_try_to_login ()) {
        echo $js->encode ($ret_info);
        exit (0);
}

$ret_info->status = 'ok';
$ret_info->fse_info = $_SESSION['FSEInfo'];
$ret_info->fse_info['fse_id'] = NULL;
$ret_info->fse_info['hashed_passwd'] = NULL;
if (!isset ($ret_info->fse_info['avatar_url'])) {
	$ret_info->fse_info['avatar_url'] = get_url_from_file_id ($_SESSION['FSEInfo']['avatar_file_id'],
			'/files/images/icon-def-avatar.png');
	$ret_info->fse_info['small_avatar_url'] = get_thumbnail_url_from_file_id ($_SESSION['FSEInfo']['avatar_file_id'],
			'/files/images/icon-def-avatar-small.png');
}

echo $js->encode ($ret_info);
exit (0);

?>

