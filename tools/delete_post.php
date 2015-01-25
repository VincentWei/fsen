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

require_once('helpers/check_login.php');
require_once('helpers/fsen/DocSectionManager.php');
require_once('helpers/fsen/ProjectInfo.php');

$domain_handle = $_REQUEST['domainHandle'];
$section_id = $_REQUEST['sectionID'];
$current_ver_code = $_REQUEST['currentVerCode'];

if (!fse_try_to_login ()) {
	$error_info = t('You are not signed in.');
}
else if (preg_match ("/^[a-f0-9]{32}$/", $section_id)
		&& in_array ($domain_handle, ProjectInfo::$mDomainList)) {
	$db = Loader::db ();
	$section_info = DocSectionManager::getSectionInfo ($domain_handle, $section_id);
	if (count ($section_info) == 0) {
		$error_info = t('No such section ID!');
	}
	else if ($section_info['status'] != 0) {
		$error_info = t('The post has been deleted or shielded.');
	}
	else if ($current_ver_code > $section_info['max_ver_code']) {
		$error_info = t('Bad request!');
	}
	else {
		$project_id = $section_info ['project_id'];
		$doc_lang = substr ($project_id, -2);
		$project_info = ProjectInfo::getBasicInfo ($project_id);
		if ($project_info == false) {
			$error_info = t('Bad project');
		}
		else {
			$curr_fse_id = $_SESSION['FSEInfo']['fse_id'];
			if ($curr_fse_id != $section_info ['author_id']) {
				$user_rights = ProjectInfo::getUserRights ($project_id, $curr_fse_id);
				if ($user_rights [2] != 't') {
					$error_info = t('You have no right to delete this post!');
				}
			}
		}
		$form_action = "/fse_settings/projects/delete_post";
	}
}
else {
	$error_info = t('Bad Request!');
}
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">
		<span aria-hidden="true">&times;</span>
		<span class="sr-only"><?php echo t ('Close') ?></span>
	</button>
	<h4 class="modal-title"><?php echo t ('Delete Post') ?></h4>
</div>
<div class="modal-body">
	<p><?php echo isset ($error_info)?$error_info:t ('Are you sure to delete/shield this post?') ?></p>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">
		<?php echo t ('Cancel') ?>
	</button>
<?php
if (!isset ($error_info)) {
?>
	<button id="btnDeletePost" type="button" class="btn btn-warning"
			data-doing-text="<?php echo t ('Deleting...') ?>"
			data-done-text="<?php echo t ('Deleted') ?>">
		<?php echo t ('Delete') ?>
	</button>
<?php
}

if (!isset ($error_info) && $user_rights [2] == 't') {
?>
	<button id="btnShieldPost" type="button" class="btn btn-danger"
			data-doing-text="<?php echo t ('Shield...') ?>"
			data-done-text="<?php echo t ('Shielded') ?>">
		<?php echo t ('Shield') ?>
	</button>
<?php
}
?>
</div>

<?php
if (!isset ($error_info)) {
	# we use formToken to avoid duplicated validation of parameters
	$form_token = hash_hmac ('md5', time (), $section_id);
	$_SESSION ['formToken4DeletePost'] = $form_token;
?>
<form id="formDeletePost" method="post" action="<?php echo $form_action ?>">
	<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
	<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
	<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
	<input type="hidden" name="volumeHandle" value="<?php echo $section_info ['volume_handle'] ?>" />
	<input type="hidden" name="partHandle" value="<?php echo $section_info ['part_handle'] ?>" />
	<input type="hidden" name="chapterHandle" value="<?php echo $section_info ['chapter_handle'] ?>" />
	<input type="hidden" name="sectionID" value="<?php echo $section_id ?>" />
	<input type="hidden" name="authorID" value="<?php echo $section_info ['author_id'] ?>" />
	<input type="hidden" name="deleteOrShield" value="" />

	<input type="hidden" name="formToken" value="<?php echo $form_token ?>" />

	<input type="submit" name="inputHiddenSubmit" value="submit" style="display:none" />

</form>
<?php
}
?>

<script lang="JavaScript">
$('#formDeletePost').submit (function (e) {
	$('#btnDeletePost').attr ('disabled', 'disabled');
	$('#btnShieldPost').attr ('disabled', 'disabled');
	if ($('#formDeletePost input[name="deleteOrShield"]').val () == 'delete') {
		$('#btnDeletePost').bootstrapBtn ('doing');
	}
	else {
		$('#btnShieldPost').bootstrapBtn ('doing');
	}
});

$('#btnDeletePost').on('click', function () {
	$('#formDeletePost input[name="deleteOrShield"]').val ('delete');
	$('#formDeletePost input[name="inputHiddenSubmit"]').click();
});

$('#btnShieldPost').on('click', function () {
	$('#formDeletePost input[name="deleteOrShield"]').val ('shield');
	$('#formDeletePost input[name="inputHiddenSubmit"]').click();
});
</script>
