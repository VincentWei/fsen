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
	else if ($current_ver_code == 0 || $current_ver_code > $section_info['max_ver_code']) {
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
			$user_rights = ProjectInfo::getUserRights ($project_id, $curr_fse_id);
			if (($section_info['status'] == DocSectionManager::SS_ADMIN_DELETED
					|| $section_info['status'] == DocSectionManager::SS_ADMIN_SHIELDED) && $user_rights [2] != 't') {
				$error_info = t('You have no right to recover a deleted/shielded post by the administrator.');
			}
			else if ($user_rights [2] != 't' && $section_info['author_id'] != $curr_fse_id) {
				$error_info = t('You have no right to recover the post.');
			}
		}
		$form_action = "/fse_settings/projects/recover_post";
	}
}
else {
	$error_info = t('Bad Request!');
}

if (!isset ($error_info)) {
	$filename = DocSectionManager::getSectionContentPath ($section_id, $current_ver_code - 1, 'html');
	$previous_content = file_get_contents ($filename);
}
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">
		<span aria-hidden="true">&times;</span>
		<span class="sr-only"><?php echo t ('Close') ?></span>
	</button>
	<h4 class="modal-title"><?php echo t ('Recover Post') ?></h4>
</div>
<div class="modal-body">
<?php
if (isset ($error_info)) {
?>
	<p><?php echo $error_info ?></p>
<?php
}
else {
?>
	<p><?php echo t ('Are you sure to recover this post?') ?></p>
	<h2>Preview</h2>
	<div>
		<?php echo $previous_content ?>
	</div>
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
	<button id="btnRecoverPost" type="button" class="btn btn-primary"
			data-doing-text="<?php echo t ('Recovering...') ?>"
			data-done-text="<?php echo t ('Recovered') ?>">
		<?php echo t ('Recover') ?>
	</button>
<?php
}
?>
</div>

<?php
if (!isset ($error_info)) {
	# we use formToken to avoid duplicated validation of parameters
	$form_token = hash_hmac ('md5', time (), $section_id);
	$_SESSION ['formToken4RecoverPost'] = $form_token;
?>
<form id="formRecoverPost" method="post" action="<?php echo $form_action ?>">
	<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
	<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
	<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
	<input type="hidden" name="volumeHandle" value="<?php echo $section_info ['volume_handle'] ?>" />
	<input type="hidden" name="partHandle" value="<?php echo $section_info ['part_handle'] ?>" />
	<input type="hidden" name="chapterHandle" value="<?php echo $section_info ['chapter_handle'] ?>" />
	<input type="hidden" name="sectionID" value="<?php echo $section_id ?>" />
	<input type="hidden" name="currentVerCode" value="<?php echo $current_ver_code ?>" />

	<input type="hidden" name="formToken" value="<?php echo $form_token ?>" />

	<input type="submit" name="inputHiddenSubmit" value="submit" style="display:none" />

</form>
<?php
}
?>

<script lang="JavaScript">
$('#formRecoverPost').submit (function (e) {
	$('#btnRecoverPost').attr ('disabled', 'disabled');
	$('#btnRecoverPost').bootstrapBtn ('doing');
});

$('#btnRecoverPost').on('click', function () {
	$('#formRecoverPost input[name="inputHiddenSubmit"]').click();
});
</script>
