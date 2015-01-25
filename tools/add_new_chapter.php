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

Loader::model ('fsen_localization');
FSENLocalization::setupInterfaceLocalization4AjaxRequest ();

$page_id = $_REQUEST['cID'];
if (!Loader::helper('validation/numbers')->integer($page_id)) {
	die(t('Access Denied'));
}

$project_id = $_REQUEST['projectID'];
$domain_handle = $_REQUEST['domainHandle'];
$volume_handle = $_REQUEST['volumeHandle'];
$part_handle = $_REQUEST['partHandle'];

$doc_lang = substr ($project_id, -2);

require_once ('helpers/check_login.php');
require_once ('helpers/fsen/ProjectInfo.php');

if ($domain_handle == 'document' || $domain_handle == 'community') {
	$form_action = "/fse_settings/projects/add_new_chapter";
}
else {
	$error_info = t('Bad domain or volume!');
}

if (!isset ($error_info)) {
	if (!fse_try_to_login ()) {
		$error_info = t('You are not signed in.');
	}
	else {
		$project_info = ProjectInfo::getBasicInfo ($project_id);
		if ($project_info == false) {
			$error_info = t('Bad project');
		}
		else if (substr (ProjectInfo::getUserRights ($project_id, $_SESSION['FSEInfo']['fse_id']), 1, 1) != 't') {
			$error_info = t('You have no right to edit content of this project!');
		}
	}
}
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">
		<span aria-hidden="true">&times;</span>
		<span class="sr-only"><?php echo t ('Close') ?></span>
	</button>
	<h4 class="modal-title"><?php echo t('Add New Chapter') ?></h4>
</div>
<div class="modal-body">
<?php
if (isset ($error_info)) {
?>
	<p><?php echo $error_info ?></p>
<?php
}
else {
$form = Loader::helper('form');
?>
	<form id="formNewChapter" method="post" action="<?php echo $form_action ?>">
		<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
		<input type="hidden" name="cID" value="<?php echo $page_id ?>" />
		<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
		<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
		<input type="hidden" name="volumeHandle" value="<?php echo $volume_handle ?>" />
		<input type="hidden" name="partHandle" value="<?php echo $part_handle ?>" />

		<div class="form-group">
			<label for="chapterSubject"><?php echo t('Subject') ?></label>
			<input name="chapterSubject" class="form-control"
					required="true" pattern=".{2,64}" />
		</div>

		<div class="form-group">
			<label for="chapterHandle"><?php echo t('Handle') ?></label>
			<input name="chapterHandle" class="form-control"
					required="true" pattern="[a-z0-9\-]{3,64}" />
		</div>

		<div class="form-group">
			<label for="chapterDesc"><?php echo t('Description (at least 5 characters)') ?></label>
			<input name="chapterDesc" class="form-control"
					required="true" pattern=".{5,255}" />
		</div>

		<div class="form-group">
			<div class="checkbox">
				<label for="mustForNewbie">
					<input type="checkbox" name="mustForNewbie" value="1">
						<?php echo t('This chapter is a must for newbie.') ?>
				</label>
			</div>
		</div>

		<input type="submit" name="inputHiddenSubmit" value="submit" style="display:none" />

	</form>
<?php
}
?>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">
		<?php echo t ('Cancel') ?>
	</button>
<?php
if (!isset ($error_info)) {
?>
	<button id="btnNewChapter" type="button" class="btn btn-primary"
			data-doing-text="<?php echo t ('Adding...') ?>"
			data-done-text="<?php echo t ('Added') ?>">
		<?php echo t ('Ok') ?>
	</button>
<?php
}
?>
</div>

<script lang="JavaScript">
$('#formNewChapter').submit (function (e) {
	$('#btnNewChapter').attr ('disabled', 'disabled');
	$('#btnNewChapter').bootstrapBtn ('doing');
});

$('#btnNewChapter').click (function () {
	$('#formNewChapter input[name="inputHiddenSubmit"]').click();
});
</script>
