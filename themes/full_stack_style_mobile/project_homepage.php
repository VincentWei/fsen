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

function output_domain_block ($project_id)
{
	foreach (ProjectInfo::$mDomainList as $domain_handle) {
		if ($domain_handle == 'home' || $domain_handle == 'misc')
			continue;

		$domain_link = ProjectInfo::assemblePath ($project_id, $domain_handle);
		$domain_name = ProjectInfo::getDomainName ($project_id, $domain_handle);
		$domain_desc = ProjectInfo::getDomainDesc ($project_id, $domain_handle);
		switch ($domain_handle) {
		case 'download':
			$icon_name = 'download';
			break;
		case 'document':
			$icon_name = 'book';
			break;
		case 'community':
			$icon_name = 'group';
			break;
		case 'contribute':
			$icon_name = 'heart';
			break;
		}
?>
<div class="col-sm-3 col-md-3 col-lg-3 note-block">
	<div class="well text-center">
		<div class="note-icon">
			<a href="<?php echo $domain_link ?>">
				<span class="glyphicon glyphicon-<?php echo $icon_name ?>"></span>
			</a>
		</div>
		<div class="note-desc">
			<h2>
				<a href="<?php echo $domain_link ?>">
					<?php echo h5($domain_name) ?>
				</a>
			</h2>
			<p>
				<?php echo h5($domain_desc) ?>
			</p>
		</div>
		</p>
	</div>
</div>
<?php
	}
}

?>

<body>

<div class="full-stack">

<?php
	include('inc/header.php');
	include 'inc/project_navbar.php';
	include ('inc/project_alert_banner.php');

	$db = Loader::db ();
	$recent_news = ProjectInfo::getRecentNews ($project_id);
	$nr_news = count ($recent_news);

	$hot_discussions = ProjectInfo::getHotDiscussions ($project_id);
	$nr_discussions= count ($hot_discussions);

	$must_documents = ProjectInfo::getMustDocuments ($project_id);
	$nr_must_documents = count ($nr_must_documents);

	$latest_documents = ProjectInfo::getLatestDocuments ($project_id);
	$nr_latest_documents = count ($latest_documents);

?>
	<section class="big-banner big-banner-<?php echo $page_style ?>" lang="<?php echo $doc_lang ?>">
		<?php  $a = new Area('Banner'); $a->display($c); ?>
	</section>

	<nav class="container" lang="<?php echo $doc_lang ?>">
		<div class="row row-md-flex row-md-flex-wrap">
<?php
	output_domain_block ($project_id);
?>
		</div>
	</nav>

	<article class="formal-content" lang="<?php echo $doc_lang ?>">
		<header class="container-fluid">
			<div class="row">
				<section class="col-md-6">
					<header>
						<h1><?php echo t('Recent News') ?></h1>
					</header>
					<ul class="list-unstyled" style="padding-left:10px;">
<?php
foreach ($recent_news as $cpt) {
	$link = ProjectInfo::assemblePath ($project_id, 'community', 'general', 'news', $cpt['chapter_handle']);
?>
<li>
	<h4 class="text-ellipsis">
		<span class="text-major-<?php echo $page_style ?>">
			<?php echo '[' . date ('Y-m-d', $cpt['create_ctime']) . ']' ?>
		</span>
		<a class="text-outline-<?php echo $page_style ?>" href="<?php echo $link ?>">
			<?php echo h5($cpt['chapter_name']) ?>
		</a>
	</h4>
</li>
<?php
}
?>
					</ul>
				</section>
				<section class="col-md-6">
					<header>
						<h1><?php echo t('Active Discussions') ?></h1>
					</header>
					<ul class="list-unstyled" style="padding-left:10px;">
<?php
foreach ($hot_discussions as $cpt) {
	$link = ProjectInfo::assemblePath ($project_id, 'community', 'general', 'discussion', $cpt['chapter_handle']);
?>
<li>
	<h4 class="text-ellipsis">
		<span class="text-major-<?php echo $page_style ?>">
			<?php echo '[' . date ('Y-m-d', $cpt['create_ctime']) . ']' ?>
		</span>
		<a class="text-outline-<?php echo $page_style ?>" href="<?php echo $link ?>">
			<?php echo h5($cpt['chapter_name']) ?>
		</a>
	</h4>
</li>
<?php
}
?>
					</ul>
				</section>
			</div> <!-- row -->
			<div class="row">
				<section class="col-md-6">
					<header>
						<h1><?php echo t('Docs for Newbies') ?></h1>
					</header>
					<ul class="list-unstyled" style="padding-left:10px;">
