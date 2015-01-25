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

	$request_parts = "?fsenDocLang=$doc_lang&cID=$page_id&projectID=$project_id&domainHandle=$domain_handle&volumeHandle=$volume_handle&partHandle=$part_handle";

	$suggested_blogs = ProjectInfo::getSuggestedBlogs ($project_id, $domain_handle, $volume_handle, $part_handle);
	$normal_blogs = ProjectInfo::getNormalBlogs ($project_id, $domain_handle, $volume_handle, $part_handle);
?>
	<article class="formal-content" lang="<?php echo $doc_lang ?>">
		<section class="container-fluid">
			<div class="row">
				<div class="col-md-8">
					<header>
						<h1>
							<?php echo t('Author Suggested') ?>
							<span class="section-action visible-for-specific-user user-<?php echo $part_handle ?>">
								<a class="launch-modal"
										href="/index.php/tools/add_blog.php<?php echo $request_parts ?>">
									<m class="glyphicon glyphicon-circle-plus"></m></a>
							</span>
						</h1>
					</header>

<?php
	if (count ($suggested_blogs) > 0) {
?>
					<ul class="list-group">
<?php
		foreach ($suggested_blogs as $blg) {
?>
						<li class="list-group-item">
							<h3>
								<a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $blg['chapter_handle']); ?>"><?php echo h5($blg['chapter_name']) ?></a>
								<span class="section-action visible-for-specific-user user-<?php echo $part_handle ?>"
										style="margin-top: 0; margin-right: 5px;">
									<a class="launch-modal"
											href="/index.php/tools/delete_blog.php<?php echo $request_parts ?>&chapterHandle=<?php echo $blg['chapter_handle'] ?>">
										<m class="glyphicon glyphicon-circle-remove"></m>
									</a>
								</span>
								<span class="section-action visible-for-specific-user user-<?php echo $part_handle ?>"
										style="margin-top: 0; margin-right: 5px;">
									<a class="launch-modal"
											href="/index.php/tools/edit_blog.php<?php echo $request_parts ?>&chapterHandle=<?php echo $blg['chapter_handle'] ?>">
										<m class="glyphicon glyphicon-edit"></m>
									</a>
								</span>
							</h3>
							<p><?php echo h5($blg['chapter_desc']) ?></p>
						</li>
<?php
		}
?>
					</ul>
<?php
	}
?>

					<header>
						<h1>
							<?php echo t('Other Blogs') ?>
							<span class="section-action visible-for-specific-user user-<?php echo $part_handle ?>">
								<a class="launch-modal"
										href="/index.php/tools/add_blog.php<?php echo $request_parts ?>">
									<m class="glyphicon glyphicon-circle-plus"></m></a>
							</span>
						</h1>
					</header>
<?php
	if (count ($normal_blogs) > 0) {
?>
					<ul class="list-group">
<?php
		foreach ($normal_blogs as $blg) {
?>
						<li class="list-group-item">
							<h3>
								<a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle, $blg['chapter_handle']); ?>"><?php echo h5($blg['chapter_name']) ?></a>
								<span class="section-action visible-for-specific-user user-<?php echo $part_handle ?>"
										style="margin-top: 0; margin-right: 5px;">
									<a class="launch-modal"
											href="/index.php/tools/delete_blog.php<?php echo $request_parts ?>&chapterHandle=<?php echo $blg['chapter_handle'] ?>">
										<m class="glyphicon glyphicon-circle-remove"></m>
									</a>
								</span>
								<span class="section-action visible-for-specific-user user-<?php echo $part_handle ?>"
										style="margin-top: 0; margin-right: 5px;">
									<a class="launch-modal"
											href="/index.php/tools/edit_blog.php<?php echo $request_parts ?>&chapterHandle=<?php echo $blg['chapter_handle'] ?>">
										<m class="glyphicon glyphicon-edit"></m>
									</a>
								</span>
							</h3>
							<p><?php echo h5($blg['chapter_desc']) ?></p>
						</li>
<?php
		}
?>
					</ul>
<?php
	}
?>

<?php
	/* blog zone navigation */
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
					<nav>
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
				</div><!-- col-md-8 -->

				<!-- right side bar -->
				<div class="col-md-4">
				</div><!-- col-md-4 -->
			</div><!-- row -->
		</section> <!--container-fluid -->
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
