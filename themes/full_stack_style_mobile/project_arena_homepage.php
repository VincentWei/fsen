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
?>

	<header class="banner-<?php echo $page_style ?>">
		<div class="container">
			<h1>
				<?php echo h5($c->getCollectionName()); ?>
			</h1>
			<p class="lead">
				<?php echo h5($c->getCollectionDescription());?>
			</p>
		</div>
	</header>

	<div class="v-seperator">
	</div>

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
					<button type="button" class="close" data-dismiss="alert">
						<span aria-hidden="true">&times;</span>
						<span class="sr-only">Close</span>
					</button>
					<?php echo t('<strong>Tip: </strong>This is the foreword area (flex columns).') ?>
					<a class="dialog-launch"
							title="<?php echo t('Click to add a new section.') ?>"
							dialog-append-buttons="true"
							dialog-modal="false"
							dialog-title="<?php echo t('New Section') ?>"
							dialog-width="80%"
							dialog-height="90%"
							href="/index.php/tools/add_new_section.php?fsenDocLang=<?php echo $doc_lang ?>&cID=<?php echo $page_id ?>&areaHandle=MainForeword&projectID=<?php echo $project_id ?>&domainHandle=<?php echo $domain_handle ?>&volumeHandle=<?php echo $volume_handle ?>&partHandle=<?php echo $part_handle ?>&chapterHandle=<?php echo $chapter_handle ?>">
						<span class="glyphicon glyphicon-circle-plus"></span>
					</a>
				</section>

			</div>
		</section>

<!-- Main area (list of arena zones and arenas) -->
		<section class="container-fluid">
			<header>
				<h1><?php echo t('Arenas') ?></h1>
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
							dialog-title="<?php echo t('New Section') ?>"
							dialog-width="80%"
							dialog-height="90%"
							href="/index.php/tools/add_new_section.php?fsenDocLang=<?php echo $doc_lang ?>&cID=<?php echo $page_id ?>&areaHandle=MainAfterword&projectID=<?php echo $project_id ?>&domainHandle=<?php echo $domain_handle ?>&volumeHandle=<?php echo $volume_handle ?>&partHandle=<?php echo $part_handle ?>&chapterHandle=<?php echo $chapter_handle ?>">
						<span class="glyphicon glyphicon-circle-plus"></span>
					</a>
				</section>

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
