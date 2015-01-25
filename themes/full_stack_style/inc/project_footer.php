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

$volumes = ProjectInfo::getAllVolumes ($project_id, 'misc');
?>

<nav class="footer-nav-bar" lang="<?php echo $doc_lang ?>">
	<ul class="nav nav-pills" >
		<li class="{active}">
			<a href="<?php echo ProjectInfo::assemblePath ($project_id, 'misc') ?>"
					title="<?php echo ProjectInfo::getDomainDesc ($project_id, 'misc') ?>">
				<?php echo ProjectInfo::getDomainName ($project_id, 'misc') ?>
			</a>
		</li>
<?php
foreach ($volumes as $v) {
?>
		<li class="{active}">
			<a href="<?php echo ProjectInfo::assemblePath ($project_id, 'misc', $v['volume_handle']) ?>"
					title="<?php echo h5($v['volume_desc']) ?>">
				<?php echo h5($v['volume_name']) ?>
			</a>
		</li>
<?php
	}
?>
	</ul>
</nav>

