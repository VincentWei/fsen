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

include('inc/head.php');
include ('inc/project_frags.php');
?>

<body>

<div class="full-stack">

<?php
	include('inc/header.php');
	if ($project_shortname != SYSTEM_PROJECT_SHORTNAME) {
		include ('inc/project_navbar.php');
		include ('inc/project_alert_banner.php');
	}
?>
	<header class="banner-<?php echo $page_style ?>">
		<div class="container">
			<h1>
				<?php echo h5($c->getCollectionName()); ?><br />
			</h1>
			<p class="lead">
				<?php echo h5($c->getCollectionDescription()); ?>
			</p>
		</div>
	</header>

	<nav>
		<ol class="breadcrumb">
			<li><a href="<?php echo ProjectInfo::assemblePath ($project_id, 'home') ?>"><?php echo ProjectInfo::getDomainName ($project_id, 'home') ?></a></li>
			<li><a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle) ?>"><?php echo ProjectInfo::getDomainName ($project_id, $domain_handle) ?></a></li>
			<li class="active"><?php echo ProjectInfo::getVolumeName ($project_id, $domain_handle, $volume_handle) ?></li>
		</ol>
	</nav>

	<article class="formal-content" lang="<?php echo $doc_lang ?>">

<!-- Main area (list of forum areas and forums) -->
		<section class="container-fluid">
<?php
	$vlm = ProjectInfo::getVolumeInfo ($project_id, $domain_handle, $volume_handle);
?>
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h2 class="panel-title">
						<?php echo h5($vlm['volume_name']) ?>
						<small style="color:#fafafa;"><?php echo h5($vlm['volume_desc']) ?></small>
					</h2>
				</div>
				<div class="panel-body">
					<ul class="list-group">
<?php
	$parts = ProjectInfo::getAllParts ($project_id, $domain_handle, $volume_handle);
	foreach ($parts as $prt) {
		$latest_chapters = ProjectInfo::getLatestChapters ($project_id, $domain_handle, $volume_handle, $prt['part_handle']);
?>
						<li class="list-group-item">
							<ul class="list-group">
								<li class="list-group-item">
									<span class="badge"><?php echo $prt['nr_chapters'] ?></span>
									<h3 class="list-group-item-heading">
										<a
											href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $prt ['part_handle']) ?>"><?php echo h5($prt['part_name']) ?></a></h3>
									<p class="list-group-item-text"><?php echo h5($prt['part_desc']) ?></p>
								</li>
<?php
		if (count ($latest_chapters) > 0) {
			foreach ($latest_chapters as $cpt) {
				$author_info = FSEInfo::getNameInfo ($cpt['fse_id']);
?>
								<li class="list-group-item">
									<span class="badge"><?php echo ($cpt['nr_sections'] -1) ?></span>
									<h4 class="list-group-item-heading">
										<a href="<?php echo "$page_path/" . $prt['part_handle'] . '/' . $cpt['chapter_handle'] ?>"><?php echo h5($cpt['chapter_name']) ?></a>
									</h4>
									<p class="list-group-item-text">
										<?php echo FSEInfo::getPersonalHomeLink ($author_info, true); ?>
									</p>
								</li>
<?php
			}
		}
?>
							</ul>
						</li>
<?php
	}
?>
					</ul>
				</div>
				<div class="panel-footer">
				</div>
			</div>
		</section>
	</article>

<?php
	if ($project_shortname != SYSTEM_PROJECT_SHORTNAME) {
		include 'inc/project_footer.php';
	}
	include('inc/footer.php');
	include('inc/status-bar.php');
?>

</div>

<?php
include ('inc/dynamic-nav.php');
include('inc/sh-autoloader.php');
Loader::element ('footer_required');
?>

</body>
</html>
