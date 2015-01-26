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

include ('inc/head.php');
include ('inc/project_frags.php');
?>

<body>

<div class="full-stack">

<?php
	include ('inc/header.php');
	include ('inc/project_navbar.php');
	include ('inc/project_alert_banner.php');
?>

	<nav>
		<ol class="breadcrumb">
			<li><a href="<?php echo ProjectInfo::assemblePath ($project_id, 'home') ?>"><?php echo ProjectInfo::getDomainName ($project_id, 'home') ?></a></li>
			<li><a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle) ?>"><?php echo ProjectInfo::getDomainName ($project_id, $domain_handle) ?></a></li>
			<li><a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle) ?>"><?php echo ProjectInfo::getVolumeName ($project_id, $domain_handle, $volume_handle) ?></a></li>
			<li><a href="<?php echo ProjectInfo::assemblePath ($project_id, $domain_handle, $volume_handle, $part_handle) ?>"><?php echo ProjectInfo::getPartName ($project_id, $domain_handle, $volume_handle, $part_handle) ?></a></li>
		</ol>
	</nav>

	<article class="formal-content" lang="<?php echo $doc_lang ?>">
		<div class="container-fluid">
			<div class="row">
				<section class="col-sm-6 col-md-9 col-lg-9">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h2 class="panel-title">
								<?php echo h5($c->getCollectionName()) ?>
							</h2>
						</div>
						<div class="panel-body">
							<?php  $a = new Area('Main'); $a->display($c); ?>
						</div>
						<div class="panel-footer">

<div class="form-group">
	<button type="button" disabled="disabled" class="btn btn-warning enable-on-logged-in"
		data-toggle="collapse" data-target="#formNewReply"
		aria-controls="formNewReply" aria-expanded="false" aria-controls="formNewReply">
		<?php echo t('New Reply') ?>
	</button>
	<p class="help-block hidden-on-logged-in">
		<?php echo t('<a href="/fse_login?redirectURL=%s">Sign in</a> to post your reply.', $page_path) ?>
	</p>
</div>

<fieldset id="formNewReply" class="collapse" data-value="top">
	<form method="post" enctype="multipart/form-data" class="validate"
			action="/fse_settings/projects/add_new_thread_reply" role="form">
		<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
		<input type="hidden" name="cID" value="<?php echo $page_id ?>" />
		<input type="hidden" name="areaHandle" value="Main" />
		<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
		<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
		<input type="hidden" name="volumeHandle" value="<?php echo $volume_handle ?>" />
		<input type="hidden" name="partHandle" value="<?php echo $part_handle ?>" />
		<input type="hidden" name="chapterHandle" value="<?php echo $chapter_handle ?>" />

		<div class="form-group">
			<label for="postSubject" class="sr-only">
				<?php $my_text = t('Title of your reply'); echo $my_text ?>
			</label>
			<input class="form-control" type="text" name="postSubject" maxlength="64"
					placeholder="<?php echo $my_text ?>" />
		</div>

		<div class="form-group">
			<label for="postContent" class="sr-only">
				<?php $my_text = t('Content of your reply'); echo $my_text ?>
			</label>
			<textarea class="form-control"
				name="postContent" rows="5"
				required="true" placeholder="<?php echo $my_text ?>"></textarea>
			<span class="help-block"><?php echo t('Markdown Extra enabled; 20 characters at least.') ?></span>
		</div>

		<div class="form-group">
			<label for="attachedFile" class="sr-only">
				<?php echo t('Attached files') ?>
			</label>
<?php
			$al = Loader::helper ('concrete/asset_library');
			for ($i = 0; $i < ProjectInfo::MAX_ATTACHED_FILES; $i++) {
				echo $al->file("attachmentFile$i", "attachmentFile$i", t('Upload or Choose File'));
			}
?>
		</div>

		<input class="btn btn-default" type="submit"
			value="<?php echo t('Publish') ?>" />
	</form>
</fieldset>

					</div>
				</section>
				<section class="col-sm-6 col-md-3 col-lg-3">
					<ul class="list-group">
						<li class="list-group-item list-group-item-info">
							<?php echo t('Top Threads') ?>
						</li>
<?php
	$top_threads = ProjectInfo::getTopThreads ($project_id, $domain_handle, $volume_handle, $part_handle);
	foreach ($top_threads as $thd) {
?>
						<li class="list-group-item">
							<span class="badge"><?php echo ($thd ['nr_sections'] - 1) ?></span>
<?php
		if ($thd ['chapter_handle'] == $chapter_handle) {
			echo h5($thd ['chapter_name']) . PHP_EOL;
		}
		else {
			echo '<a href="' . $thd ['chapter_handle'] . '">' . h5($thd ['chapter_name']) . '</a>' . PHP_EOL;
		}
?>
						</li>
<?php
	}
?>
					</ul>
					<ul class="list-group">
						<li class="list-group-item list-group-item-info">
							<?php echo t('Normal Threads') ?>
						</li>
<?php
	$normal_threads = ProjectInfo::getNormalThreads ($project_id, $domain_handle, $volume_handle, $part_handle);
	foreach ($normal_threads as $thd) {
?>
						<li class="list-group-item">
							<span class="badge"><?php echo ($thd ['nr_sections'] - 1) ?></span>
<?php
		if ($thd ['chapter_handle'] == $chapter_handle) {
			echo h5($thd ['chapter_name']) . PHP_EOL;
		}
		else {
			echo '<a href="' . $thd ['chapter_handle'] . '">' . h5($thd ['chapter_name']) . '</a>' . PHP_EOL;
		}
?>
						</li>
<?php
	}
?>
					</ul>
				</section>
			</div>
		</div>
	</article>

<?php
	include 'inc/project_footer.php';
	include ('inc/footer.php');
	include ('inc/status-bar.php');
?>

</div>

<?php
include ('inc/dynamic-nav.php');
include ('inc/sh-autoloader.php');
Loader::element ('footer_required');
?>

</body>
</html>
