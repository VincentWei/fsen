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
require_once ('helpers/fsen/DocSectionManager.php');

include('inc/head.php');
include('inc/project_frags.php');
?>

<body>

<div class="full-stack">

<?php
	include('inc/header.php');
	include ('inc/project_navbar.php');
	include ('inc/project_alert_banner.php');
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

<?php
	$domain_long_desc = ProjectInfo::getDomainLongDesc ($project_id, $domain_handle);
	$domain_long_desc = DocSectionManager::safeMarkdown2HTML ($domain_long_desc);

	$common_request_parts = "?fsenDocLang=$doc_lang&cID=$page_id&projectID=$project_id&domainHandle=$domain_handle&volumeHandle=$volume_handle&partHandle=$part_handle&chapterHandle=$chapter_handle";
?>
	<header class="text-center project-subpage-desc">
		<span class="glyphicon glyphicon-group big-glyph text-major-default"></span><?php echo $domain_long_desc ?>
	</header>

	<article class="formal-content" lang="<?php echo $doc_lang ?>">

<!-- MainForeword area: Flex columns -->
		<section class="container-fluid">
			<div class="row row-md-flex row-md-flex-wrap">
<?php
	$a = Area::getOrCreate ($c, 'MainForeword');
	$blocks = $c->getBlocks ('MainForeword');
	foreach ($blocks as $block) {
		$block->display ();
	}
?>
				<section class="col-md-6 visible-on-edit-document-right alert alert-info alert-dismissible">
					<button type="button" class="close"
							data-dismiss="alert">
						<span aria-hidden="true">&times;</span>
						<span class="sr-only">Close</span>
					</button>
						<?php echo t('<strong>Tip: </strong>This is the foreword area (flex columns). You can place other community venues here.') ?>
						<a class="dialog-launch"
								title="<?php echo t('Click to add a new section.') ?>"
								dialog-append-buttons="true"
								dialog-modal="false"
								dialog-title="New Section"
								dialog-width="80%"
								dialog-height="90%"
								href="/index.php/tools/add_new_section.php<?php echo $common_request_parts . '&areaHandle=MainForeword' ?>">
							<span class="glyphicon glyphicon-circle-plus"></span>
						</a>
				</section>

			</div>
		</section>

<!-- Main area (list of forum areas and forums) -->
		<section class="container-fluid">
			<header>
				<h1><?php echo t('Forums') ?></h1>
			</header>
<?php
	$volumes = ProjectInfo::getAllVolumes ($project_id, $domain_handle);
	foreach ($volumes as $vlm) {
?>
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h2 class="panel-title">
						<a
							href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $vlm['volume_handle']) ?>"><?php echo h5($vlm['volume_name']) ?></a>
						<small style="color:#fafafa;"><?php echo h5($vlm['volume_desc']) ?></small>
					</h2>
				</div>
				<div class="panel-body">
					<ul class="list-group">
<?php
		$parts = ProjectInfo::getAllParts ($project_id, $domain_handle, $vlm['volume_handle']);
		foreach ($parts as $prt) {
			$latest_chapter = ProjectInfo::getLatestChapterInfo ($project_id,
				$domain_handle, $vlm['volume_handle'], $prt['part_handle']);
?>
						<li class="list-group-item">
							<div class="row">
								<div class="col-md-6">
									<span class="badge"><?php echo $prt['nr_chapters'] ?></span>
									<h3 class="list-group-item-heading">
										<a
											href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $vlm['volume_handle'], $prt ['part_handle']) ?>"><?php echo h5($prt['part_name']) ?></a></h3>
									<p class="list-group-item-text"><?php echo h5($prt['part_desc']) ?></p>
								</div>
<?php
			if (count ($latest_chapter) > 0) {
				$author_info = FSEInfo::getNameInfo ($latest_chapter['fse_id']);
?>
								<div class="col-md-6 wrap-on-xs">
									<span class="badge"><?php echo ($latest_chapter['nr_sections'] - 1) ?></span>
									<h4 class="list-group-item-heading">
										<a href="<?php echo "$page_path/" . $vlm['volume_handle'] . '/' . $prt['part_handle'] . '/' . $latest_chapter['chapter_handle'] ?>"><?php echo h5($latest_chapter['chapter_name']) ?></a>
									</h4>
									<p class="list-group-item-text">
										<?php echo FSEInfo::getPersonalHomeLink ($author_info, true) ?>
									</p>
								</div>
<?php
			}
?>
						</li>
<?php
		}
?>
					</ul>
				</div>
				<div class="panel-footer">
				</div>
			</div>
<?php
	}
?>
		</section>

<!-- MainAfterword area: Flex columns -->
		<section class="container-fluid">
			<div class="row row-md-flex row-md-flex-wrap">
<?php
	$a = Area::getOrCreate ($c, 'MainAfterword');
	$blocks = $c->getBlocks ('MainAfterword');
	foreach ($blocks as $block) {
		$block->display ();
	}
?>
				<section class="col-md-6 visible-on-edit-document-right alert alert-info alert-dismissible">
					<button type="button" class="close" data-dismiss="alert">
						<span aria-hidden="true">&times;</span>
						<span class="sr-only">Close</span>
					</button>
					<?php echo t('<strong>Tip: </strong>This is the afterword area (flex columns).') ?>
					<a class="dialog-launch"
							title="<?php echo t('Click to add a new section.') ?>"
							dialog-append-buttons="true"
							dialog-modal="false"
							dialog-title="<?php t('New Section') ?>"
							dialog-width="80%"
							dialog-height="90%"
							href="/index.php/tools/add_new_section.php<?php echo $common_request_parts . '&areaHandle=MainAfterword' ?>">
						<span class="glyphicon glyphicon-circle-plus"></span>
					</a>
				</section>
			</div>
		</section>

	</article>


<?php
	include 'inc/project_footer.php';
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
