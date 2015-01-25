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

require_once ('helpers/check_login.php');
require_once ('helpers/fsen/ProjectInfo.php');

$project_id = $_REQUEST['projectID'];
$domain_handle = $_REQUEST['domainHandle'];
$volume_handle = $_REQUEST['volumeHandle'];
$part_handle = $_REQUEST['partHandle'];
$chapter_handle = $_REQUEST['chapterHandle'];
$thread_action = $_REQUEST['threadAction'];

$doc_lang = substr ($project_id, -2);

if ($domain_handle == 'community') {
	switch ($thread_action) {
	case 'top':
		$glyphicon_name = 'circle-question-mark';
		$glyphicon_color = 'primary';
		$title = t('Top Thread');
		$prompt = t('Do you really want to mark this thread as the top thread?');
		$form_action = "/fse_settings/projects/toggle_thread_top";
		$btn_class = 'warning';
		break;
	case 'untop':
		$glyphicon_name = 'circle-question-mark';
		$glyphicon_color = 'primary';
		$title = t('Untop Thread');
		$prompt = t('Do you really want to remove the top mark of this thread?');
		$form_action = "/fse_settings/projects/toggle_thread_top";
		$btn_class = 'warning';
		break;
	case 'delete':
		$glyphicon_name = 'circle-exclamation-mark';
		$glyphicon_color = 'danger';
		$title = t('Remove Thread');
		$prompt = t('Do you really want to remove this thread (and its all replies and comments)?');
		$form_action = "/fse_settings/projects/delete_thread";
		$btn_class = 'danger';
		break;
	default:
		$error_info = t('Bad action!');
		break;
	}
}
else {
	$error_info = t('Bad domain!');
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
		else if (substr (ProjectInfo::getUserRights ($project_id, $_SESSION['FSEInfo']['fse_id']), 2, 1) != 't') {
			$error_info = t('You have no right to manage forum of this project!');
		}
		else {
			$chapter_name = ProjectInfo::getChapterName ($project_id,
					$domain_handle, $volume_handle, $part_handle, $chapter_handle);
			if ($chapter_name == false) {
				$error_info = t('Not existed thread!');
			}
		}
	}
}
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">
		<span aria-hidden="true">&times;</span>
		<span class="sr-only"><?php echo t ('Close') ?></span>
	</button>
	<h4 class="modal-title"><?php echo $title ?></h4>
</div>
<div class="modal-body">
	<p><?php echo isset ($error_info)?$error_info:$prompt ?></p>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">
		<?php echo t ('Cancel') ?>
	</button>
<?php
if (!isset ($error_info)) {
?>
	<button id="btnThreadAction" type="button" class="btn btn-<?php echo $btn_class ?>"
			data-doing-text="<?php echo t ('Submitting...') ?>"
			data-done-text="<?php echo t ('Done') ?>">
		<?php echo t ('Ok') ?>
	</button>
<?php
}
?>

<form id="formThreadAction" method="post" action="<?php echo $form_action ?>">
	<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
	<input type="hidden" name="cID" value="<?php echo $page_id ?>" />
	<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
	<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
	<input type="hidden" name="volumeHandle" value="<?php echo $volume_handle ?>" />
	<input type="hidden" name="partHandle" value="<?php echo $part_handle ?>" />
	<input type="hidden" name="chapterHandle" value="<?php echo $chapter_handle ?>" />

	<input type="submit" name="inputHiddenSubmit" value="submit" style="display:none" />

</form>
</div>

<script lang="JavaScript">
$('#formThreadAction').submit (function (e) {
	$('#btnThreadAction').attr ('disabled', 'disabled');
	$('#btnThreadAction').bootstrapBtn ('doing');
});

$('#btnThreadAction').on('click', function () {
	$('#formThreadAction input[name="inputHiddenSubmit"]').click();
});
</script>
