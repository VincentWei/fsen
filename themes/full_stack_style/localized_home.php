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

require_once ('helpers/fsen/FSEInfo.php');
require_once ('helpers/fsen/ProjectInfo.php');
require_once ('helpers/fsen/DocSectionManager.php');

include('inc/head.php');

$doc_lang = $this->controller->get ('fsenDocLang');
if (!isset ($doc_lang)) {
	$doc_lang = 'en';
}
?>

<body>

<div class="full-stack">

<?php
	include('inc/header.php');

	$db = Loader::db();
	$top_projects = Cache::get ('TopProjects', $doc_lang);
	if ($top_projects == false) {
		$top_projects = $db->getAll ("SELECT project_id, name, heat_level FROM fsen_projects
	WHERE project_id LIKE '%-$doc_lang' AND project_id NOT LIKE 'sys-__'
	ORDER BY heat_level DESC LIMIT 5");
		Cache::set ('TopProjects', $doc_lang, $top_projects, 60*60*24);
	}

	$top_blogs = Cache::get ('TopBlogs', $doc_lang);
	if ($top_blogs == false) {
		$top_blogs = $db->getAll ("SELECT part_handle, chapter_handle, chapter_name, heat_level
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE project_id='sys-$doc_lang' AND domain_handle='document' AND volume_handle='blog'
	ORDER BY heat_level DESC LIMIT 5");
		Cache::set ('TopBlogs', $doc_lang, $top_blogs, 60*60*24);
	}

	$hot_discussions = Cache::get ('HotDiscussions', $doc_lang);
	if ($hot_dicussions == false) {
		$hot_discussions = $db->getAll ("SELECT project_id, domain_handle, volume_handle, part_handle,
		chapter_handle, chapter_name, chapter_desc, heat_level, nr_sections
	FROM fsen_project_doc_volume_part_chapters_$doc_lang
	WHERE domain_handle='community' ORDER BY heat_level DESC LIMIT 5");
		Cache::set ('HotDiscussions', $doc_lang, $hot_discussions, 60*10);
	}

	$latest_posts = Cache::get ('LatestPosts', $doc_lang);
	if ($lateset_posts == false) {
		$latest_posts = $db->getAll ("SELECT project_id, domain_handle, volume_handle, part_handle, chapter_handle,
		id, page_id, curr_ver_code, heat_level
	FROM fsen_document_sections_$doc_lang ORDER BY create_time DESC LIMIT 20");
		Cache::set ('LatestPosts', $doc_lang, $latest_posts, 60*10);
	}

	$hot_commented_posts = Cache::get ('HotCommentedPosts', $doc_lang);
	if ($hot_commented_posts == false) {
		$hot_commented_posts = $db->getAll ("SELECT id, author_id, curr_ver_code, page_id,
		nr_comments, nr_praise, nr_favorites, heat_level,
		project_id, domain_handle, volume_handle, part_handle, chapter_handle
	FROM fsen_document_sections_$doc_lang
	ORDER BY heat_level DESC, create_time DESC LIMIT 10");
		Cache::set ('HotCommentedPosts', $doc_lang, $hot_commented_posts, 60*5);
	}

?>

<section class="banner-global">
	<div class="container">
		<h1>
			<?php echo t('Full Stack Engineer?!') ?>
		</h1>
		<p class="lead">
			<?php echo t('We help you to be a full stack engineer and promote your credits!') ?>
			<a href="/<?php echo t('zh') ?>" class="btn btn-outline-inverse btn-sm pull-right"><?php echo t('中文版') ?></a>
		</p>
	</div>
</section>

<div class="v-seperator">
</div>

<div class="container">
	<section id="sectionIntroduction" class="alert alert-major-info fade in alert-dismissible" style="display:none;">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<h3>
			<?php echo t('WHY YOU ARE HERE?') ?>
		</h3>
		<ul>
			<li>
				<?php echo t('You are an open source project developer. Or,') ?>
			</li>
			<li>
				<?php echo t('You are a software engineer. And, ') ?>
			</li>
			<li>
				<?php echo t('You want to be a Full Stack Engineer!') ?>
			</li>
		</ul>

		<h3>
			<?php echo t('SERVICES WE PROVIDE') ?>
		</h3>

		<ul>
			<li>
				<?php echo t('Hosting your open source project webpages.') ?>
			</li>
			<li>
				<?php echo t('Writting your technology blogs. ') ?>
			</li>
			<li>
				<?php echo t('Involving in open source projects and discuss things with others.') ?>
			</li>
			<li>
				<?php echo t('Asking questions and/or answering questions.') ?>
			</li>
		</ul>

		<h3>
			<?php echo t('ONE MORE THING') ?>
		</h3>
		<ul>
			<li>
				<?php echo t('This site is a <strong>PURE</strong> and <strong>CLEAN</strong> open source project, which is being developed by some from you!') ?>
			</li>
			<li>
				<?php echo t('THERE IS NO ANY ADS ON THIS SITE!') ?>
			</li>
		</ul>
		<div class="v-seperator">
		</div>
		<p class="lead block-right">
			<a href="<?php echo "/$doc_lang/project/fsen-$doc_lang" ?>" class="btn btn-primary btn-lg"><?php echo t('Visit FSEN Project') ?></a>
		</p>
	</section> <!-- #sectionIntroduction -->
</div> <!-- container -->

<article class="formal-content">

	<div class="container">
		<div class="row">
			<div class="col-md-4">
				<ul class="list-group">
					<li class="list-group-item list-group-item-info">
						<?php echo t('Top Projects') ?>
					</li>
<?php
		foreach ($top_projects as $prj) {
			$link = "/$doc_lang/project/" . $prj['project_id'];
?>
					<li class="list-group-item">
						<span class="badge"><?php echo $prj['heat_level'] ?></span>
						<a href="<?php echo $link ?>"><?php echo h5($prj['name']) ?></a>
					</li>
<?php
		}
?>
				</ul>
			</div>
			<div class="col-md-4">
				<ul class="list-group">
					<li class="list-group-item list-group-item-info">
						<?php echo t('Top Blogs') ?>
					</li>
<?php
		foreach ($top_blogs as $blg) {
			$link = ProjectInfo::assemblePath ("sys-$doc_lang", 'document',
					'blog', $blg['part_handle'], $blg['chapter_handle']);
?>
					<li class="list-group-item">
						<span class="badge"><?php echo $blg['heat_level'] ?></span>
						<a href="<?php echo $link ?>"><?php echo h5($blg['chapter_name']) ?></a>
					</li>
<?php
		}
?>
				</ul>
			</div>
			<div class="col-md-4">
				<ul class="list-group">
					<li class="list-group-item list-group-item-info">
						<?php echo t('Hot Disucssions') ?>
					</li>
<?php
		foreach ($hot_discussions as $cpt) {
			$link = ProjectInfo::assemblePath ($cpt['project_id'], $cpt['domain_handle'],
					$cpt['volume_handle'], $cpt['part_handle'], $cpt['chapter_handle']);
?>
					<li class="list-group-item">
						<span class="badge"><?php echo $cpt['heat_level'] ?></span>
						<a href="<?php echo $link ?>"><?php echo h5($cpt['chapter_name']) ?></a>
					</li>
<?php
		}
?>
				</ul>
			</div>
		</div><!-- row -->
	</div><!-- container -->

	<div class="container-fluid">
		<div class="row">
			<div class="col-md-6">
				<h1>
					<?php echo t('Hot Commented Posts') ?>
				</h1>
<?php
	foreach ($hot_commented_posts as $pst) {
		$author_info = FSEInfo::getNameInfo ($pst['author_id']);
		if ($author_info == false) {
			continue;
		}

		$comments = DocSectionManager::getCachedComments (false, $pst['id']);
		if (count ($comments) == 0) {
			continue;
		}

		$plain_content = DocSectionManager::getPlainContent ($pst['id'], $pst['curr_ver_code']);
		$link = ProjectInfo::assemblePath ($pst['project_id'], $pst['domain_handle'],
					$pst['volume_handle'], $pst['part_handle'], $pst['chapter_handle']);
		$link .= '#section-' . $pst['id'];
		if (strlen ($plain_content['title']) == 0) {
			$page = Page::getByID ($pst['page_id']);
			$plain_content['title'] = $page->getCollectionName ();
		}
?>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="media" style="margin-top:15px">
			<a class="media-left" href="<?php echo FSEInfo::getPersonalHomeLink($author_info) ?>">
				<img class="middle-avatar" src="<?php echo $author_info['avatar_url'] ?>"
						alt="<?php echo h5($author_info['nick_name']) ?>">
			</a>
			<div class="media-body" style="width:100%">
				<h4 class="media-heading">
					<span class="badge"><?php echo $pst['heat_level'] ?></span>
					<a href="<?php echo $link ?>"><?php echo h5($plain_content['title']) ?></a>
				</h4>
				<p>
					<?php echo $plain_content['content'] ?>
				</p>
				<ul class="text-right list-unstyled">
					<li class="inline-list">
						<a data-toggle="collapse" href="#divCommentsFor<?php echo $pst['id'] ?>"
								aria-expanded="false" aria-controls="divCommentsFor<?php echo $pst['id'] ?>">
							<span class="glyphicon glyphicon-comments"></span>
							<?php echo $pst['nr_comments'] ?>
						</a>
					</li>
					<li class="inline-list">
						<span class="glyphicon glyphicon-thumbs-up text-outline-default"></span>
						<span class="text-outline-default"><?php echo $pst['nr_praise'] ?></span>
					</li>
					<li class="inline-list">
						<span class="glyphicon glyphicon-heart text-outline-default"></span>
						<span class="text-outline-default"><?php echo $pst['nr_favorites'] ?></span>
					</li>
				</ul>
				<div class="collapse" id="divCommentsFor<?php echo $pst['id'] ?>">
					<hr/>
<?php
		foreach ($comments as $comment) {
			$author_info = FSEInfo::getNameInfo ($comment['author_id']);
			if ($author_info == false) {
				continue;
			}

			$replied_name_info = false;
			if (preg_match ("/^[0-9a-f]{32}$/", $comment ['replied_author_id'])) {
				$replied_name_info = FSEInfo::getNameInfo ($comment ['replied_author_id']);
			}
?>
				<div class="media" style="margin-top:5px;">
					<a class="media-left" href="<?php echo FSEInfo::getPersonalHomeLink($author_info) ?>">
						<img class="small-avatar" src="<?php echo $author_info['avatar_url'] ?>"
								alt="<?php echo h5($author_info['nick_name']) ?>">
					</a>
					<div class="media-body">
						<p style="line-height: 1; margin-top:0; margin-bottom:0;">
							<small><strong class="text-info"><?php echo h5($author_info['nick_name']) ?></strong></small>
						</p>
						<p style="line-height: 1; margin-top:0; margin-bottom:0;">
							<small>
<?php
			if ($replied_name_info != false) {
				$tmp = t('Reply to ') . '<strong class="text-info">' . $replied_name_info['nick_name'] . ': </strong>';
				echo $tmp;
			}
			echo h5($comment['body']) . '</small>';
?>
						</p>
					</div>
				</div>
<?php
		}
?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	}
?>
			</div>
			<div class="col-md-6">
				<h1>
					<?php echo t('Latest Posts') ?>
				</h1>
				<ul class="list-group">
<?php
		foreach ($latest_posts as $pst) {
			$plain_content = DocSectionManager::getPlainContent ($pst['id'], $pst['curr_ver_code']);
			$link = ProjectInfo::assemblePath ($pst['project_id'], $pst['domain_handle'],
					$pst['volume_handle'], $pst['part_handle'], $pst['chapter_handle']);
			$link .= '#section-' . $pst['id'];
			if (strlen ($plain_content['title']) == 0) {
				$page = Page::getByID ($pst['page_id']);
				$plain_content['title'] = $page->getCollectionName ();
			}
?>
<li class="list-group-item">
	<span class="badge"><?php echo $pst['heat_level'] ?></span>
	<h4 class="list-group-item-heading"><a href="<?php echo $link ?>"><?php echo h5($plain_content['title']) ?></a></h4>
	<p><?php echo $plain_content['content'] ?></p>
</li>
<?php
		}
?>
				</ul>
			</div>
		</div>
	</div>

	<div class="v-seperator">
	</div>

	<div class="container-fluid">
		<section>
			<div class="row">
				<div class="col-md-8">
				</div>
			</div>
		</section>
	</div>

</article>

<?php
	include('inc/footer.php');
?>

<script lang="javascript">
$(document).ready (function() {
	$('#sectionIntroduction').alert();

	if (typeof (localStorage) != "undefined") {
		if (localStorage.getItem ("fsenNoIntroductionAlert") != "true") {
			$('#sectionIntroduction').show();
		}
	}
	else {
		$('#sectionIntroduction').show();
	}

	$('#sectionIntroduction').on('closed.bs.alert', function () {
		if (typeof (localStorage) != "undefined") {
			localStorage.setItem ("fsenNoIntroductionAlert", "true");
		}
	});
});

</script>

</div><!-- .full-stack -->

<?php
include('inc/dynamic-nav.php');
Loader::element ('footer_required');
?>

</body>
</html>
