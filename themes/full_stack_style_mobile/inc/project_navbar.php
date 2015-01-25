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

require_once ('helpers/misc.php');
require_once ('helpers/fsen/ProjectInfo.php');

$project_info = ProjectInfo::getBasicInfo ($project_id);
?>
<nav class="sub-nav-bar" lang="<?php echo $doc_lang ?>">
	<a <?php echo ($domain_handle == 'contribute')?'class="active"':'' ?>
		href="<?php echo ProjectInfo::assemblePath ($project_id, 'contribute') ?>" class="tab-item"><span
		class="glyphicon glyphicon-heart"></span></a>
	<a <?php echo ($domain_handle == 'community')?'class="active"':'' ?>
		href="<?php echo ProjectInfo::assemblePath ($project_id, 'community') ?>" class="tab-item"><span
		class="glyphicon glyphicon-group"></span></a>
	<a <?php echo ($domain_handle == 'document')?'class="active"':'' ?>
		href="<?php echo ProjectInfo::assemblePath ($project_id, 'document') ?>" class="tab-item"><span
		class="glyphicon glyphicon-book"></span></a>
	<a <?php echo ($domain_handle == 'download')?'class="active"':'' ?>
		href="<?php echo ProjectInfo::assemblePath ($project_id, 'download') ?>" class="tab-item"><span
		class="glyphicon glyphicon-download"></span></a>
	<a <?php echo ($domain_handle == 'home')?'class="active"':'' ?>
		href="<?php echo ProjectInfo::assemblePath ($project_id, 'home') ?>" class="tab-item"><span
		class="glyphicon glyphicon-home"></span></a>

<?php
if ($domain_handle != 'home') {
?>
		<img src="<?php echo get_url_from_file_id ($project_info['icon_file_id'], '/files/images/icon-fsen-144.png'); ?>"/ alt="Logo Icon" />
		<h1><?php echo h5($project_info['name']) ?></h1>
<?php
}
?>
</nav>

