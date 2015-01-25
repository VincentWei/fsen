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

	<nav>
		<ol class="breadcrumb">
			<li><a href="<?php echo ProjectInfo::assemblePath ($project_id, 'home') ?>"><?php echo ProjectInfo::getDomainName ($project_id, 'home') ?></a></li>
			<li><a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle) ?>"><?php echo ProjectInfo::getDomainName ($project_id, $domain_handle) ?></a></li>
			<li><a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle) ?>"><?php echo ProjectInfo::getVolumeName ($project_id, $domain_handle, $volume_handle) ?></a></li>
			<li class="active"><?php echo ProjectInfo::getPartName ($project_id, $domain_handle, $volume_handle, $part_handle) ?></li>
		</ol>
	</nav>

	<article class="formal-content" lang="<?php echo $doc_lang ?>">

		<section class="container-fluid">

<?php
	$prt = ProjectInfo::getPartInfo ($project_id, $domain_handle, $volume_handle, $part_handle);
?>
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h2 class="panel-title">
						<?php echo h5($prt['part_name']) ?>
					</h2>
				</div>
				<div class="panel-footer">

<div class="form-group">
	<button type="button" disabled="disabled" class="btn btn-warning enable-on-logged-in"
		data-toggle="collapse" data-target="#formNewThread"
		aria-controls="formNewThread" aria-expanded="false" aria-controls="formNewThread">
		<?php echo t('New Question') ?>
	</button>
	<p class="help-block hidden-on-logged-in">
		<?php echo t('<a href="/fse_login?redirectURL=%s">Sign in</a> to start a new question.', $page_path) ?>
	</p>
</div>

<fieldset id="formNewThread" class="collapse">
	<form method="post" enctype="multipart/form-data" class="validate"
			action="<?php echo "/fse_settings/projects/add_new_forum_thread" ?>" role="form">
		<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
		<input type="hidden" name="cID" value="<?php echo $page_id ?>" />
		<input type="hidden" name="areaHandle" value="Main" />
		<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
		<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
		<input type="hidden" name="volumeHandle" value="<?php echo $volume_handle ?>" />
		<input type="hidden" name="partHandle" value="<?php echo $part_handle ?>" />
		<input type="hidden" name="postType" value="question" />

		<div class="form-group">
			<label for="threadSubject" class="sr-only">
				<?php $my_text = t('Title of your question'); echo $my_text ?>
			</label>
			<input type="text" class="form-control" name="threadSubject"
				required="true" pattern=".{4,64}" placeholder="<?php echo $my_text ?>" />
		</div>

		<div class="form-group">
			<label for="postContent" class="sr-only">
				<?php $my_text = t('Content of your question'); echo $my_text ?>
			</label>
			<textarea class="form-control"
				name="postContent" rows="3"
				required="true" placeholder="<?php echo $my_text ?>"></textarea>
			<p class="help-block">
				<?php echo t('Markdown Extra enabled; 20 characters at least.'); ?>
			</p>
		</div>

		<div class="form-group">
			<label for="attachedFile" class="sr-only">
				<?php echo t('Attached files'); ?>
			</label>
		</div>

		<input class="btn btn-default" type="submit"
			value="<?php echo t('Publish') ?>" />
	</form>
</fieldset>

				</div>
				<div class="panel-body">
					<ul class="list-group">
						<li class="list-group-item list-group-item-info">
							<?php echo t('Top Q&amp;A') ?>
						</li>
