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
				<?php echo h5($c->getCollectionDescription()); ?>
			</p>
		</div>
	</header>

	<div class="v-seperator">
	</div>

	<article class="formal-content" lang="<?php echo $doc_lang ?>">
		<section class="container-fluid">
			<div class="row">
				<div class="col-md-8">
<?php
	$top_blogs = ProjectInfo::getTopBlogs ($doc_lang);
	if (count ($top_blogs) > 0) {
?>
			<header>
				<h1>
					<?php echo t('Top Blogs') ?>
				</h1>
			</header>

			<ul class="list-group">
<?php
		foreach ($top_blogs as $blg) {
?>
				<li class="list-group-item">
					<h3 class="list-group-item-heading">
						<a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $blg['part_handle'], $blg['chapter_handle']); ?>"><?php echo h5($blg['chapter_name']) ?></a>
					</h3>
					<h4 class="list-group-item-heading">
						<a href="<?php echo "/$doc_lang/blog/" . $blg ['part_handle'] ?>">
							<?php $author_info = FSEInfo::getBasicProfile ($blg['part_handle']); echo h5($author_info ['nick_name']) ?>
						</a>
					</h4>
					<p><?php echo h5($blg['chapter_desc']) ?></p>
				</li>
<?php
		}
?>
			</ul>

<?php
	}

	$suggested_blogs = ProjectInfo::getAllSuggestedBlogs ($doc_lang);
	if (count ($suggested_blogs) > 0) {
?>
			<header>
				<h1>
					<?php echo t('Author Suggested Blogs') ?>
				</h1>
			</header>

			<ul class="list-group">
<?php
		foreach ($suggested_blogs as $blg) {
?>
				<li class="list-group-item">
					<h3 class="list-group-item-heading">
						<a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $blg['part_handle'], $blg['chapter_handle']); ?>"><?php echo h5($blg['chapter_name']) ?></a>
					</h3>
					<h4 class="list-group-item-heading">
						<a href="<?php echo "/$doc_lang/blog/" . $blg ['part_handle'] ?>">
							<?php $author_info = FSEInfo::getBasicProfile ($blg['part_handle']); echo h5($author_info ['nick_name']) ?>
						</a>
					</h4>
					<p><?php echo h5($blg['chapter_desc']) ?></p>
				</li>
<?php
		}
?>
			</ul>

<?php
	}
?>

				</div><!-- col-md-8 -->
				<div class="col-md-4">
<?php
	$top_authors = ProjectInfo::getTopBlogAuthors ($doc_lang);
	if (count ($top_authors) > 0) {
?>
			<header>
				<h1>
					<?php echo t('Top Blog Authors') ?>
				</h1>
			</header>

			<ul class="list-group">
<?php
		foreach ($top_authors as $ta) {
?>
				<li class="list-group-item">
					<h4 class="list-group-item-heading">
						<a href="<?php echo "/$doc_lang/blog/" . $ta ['part_handle'] ?>">
							<?php $author_info = FSEInfo::getBasicProfile ($ta['part_handle']); echo h5($author_info ['nick_name']) ?>
						</a>
					</h4>
				</li>
<?php
		}
?>
			</ul>

<?php
	}
?>

				</div><!-- col-md-4 -->
			</div>
		</section>
	</article>

<?php
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
