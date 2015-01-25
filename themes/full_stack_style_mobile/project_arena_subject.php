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

include ('inc/head.php');
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

	$db = Loader::db ();
	$section_question = $db->getRow ("SELECT block_id, nr_comments, nr_praise FROM fsen_document_sections_$doc_lang
	WHERE page_id=? AND area_handle='Main'", array ($page_id));

	$section_answers = $db->getAll ("SELECT block_id, nr_comments, nr_praise FROM fsen_document_sections_$doc_lang
	WHERE page_id=? AND area_handle='Answers' ORDER BY display_order DESC, update_time DESC LIMIT 20",
			array ($page_id));
	$nr_answers = count ($section_answers);

	$section_notes = $db->getAll ("SELECT block_id, nr_comments, nr_praise FROM fsen_document_sections_$doc_lang
	WHERE page_id=? AND area_handle='Notes'  ORDER BY display_order DESC, update_time DESC LIMIT 20",
			array ($page_id));
	$nr_notes = count ($section_notes);

	$section_references = $db->getAll ("SELECT block_id, nr_comments, nr_praise FROM fsen_document_sections_$doc_lang
	WHERE page_id=? AND area_handle='References'  ORDER BY display_order DESC, update_time DESC LIMIT 20",
			array ($page_id));
	$nr_references = count ($section_references);

	$form_token_name = 'formTokenFor' . $chapter_handle;
	$form_token_value = hash_hmac ('md5', time (), $chapter_handle);
	$_SESSION [$form_token_name] = $form_token;

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
				<section class="col-sm-6 col-md-9 col-md-9">
					<div class="row">
						<div class="col-md-1">
							<span class="glyphicon glyphicon-circle-question-mark big-glyph text-primary"></span>
						</div>
						<div class="col-md-11">
<?php
	$am = Area::getOrCreate ($c, 'Main');
	if ($section_question ['block_id'] > 0) {
		$b = Block::getByID ($section_question ['block_id'], $c, $am);
		$b->display ();
	}
?>
						</div>
					</div> <!-- row -->

<?php
	$aa = Area::getOrCreate ($c, 'Answers');
	if ($nr_answers > 0) {
?>
					<div class="row">
						<div class="col-md-1">
							<span class="glyphicon glyphicon-ok-2 big-glyph text-success"></span>
							<p class="text-center text-success lead">
								<?php echo $section_answers[0]['nr_praise']?>
							</p>
						</div>
						<div class="col-md-11">
<?php
		$b = Block::getByID ($section_answers [0]['block_id'], $c, $aa);
		if ($b instanceof Block) {
			$b->display ();
		}
?>
						</div>
					</div> <!-- row -->
<?php
	}

	$an = Area::getOrCreate ($c, 'Notes');
	if ($nr_notes > 0) {
?>
					<div class="row">
						<div class="col-md-1">
							<span class="glyphicon glyphicon-circle-info big-glyph text-info"></span>
							<p class="text-center text-info lead">
								<?php echo $section_notes[0]['nr_praise']?>
							</p>
						</div>
						<div class="col-md-11">
<?php
		$b = Block::getByID ($section_notes [0]['block_id'], $c, $an);
		if ($b instanceof Block) {
			$b->display ();
		}
?>
						</div>
					</div> <!-- row -->
<?php
	}

	$ar = Area::getOrCreate ($c, 'References');
	if ($nr_references > 0) {
?>
					<div class="row">
						<div class="col-md-1">
							<span class="glyphicon glyphicon-link big-glyph text-warning"></span>
							<p class="text-center text-warning lead">
								<?php echo $section_references[0]['nr_praise']?>
							</p>
						</div>
						<div class="col-md-11">

<?php
		$b = Block::getByID ($section_references [0]['block_id'], $c, $ar);
		if ($b instanceof Block) {
			$b->display ();
		}
?>
						</div>
					</div> <!-- .row -->
<?php
	}
?>
					<div class="panel panel-success">
						<div class="panel-body">
							<!-- Nav tabs -->
							<ul class="nav nav-tabs" role="tablist">
								<li role="presentation" class="active">
									<a href="#divAnswers" role="tab" data-toggle="tab">
										<span class="glyphicon glyphicon-ok"></span>
										<span class="hidden-xs"><?php echo t('Other Answers') ?></span>
<?php
	if ($nr_answers > 1) {
?>
										<span class="badge"><?php echo ($nr_answers - 1) ?></span>
<?php
	}
?>
									</a>
								</li>
								<li role="presentation">
									<a href="#divNotes" role="tab" data-toggle="tab">
										<span class="glyphicon glyphicon-circle-info"></span>
										<span class="hidden-xs"><?php echo t('Other Notes') ?></span>
<?php
	if ($nr_notes > 1) {
?>
										<span class="badge"><?php echo ($nr_notes - 1) ?></span>
<?php
	}
?>
									</a>
								</li>
								<li role="presentation">
									<a href="#divReferences" role="tab" data-toggle="tab">
										<span class="glyphicon glyphicon-link"></span>
										<span class="hidden-xs"><?php echo t('Other References') ?></span>
<?php
	if ($nr_references > 1) {
?>
										<span class="badge"><?php echo ($nr_references - 1) ?></span>
<?php
	}
?>
									</a>
								</li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane fade in active" id="divAnswers">
								<!-- answer sections -->
									<div class="v-seperator">
									</div>
<?php
	if ($nr_answers > 1) {
		foreach ($section_answers as $i => $sa) {
			if ($i == 0)
				continue;
			$b = Block::getByID ($sa ['block_id'], $c, $aa);
			if ($b instanceof Block) {
				$b->display ();
			}
		}
	}
	else {
?>
<div class="alert alert-info alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert">
		<span aria-hidden="true">&times;</span>
		<span class="sr-only">Close</span>
	</button>
	<?php echo t('No any other answer so far.') ?>
</div>
<?php
	}
?>

<fieldset id="formNewAnswer">
	<legend><?php echo t('Your anwser') ?></legend>
	<form method="post" enctype="multipart/form-data" class="validate"
			action="/fse_settings/projects/add_new_section" role="form">
		<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
		<input type="hidden" name="cID" value="<?php echo $page_id ?>" />
		<input type="hidden" name="areaHandle" value="Answers" />
		<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
		<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
		<input type="hidden" name="volumeHandle" value="<?php echo $volume_handle ?>" />
		<input type="hidden" name="partHandle" value="<?php echo $part_handle ?>" />
		<input type="hidden" name="chapterHandle" value="<?php echo $chapter_handle ?>" />
		<input type="hidden" name="contentType" value="post-answer" />
		<input type="hidden" name="contentFormat" value="plain" />
		<input type="hidden" name="contentWrapper" value="none" />
		<input type="hidden" name="contentStyle" value="default" />
		<input type="hidden" name="contentAlignment" value="none" />

		<div class="form-group">
			<label for="sectionSubject" class="sr-only">
				<?php echo t('Title of your answer') ?>
			</label>
			<input class="form-control"
				name="sectionSubject" maxlength="255"
				required="true" placeholder="<?php echo t('Title of your answer') ?>" />
		</div>

<?php
	if ($volume_handle != 'language') {
?>
		<div class="form-group">
			<label for="contentCodeLang" class="sr-only">
				<?php echo t('Code Language') ?>
			</label>
			<select name="contentCodeLang" class="form-control">
				<?php Loader::element('computer_language_list', array ('selected_value' => 'plain')); ?>
			</select>
			<p class="help-block"><?php echo t('Please choose the code language.') ?></p>
		</div>
<?php
	}
	else {
?>
		<input type="hidden" name="contentCodeLang" value="<?php echo $part_handle ?>" />
<?php
	}
?>

		<div class="form-group">
			<label for="sectionContent" class="sr-only">
				<?php $my_text = t('Your code (%s)', ProjectInfo::getPartName ($project_id, $domain_handle, $volume_handle, $part_handle)); echo $my_text ?>
			</label>
			<textarea class="form-control"
				name="sectionContent" rows="3"
				required="true" placeholder="<?php echo $my_text ?>"></textarea>
			<span class="help-block"><?php echo t('20 characters at least.') ?></span>
		</div>

		<input type="submit" disabled="disabled" class="btn btn-default enable-on-logged-in"
			value="<?php echo t ('Publish') ?>" />
		<p class="help-block hidden-on-logged-in">
			<?php echo t('<a href="/fse_login?redirectURL=%s">Sign in</a> to post your answer.', $page_path) ?>
		</p>
	</form>
</fieldset>
								</div> <!-- #divAnswers -->
								<div role="tabpanel" class="tab-pane fade" id="divNotes">
									<div class="v-seperator">
									</div>
<?php
	if ($nr_notes > 1) {
		foreach ($section_notes as $i => $sa) {
			if ($i == 0)
				continue;
			$b = Block::getByID ($sa ['block_id'], $c, $an);
			$b->display ();
		}
	}
	else {
?>
<div class="alert alert-info alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert">
		<span aria-hidden="true">&times;</span>
		<span class="sr-only">Close</span>
	</button>
	<?php echo t('No any other note so far.') ?>
</div>
<?php
	}
?>

<fieldset id="formNewNote">
	<legend><?php echo t('Your note') ?></legend>
	<form method="post" enctype="multipart/form-data" class="validate"
			action="/fse_settings/projects/add_new_section" role="form">
		<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
		<input type="hidden" name="cID" value="<?php echo $page_id ?>" />
		<input type="hidden" name="areaHandle" value="Notes" />
		<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
		<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
		<input type="hidden" name="volumeHandle" value="<?php echo $volume_handle ?>" />
		<input type="hidden" name="partHandle" value="<?php echo $part_handle ?>" />
		<input type="hidden" name="chapterHandle" value="<?php echo $chapter_handle ?>" />
		<input type="hidden" name="contentType" value="post-note" />
		<input type="hidden" name="contentFormat" value="markdown_extra" />
		<input type="hidden" name="contentCodeLang" value="none" />
		<input type="hidden" name="contentWrapper" value="none" />
		<input type="hidden" name="contentStyle" value="default" />
		<input type="hidden" name="contentAlignment" value="none" />

		<div class="form-group">
			<label for="sectionSubject" class="sr-only">
				<?php echo t('Title of your note') ?>
			</label>
			<input class="form-control"
				name="sectionSubject" maxlength="255"
				required="true" placeholder="<?php echo t('Title of your note') ?>" />
		</div>

		<div class="form-group">
			<label for="sectionContent" class="sr-only">
				<?php $my_text = t('Your note'); echo $my_text; ?>
			</label>
			<textarea class="form-control"
				name="sectionContent" rows="3"
				required="true" placeholder="<?php echo $my_text ?>"></textarea>
			<span class="help-block"><?php echo t('Markdown Extra enabled. 20 characters at least.') ?></span>
		</div>

		<input type="submit" disabled="disabled" class="btn btn-default enable-on-logged-in"
			value="<?php echo t ('Publish') ?>" />
		<p class="help-block hidden-on-logged-in">
			<?php echo t('<a href="/fse_login?redirectURL=%s">Sign in</a> to post your note.', $page_path) ?>
		</p>
	</form>
</fieldset>

								</div> <!-- #divNotes -->
								<div role="tabpanel" class="tab-pane fade" id="divReferences">
									<div class="v-seperator">
									</div>
<?php
	if ($nr_references > 1) {
		foreach ($section_references as $i => $sr) {
			if ($i == 0)
				continue;
			$b = Block::getByID ($sr ['block_id'], $c, $ar);
			$b->display ();
		}
	}
	else {
?>
<div class="alert alert-info alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert">
		<span aria-hidden="true">&times;</span>
		<span class="sr-only">Close</span>
	</button>
	<?php echo t('No any other reference so far.') ?>
</div>
<?php
	}
?>

<fieldset id="formNewReference">
	<legend><?php echo t('Your reference') ?></legend>
	<form method="post" enctype="multipart/form-data" class="validate"
			action="/fse_settings/projects/add_new_ref_link_list" role="form">
		<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
		<input type="hidden" name="cID" value="<?php echo $page_id ?>" />
		<input type="hidden" name="areaHandle" value="References" />
		<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
		<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
		<input type="hidden" name="volumeHandle" value="<?php echo $volume_handle ?>" />
		<input type="hidden" name="partHandle" value="<?php echo $part_handle ?>" />
		<input type="hidden" name="chapterHandle" value="<?php echo $chapter_handle ?>" />

		<div class="form-group">
			<div class="row">
				<div class="col-md-4">
					<div class="input-group">
						<span class="input-group-addon">#1</span>
						<input type="text" class="form-control" name="refTitle0"
								required="true" placeholder="<?php echo t('Title of your reference') ?>" />
					</div><!-- /input-group -->
				</div><!-- /.col-md-4 -->
				<div class="col-md-8">
					<div class="input-group">
						<span class="input-group-addon">@</span>
						<input type="url" class="form-control" name="refLink0"
								required="true" placeholder="<?php echo t('Link of your reference') ?>" />
					</div><!-- /input-group -->
				</div><!-- /.col-md-8 -->
			</div><!-- /.row -->
		</div><!-- /.form-group -->

		<div class="form-group">
			<div class="row">
				<div class="col-md-4">
					<div class="input-group">
						<span class="input-group-addon">#2</span>
						<input type="text" class="form-control" name="refTitle1"
								placeholder="<?php echo t('Title of your reference') ?>" />
					</div><!-- /input-group -->
				</div><!-- /.col-md-4 -->
				<div class="col-md-8">
					<div class="input-group">
						<span class="input-group-addon">@</span>
						<input type="url" class="form-control" name="refLink1"
								placeholder="<?php echo t('Link of your reference') ?>" />
					</div><!-- /input-group -->
				</div><!-- /.col-md-8 -->
			</div><!-- /.row -->
		</div><!-- /.form-group -->

		<div class="form-group">
			<div class="row">
				<div class="col-md-4">
					<div class="input-group">
						<span class="input-group-addon">#3</span>
						<input type="text" class="form-control" name="refTitle2"
								placeholder="<?php echo t('Title of your reference') ?>" />
					</div><!-- /input-group -->
				</div><!-- /.col-md-4 -->
				<div class="col-md-8">
					<div class="input-group">
						<span class="input-group-addon">@</span>
						<input type="url" class="form-control" name="refLink2"
								placeholder="<?php echo t('Link of your reference') ?>" />
					</div><!-- /input-group -->
				</div><!-- /.col-md-8 -->
			</div><!-- /.row -->
		</div><!-- /.form-group -->

		<div class="form-group">
			<div class="row">
				<div class="col-md-4">
					<div class="input-group">
						<span class="input-group-addon">#4</span>
						<input type="text" class="form-control" name="refTitle3"
								placeholder="<?php echo t('Title of your reference') ?>" />
					</div><!-- /input-group -->
				</div><!-- /.col-md-4 -->
				<div class="col-md-8">
					<div class="input-group">
						<span class="input-group-addon">@</span>
						<input type="url" class="form-control" name="refLink3"
								placeholder="<?php echo t('Link of your reference') ?>" />
					</div><!-- /input-group -->
				</div><!-- /.col-md-8 -->
			</div><!-- /.row -->
		</div><!-- /.form-group -->

		<input type="submit" disabled="disabled" class="btn btn-default enable-on-logged-in"
				value="<?php echo t ('Publish') ?>" />
		<p class="help-block hidden-on-logged-in">
			<?php echo t('<a href="/fse_login?redirectURL=%s">Sign in</a> to post your reference.', $page_path) ?>
		</p>
	</form>
</fieldset>

								</div> <!-- #divReferences -->
							</div><!-- .tab-content -->
						</div><!-- .panel-body -->

						<div class="panel-footer">
						</div><!-- .panel-footer -->
					</div> <!-- .panel -->
				</section> <!-- .col-md-9 -->

				<section class="col-sm-6 col-md-3 col-md-3">
					<ul class="list-group">
						<li class="list-group-item list-group-item-info">
							<?php echo t('Top Q&amp;A') ?>
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
							<?php echo t('Normal Q&amp;A') ?>
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
				</section> <!-- .col-md-3 -->
			</div> <!-- row -->
		</div> <!-- .container-fluid -->
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