<?php
	$top_threads = ProjectInfo::getTopThreads ($project_id, $domain_handle, $volume_handle, $part_handle);
	foreach ($top_threads as $thd) {
?>
						<li class="list-group-item">
<div class="row">
	<div class="col-md-8">
		<a href="<?php echo "$page_path/" . $thd ['chapter_handle'] ?>"><?php echo h5($thd ['chapter_name']) ?></a>
		<span class="section-action visible-on-manage-community-right"
				style="margin-top: 0;">
			<a class="launch-modal"
					href="/index.php/tools/thread_action.php?fsenDocLang=<?php echo $doc_lang ?>&cID=<?php echo $page_id ?>&projectID=<?php echo $project_id ?>&domainHandle=<?php echo $domain_handle ?>&volumeHandle=<?php echo $volume_handle ?>&partHandle=<?php echo $part_handle ?>&chapterHandle=<?php echo $thd['chapter_handle'] ?>&threadAction=delete">
				<m class="glyphicon glyphicon-circle-remove"></m>
			</a>
		</span>
		<span
				class="section-action visible-on-manage-community-right"
				style="margin-top: 0; margin-right: 5px;">
			<a class="launch-modal"
					href="/index.php/tools/thread_action.php?fsenDocLang=<?php echo $doc_lang ?>&cID=<?php echo $page_id ?>&projectID=<?php $project_id ?>&domainHandle=<?php $domain_handle ?>&volumeHandle=<?php $volume_handle ?>&partHandle=<?php $part_handle ?>&chapterHandle=<?php echo $thd['chapter_handle'] ?>&threadAction=untop">
				<m class="glyphicon glyphicon-down-arrow"></m>
			</a>
		</span>
		<br/>
<?php
		$author_name_info = FSEInfo::getNameInfo ($thd['fse_id']);
		$publish_date = date ('Y-m-d', $thd['create_ctime']);
?>
		<small><?php echo FSEInfo::getPersonalHomeLink ($author_name_info, true) ?><br/><?php echo $publish_date ?></small>
	</div>
	<div class="col-md-4 wrap-on-xs">
		<?php echo t('Replies #: ') . ($thd['nr_sections'] - 1) ?><br/>
<?php
		if ($thd['nr_sections'] > 1) {
			$last_section = ProjectInfo::getLastSectionInfo ($project_id,
					$domain_handle, $volume_handle, $part_handle, $thd['chapter_handle']);
			$author_name_info = FSEInfo::getNameInfo ($last_section ['author_id']);
			$publish_date = date ('Y-m-d H:i', $last_section['create_ctime']);
?>
		<small><?php echo FSEInfo::getPersonalHomeLink ($author_name_info, true) ?><br/><?php echo $publish_date ?></small>
<?php
		}
?>
	</div>
</div>
						</li>
<?php
	}
?>
					</ul>
					<ul class="list-group">
						<li class="list-group-item list-group-item-info">
							<?php echo t('Normal Q&amp;A') ?>
						</li>
<?php
	$normal_threads = ProjectInfo::getNormalThreads ($project_id, $domain_handle, $volume_handle, $part_handle);
	foreach ($normal_threads as $thd) {
?>
						<li class="list-group-item">
<div class="row">
	<div class="col-md-8">
		<a href="<?php echo "$page_path/" . $thd ['chapter_handle'] ?>"><?php echo h5($thd ['chapter_name']) ?></a>
		<span class="section-action visible-on-manage-community-right"
				style="margin-top: 0;">
			<a class="launch-modal"
					href="/index.php/tools/thread_action.php?fsenDocLang=<?php echo $doc_lang ?>&cID=<?php echo $page_id ?>&projectID=<?php echo $project_id ?>&domainHandle=<?php echo $domain_handle ?>&volumeHandle=<?php echo $volume_handle ?>&partHandle=<?php echo $part_handle ?>&chapterHandle=<?php echo $thd['chapter_handle'] ?>&threadAction=delete">
				<m class="glyphicon glyphicon-circle-remove"></m>
			</a>
		</span>
		<span class="section-action visible-on-manage-community-right"
				style="margin-top: 0; margin-right: 5px;">
			<a class="launch-modal"
					href="/index.php/tools/thread_action.php?fsenDocLang=<?php echo $doc_lang ?>&cID=<?php echo $page_id ?>&projectID=<?php $project_id ?>&domainHandle=<?php $domain_handle ?>&volumeHandle=<?php $volume_handle ?>&partHandle=<?php $part_handle ?>&chapterHandle=<?php echo $thd['chapter_handle'] ?>&threadAction=top">
				<m class="glyphicon glyphicon-up-arrow"></m>
			</a>
		</span>
		<br/>
<?php
		$author_name_info = FSEInfo::getNameInfo ($thd['fse_id']);
		$publish_date = date ('Y-m-d', $thd['create_ctime']);
?>
		<small><?php echo FSEInfo::getPersonalHomeLink ($author_name_info, true) ?><br/><?php echo $publish_date ?></small>
	</div>
	<div class="col-md-4 wrap-on-xs">
		<?php echo t('Replies #: ') . ($thd['nr_sections'] - 1) ?><br/>
<?php
		if ($thd['nr_sections'] > 1) {
			$last_section = ProjectInfo::getLastSectionInfo ($project_id,
					$domain_handle, $volume_handle, $part_handle, $thd['chapter_handle']);
			$author_name_info = FSEInfo::getNameInfo ($last_section ['author_id']);
			$publish_date = date ('Y-m-d H:i', $last_section['create_ctime']);
?>
		<small><?php echo FSEInfo::getPersonalHomeLink ($author_name_info, true) ?><br/><?php echo $publish_date ?></small>
<?php
		}
?>
	</div>
</div>
						</li>
<?php
	}
?>
					</ul>
				</div>
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
