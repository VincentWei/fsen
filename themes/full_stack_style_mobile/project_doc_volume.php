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
include('inc/project_frags.php');
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
			<li class="active"><?php echo ProjectInfo::getVolumeName ($project_id, $domain_handle, $volume_handle) ?></li>
		</ol>
	</nav>
<?php
	}
	else {
?>
	<div class="v-seperator">
	</div>
<?php
	}
?>

	<article class="formal-content" lang="<?php echo $doc_lang ?>">

<?php
	$must_chapters = ProjectInfo::getRequiredChapters ($project_id, $domain_handle, $volume_handle);
	if (count ($must_chapters) > 0) {
?>
		<header>
			<h1>
				<?php echo t('Docs for Newbies') ?>
			</h1>
		</header>

		<section class="container-fluid">
			<nav class="block-menu">
				<ul>
<?php
		foreach ($must_chapters as $cpt) {
?>
						<li class="list-unstyled">
							<a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $cpt['part_handle'], $cpt['chapter_handle']); ?>"><p><span class="menu-item-arrow"></span><?php echo h5($cpt['chapter_name']) ?></p></a>
						</li>
<?php
		}
?>
				</ul>
			</nav>
		</section>
<?php
	}
?>
		<header>
			<h1>
				<?php echo t('Table of Contents') ?>
			</h1>
		</header>

		<section class="container-fluid">
		<div class="row row-md-flex row-md-flex-wrap">
<?php
	$parts = ProjectInfo::getAllParts ($project_id, $domain_handle, $volume_handle);
	$nr_cols = count ($parts) / 4;
	if ($nr_cols == 0) $nr_cols = 1;
	$nr_parts_per_col = count ($parts)/$nr_cols;
	if ($nr_parts_per_col == 0) $nr_parts_per_col = 1;

	$start_part = 0;
	for ($i = 0; $i < $nr_cols; $i ++) {
		if ($start_part == count ($parts))
			break;
?>
			<div class="col-sm-12 col-md-3 col-lg-3">
			<div>
<?php
		$end_part = $start_part + $nr_parts_per_col;
		for ($p = $start_part; $p < $end_part; $p++) {
			if ($p == count ($parts))
				break;

			$part_handle = $parts[$p]['part_handle'];
?>
				<nav class="plain-menu">
					<header>
						<h2>
							<a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $parts[$p]['part_handle']); ?>"><?php echo h5($parts[$p]['part_name']); ?></a>
							<span class="section-action visible-on-edit-document-right">
								<a class="launch-modal"
										href="/index.php/tools/add_new_chapter.php?<?php echo "fsenDocLang=$doc_lang&cID=$page_id&projectID=$project_id&domainHandle=$domain_handle&volumeHandle=$volume_handle&partHandle=$part_handle" ?>">
									<m class="glyphicon glyphicon-circle-plus"></m>
								</a>
							</span>
						</h2>
					</header>
					<ul>
<?php
			$chapters = ProjectInfo::getAllChapters ($project_id, $domain_handle, $volume_handle, $part_handle);
			foreach ($chapters as $cpt) {
?>
						<li class="list-unstyled">
							<a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $parts[$p]['part_handle'], $cpt['chapter_handle']); ?>">
								<p><span></span><?php echo h5($cpt['chapter_name']) ?></p>
							</a>
						</li>
<?php
			}

?>
					</ul>
				</nav>
<?php
			$start_part ++;
		}
?>
			</div>
			</div>
<?php
	}
?>
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
