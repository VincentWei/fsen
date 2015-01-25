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

/*
	$th = Loader::helper('text');
	$global_area_handle = $th->unhandle ('custom_banner_for_' . str_replace ('-', '_', $project_id));
	$pna = new GlobalArea ($global_area_handle);
*/

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
			<li><a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle) ?>"><?php echo ProjectInfo::getPartName ($project_id, $domain_handle, $volume_handle, $part_handle) ?></a></li>
			<li class="active"><?php echo ProjectInfo::getChapterName ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle) ?></li>
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
			<li><a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle) ?>"><?php echo ProjectInfo::getPartName ($project_id, $domain_handle, $volume_handle, $part_handle) ?></a></li>
			<li class="active"><?php echo ProjectInfo::getChapterName ($project_id, $domain_handle, $volume_handle, $part_handle, $chapter_handle) ?></li>
		</ol>
	</nav>
<?php
	}
?>
	<section class="container-fluid">
		<div class="row">
			<section class="col-sm-6 col-md-3 col-lg-3">
				<header>
					<h1>
						<?php echo t('Table of Contents') ?>
					</h1>
				</header>
				<nav class="plain-menu rounded-border">
					<ul>
<?php
	$parts = ProjectInfo::getAllParts ($project_id, $domain_handle, $volume_handle);
	foreach ($parts as $pt) {
?>
						<li class="list-unstyled">
							<h4>
								<a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $pt['part_handle']); ?>"><?php echo ProjectInfo::getPartName ($project_id, $domain_handle, $volume_handle, $pt['part_handle']) ?></a>
<?php
		if ($pt['part_handle'] == $part_handle) {
?>
								<span class="section-action visible-on-edit-document-right">
									<a class="dialog-launch"
											dialog-append-buttons="true"
											dialog-modal="false"
											dialog-title="New Chapter"
											dialog-width="60%"
											dialog-height="40%"
											href="<?php echo "/index.php/tools/add_new_chapter.php?fsenDocLang=$doc_lang&cID=$page_id&projectID=$project_id&domainHandle=$domain_handle&volumeHandle=$volume_handle&partHandle=" . $pt['part_handle'] ?>">
										<m class="glyphicon glyphicon-circle-plus"></m>
									</a>
								</span>
<?php
		}
?>
							</h4>
						</li>
<?php
		if ($pt['part_handle'] == $part_handle) {
			$chapter = ProjectInfo::getAllChapters ($project_id, $domain_handle, $volume_handle, $part_handle);
?>
						<ul>
<?php
			foreach ($chapter as $cpt) {
?>
<?php
				if ($cpt['chapter_handle'] != $chapter_handle) {
?>
							<li class="list-unstyled">
								<a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $cpt['chapter_handle']); ?>"><p><span class="menu-item-arrow"></span><?php echo h5($cpt['chapter_name']) ?></p></a>
							</li>
<?php
				}
				else {
?>
							<li class="list-unstyled">
								<p>
									<span class="menu-item-arrow"></span><?php echo h5($cpt['chapter_name']) ?>
								</p>
							</li>
<?php
				}
			}
?>
						</ul>
<?php
		}
	}
?>
					</ul>
				</nav>
			</section>

			<section class="col-sm-6 col-md-9 col-lg-9">
<?php
	include 'inc/project_main_areas.php';
?>

<?php
	$chapters = ProjectInfo::getAllChapters ($project_id, $domain_handle, $volume_handle, $part_handle);
	$idx_chapter = 0;
	foreach ($chapters as $cpt) {
		if ($cpt ['chapter_handle'] == $chapter_handle) {
			$idx_curr_chapter = $idx_chapter;
			break;
		}
		$idx_chapter ++;
	}

	if (isset ($idx_curr_chapter) && count($chapters) > 1) {
?>
			<nav>
				<ul class="pager">
<?php
		if ($idx_curr_chapter == 0) {
?>
					<li class="previous disabled">
						<a href="#">&laquo;</a>
					</li>
<?php
		}
		else {
			$href = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle,
						$chapters[$idx_curr_chapter - 1]['chapter_handle']);
			$name = $chapters[$idx_curr_chapter - 1]['chapter_name'];
?>
					<li class="previous">
						<a href="<?php echo $href ?>">&laquo; <?php echo h5($name) ?></a>
					</li>
<?php
		}

		if ($idx_curr_chapter == (count($chapters) - 1)) {
?>
					<li class="next disabled">
						<a href="#">&raquo;</a>
					</li>
<?php
		}
		else {
			$href = ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle,
						$chapters[$idx_curr_chapter + 1]['chapter_handle']);
			$name = $chapters[$idx_curr_chapter + 1]['chapter_name'];
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

			</section>

		</div>
	</section>

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
