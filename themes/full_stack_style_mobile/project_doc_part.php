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
				<?php echo h5($c->getCollectionName()); ?>
			</h1>
			<p class="lead">
				<?php echo h5($c->getCollectionDescription()); ?>
			</p>
		</div>
	</header>
<?php
	if ($project_shortname != SYSTEM_PROJECT_SHORTNAME) {
?>
	<nav>
		<ol class="breadcrumb">
			<li><a href="<?php echo ProjectInfo::assemblePath ($project_id, 'home') ?>"><?php echo ProjectInfo::getDomainName ($project_id, 'home') ?></a></li>
			<li><a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle) ?>"><?php echo ProjectInfo::getDomainName ($project_id, $domain_handle) ?></a></li>
			<li><a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle) ?>"><?php echo ProjectInfo::getVolumeName ($project_id, $domain_handle, $volume_handle) ?></a></li>
			<li class="active"><?php echo ProjectInfo::getPartName ($project_id, $domain_handle, $volume_handle, $part_handle) ?></li>
		</ol>
	</nav>
<?php
	}
	else {
?>
	<nav>
		<ol class="breadcrumb">
			<li><a href="<?php echo ProjectInfo::assemblePath ($project_id, 'home') ?>"><?php echo ProjectInfo::getDomainName ($project_id, 'home') ?></a></li>
			<li><a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle) ?>"><?php echo ProjectInfo::getVolumeName ($project_id, $domain_handle, $volume_handle) ?></a></li>
			<li class="active"><?php echo ProjectInfo::getPartName ($project_id, $domain_handle, $volume_handle, $part_handle) ?></li>
		</ol>
	</nav>
<?php
	}
?>
	<article class="formal-content" lang="<?php echo $doc_lang ?>">

<?php
	$chapters = ProjectInfo::getAllChapters ($project_id, $domain_handle, $volume_handle, $part_handle);
?>
		<section class="container">
			<header>
				<h1>
					<?php echo ProjectInfo::getPartName ($project_id, $domain_handle, $volume_handle, $part_handle) ?>
					<span class="section-action visible-on-edit-document-right">
						<a class="launch-modal"
								href="<?php echo "/index.php/tools/add_new_chapter.php?fsenDocLang=$doc_lang&cID=$page_id&projectID=$project_id&domainHandle=$domain_handle&volumeHandle=$volume_handle&partHandle=$part_handle" ?>">
							<m class="glyphicon glyphicon-circle-plus"></m>
						</a>
					</span>
				</h1>
			</header>

<?php
	if (count ($chapters) > 0) {
?>
			<nav class="block-menu">
				<ul>
<?php
		foreach ($chapters as $cpt) {
?>
						<li class="list-unstyled">
							<p>
								<span class="menu-item-arrow"></span>
								<a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $cpt['chapter_handle']); ?>"><?php echo h5($cpt['chapter_name']) ?></a>
								<span
										class="section-action visible-on-edit-document-right"
										style="margin-top: 0;">
									<a class="launch-modal"
											href="/index.php/tools/delete_chapter.php?<?php echo "fsenDocLang=$doc_lang&cID=$page_id&projectID=$project_id&domainHandle=$domain_handle&volumeHandle=$volume_handle&partHandle=$part_handle&chapterHandle=" . $cpt['chapter_handle'] ?>">
										<m class="glyphicon glyphicon-circle-remove"></m>
									</a>
								</span>
								<span class="section-action visible-on-edit-document-right"
										style="margin-top: 0; margin-right: 5px;">
									<a class="launch-modal"
											href="/index.php/tools/edit_chapter.php?<?php echo "fsenDocLang=$doc_lang&cID=$page_id&projectID=$project_id&domainHandle=$domain_handle&volumeHandle=$volume_handle&partHandle=$part_handle&chapterHandle=" . $cpt['chapter_handle'] ?>">
										<m class="glyphicon glyphicon-edit"></m>
									</a>
								</span>
							</p>
							<p style="margin-left:29px;">
								<small><?php echo h5($cpt['chapter_desc']) ?></small>
							</p>
						</li>
<?php
		}
?>
				</ul>
			</nav>
<?php
	}
?>
		</section>
<?php

	$parts = ProjectInfo::getAllParts ($project_id, $domain_handle, $volume_handle);
	$idx_part = 0;
	foreach ($parts as $p) {
		if ($p ['part_handle'] == $part_handle) {
			$idx_curr_part = $idx_part;
			break;
		}
		$idx_part ++;
	}

	if (isset ($idx_curr_part) && count($parts) > 1) {
?>
		<nav class="container">
			<ul class="pager">
<?php
		if ($idx_curr_part == 0) {
?>
				<li class="previous disabled">
					<a href="#">&laquo;</a>
				</li>
<?php
		}
		else {
			$href = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $parts[$idx_curr_part - 1]['part_handle']);
			$name = $parts[$idx_curr_part - 1]['part_name'];
?>
				<li class="previous">
					<a href="<?php echo $href ?>">&laquo; <?php echo h5($name) ?></a>
				</li>
<?php
		}

		if ($idx_curr_part == (count($parts) - 1)) {
?>
				<li class="next disabled">
					<a href="#">&raquo;</a>
				</li>
<?php
		}
		else {
			$href = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $parts[$idx_curr_part + 1]['part_handle']);
			$name = $parts[$idx_curr_part + 1]['part_name'];
?>
				<li class="next">
					<a href="<?php echo $href ?>"><?php echo h5($name) ?> &raquo;</a>
				</li>
<?php
		}
?>
			</ul>
		</nav>
<?php
	}
?>

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
