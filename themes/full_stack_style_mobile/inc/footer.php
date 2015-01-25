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

require_once ('helpers/fsen/ProjectInfo.php');

if (!isset ($doc_lang)) {
	$doc_lang = 'en';
}
$sys_project_id = SYSTEM_PROJECT_SHORTNAME . "-$doc_lang";

$volumes = ProjectInfo::getAllVolumes ($sys_project_id, 'misc');
?>

<footer class="global-footer">

	<?php $a = new Area('Footer'); $a->display($c); ?>

	<nav class='footer-block'>
		<ul>
			<li>
				<a href="<?php echo ProjectInfo::assemblePath ($sys_project_id, 'misc') ?>"
						title="<?php echo ProjectInfo::getDomainDesc ($sys_project_id, 'misc') ?>"
						class="inline-list small"><?php echo ProjectInfo::getDomainName ($sys_project_id, 'misc') ?></a>
			</li>
<?php
foreach ($volumes as $v) {
?>
		<li>
			<a href="<?php echo ProjectInfo::assemblePath ($sys_project_id, 'misc', $v['volume_handle']) ?>"
					title="<?php echo h5($v['volume_desc']) ?>"
					class="inline-list small"><?php echo h5($v['volume_name']) ?></a>
		</li>
<?php
	}
?>
		</ul>

		<ul>
			<li><a class="inline-list small" target="_blank" href="http://www.concrete5.org">Concrete5</a></li>
			<li><a class="inline-list small" target="_blank" href="http://getbootstrap.com">Bootstrap</a></li>
			<li><a class="inline-list small" target="_blank" href="https://michelf.ca/projects/php-markdown/">PHP Markdown</a></li>
		</ul>
	</nav>

	<address>
		2015 &copy; FullStackEngineer.NET<br/>
		京ICP备05046847号-6
	</address>
</footer>

