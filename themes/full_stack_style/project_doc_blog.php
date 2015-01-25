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

	$bi = ProjectInfo::getBlogInfo ($chapter_handle);
	$author_info = FSEInfo::getBasicProfile ($part_handle);
?>

	<header class="blog-banner-<?php echo $page_style ?>">
		<div class="container">
			<h1>
				<?php echo h5($c->getCollectionName()); ?>
			</h1>
			<p class="lead">
				<?php echo h5($c->getCollectionDescription()); ?>
			</p>
			<p>
				<span class="glyphicon glyphicon-sort"></span>
				<?php echo h5($bi['category']) ?>
				<br/>
				<span class="glyphicon glyphicon-tags"></span>
				<?php foreach ($bi['tags'] as $tag) echo h5($tag['tag']) . ' '; ?>
				<br/>
				<span class="glyphicon glyphicon-pen"></span>
				<?php echo FSEInfo::getPersonalHomeLink ($author_info, true); ?>
				<span class="glyphicon glyphicon-clock"></span>
				<?php
					echo $bi['info']['create_time'] . PHP_EOL;
					if ($bi['info']['update_time'] != $bi['info']['create_time']) {
						echo '<span class="glyphicon glyphicon-edit"></span>' . PHP_EOL;
						echo $bi['info']['update_time'];
					}
				?>
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
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-6 col-md-9 col-lg-9">
<?php
	include 'inc/blog_main_areas.php';
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

	$suggested_blogs = ProjectInfo::getSuggestedBlogs ($project_id, $domain_handle, $volume_handle, $part_handle);
	$normal_blogs = ProjectInfo::getNormalBlogs ($project_id, $domain_handle, $volume_handle, $part_handle);
?>
			</div> <!-- col-md-9 -->

			<div class="col-sm-6 col-md-3 col-lg-3">
				<header>
					<h3>
						<?php echo t('Author Suggested') ?>
					</h3>
				</header>

<?php
	if (count ($suggested_blogs) > 0) {
?>
				<ul class="list-group">
<?php
		foreach ($suggested_blogs as $blg) {
?>
					<li class="list-group-item">
						<p>
							<a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $blg['chapter_handle']); ?>"><?php echo h5($blg['chapter_name']) ?></a>
						</p>
					</li>
<?php
		}
?>
				</ul>
<?php
	}
?>

				<header>
					<h3>
						<?php echo t('Other Blogs') ?>
					</h3>
				</header>
<?php
	if (count ($normal_blogs) > 0) {
?>
				<ul class="list-group">
<?php
		foreach ($normal_blogs as $blg) {
?>
					<li class="list-group-item">
						<p>
							<a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $blg['chapter_handle']); ?>"><?php echo h5($blg['chapter_name']) ?></a>
						</p>
					</li>
<?php
		}
?>
				</ul>
<?php
	}
?>

			</div>

		</div>
	</div>

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