<?php
foreach ($must_documents as $cpt) {
	$link = ProjectInfo::assemblePath ($project_id, 'document',
			$cpt['volume_handle'], $cpt['part_handle'], $cpt['chapter_handle']);
?>
<li>
	<h4 class="text-ellipsis">
		<span class="text-major-<?php echo $page_style ?>">
			<?php echo '[' . date ('Y-m-d', $cpt['create_ctime']) . ']' ?>
		</span>
		<a class="text-outline-<?php echo $page_style ?>" href="<?php echo $link ?>">
			<?php echo h5($cpt['chapter_name']) ?>
		</a>
	</h4>
</li>
<?php
}
?>
					</ul>
				</section>
				<section class="col-md-6">
					<header>
						<h1><?php echo t('Latest Documents') ?></h1>
					</header>
					<ul class="list-unstyled" style="padding-left:10px;">
<?php
foreach ($latest_documents as $cpt) {
	$link = ProjectInfo::assemblePath ($project_id, 'document',
			$cpt['volume_handle'], $cpt['part_handle'], $cpt['chapter_handle']);
?>
<li>
	<h4 class="text-ellipsis">
		<span class="text-major-<?php echo $page_style ?>">
			<?php echo '[' . date ('Y-m-d', $cpt['create_ctime']) . ']' ?>
		</span>
		<a class="text-outline-<?php echo $page_style ?>" href="<?php echo $link ?>">
			<?php echo h5($cpt['chapter_name']) ?>
		</a>
	</h4>
</li>
<?php
}
?>
					</ul>
				</section>
			</div>
		</header>

		<section>
			<header>
				<h1>
					<?php echo ProjectInfo::getVolumeName($project_id, 'home', 'feature') ?>
					<span class="section-action visible-on-edit-document-right">
						<a class="launch-modal"
								href="/index.php/tools/add_new_feature.php?fsenDocLang=<?php echo $doc_lang ?>&cID=<?php echo $page_id ?>&areaHandle=Features&projectID=<?php echo $c->getCollectionHandle ()?>&domainHandle=home&volumeHandle=feature&partHandle=na&chapterHandle=na">
							<m class="glyphicon glyphicon-circle-plus"></m>
						</a>
					</span>
				</h1>
			</header>
			<section class="container-fluid">
				<div class="row">
<?php
	$a = Area::getOrCreate ($c, 'Features');
	$blocks = $c->getBlocks ('Features');
	foreach ($blocks as $block) {
		echo '<section class="col-sm-12 col-md-6">';
		$block->display ();
		echo '</section>';
	}
?>
				</div>
			</section>
		</section>

		<section class="container-fluid">
			<div class="row">
			<header>
				<h1><?php echo ProjectInfo::getVolumeName($project_id, 'home', 'overview') ?>
					<span class="section-action visible-on-edit-document-right">
						<a class="dialog-launch"
								dialog-append-buttons="true"
								dialog-modal="false"
								dialog-title="<?php echo t('New Section') ?>"
								dialog-width="80%"
								dialog-height="90%"
								href="/index.php/tools/add_new_section.php?fsenDocLang=<?php echo $doc_lang ?>&cID=<?php echo $page_id ?>&areaHandle=Main&projectID=<?php echo $c->getCollectionHandle ()?>&domainHandle=home&volumeHandle=overview&partHandle=na&chapterHandle=na">
							<m class="glyphicon glyphicon-circle-plus"></m>
						</a>
					</span>
				</h1>
			</header>
			<section>
				<?php  $a = new Area('Main'); $a->display($c); ?>
			</section>
			</div>
		</section>

		<section class="container-fluid">
			<div class="row">
			<header>
<h1><?php echo t('Code Repository') ?></h1>
			</header>
			<?php  $a = new Area('CodeRepository'); $a->display($c); ?>
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
include ('inc/sh-autoloader.php');
Loader::element ('footer_required');
?>

</body>
</html>
