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
		$curr_fse_id = $_SESSION['FSEInfo']['fse_id'];
		$user_rights = ProjectInfo::getUserRights ($project_id, $curr_fse_id);

		$project_info = ProjectInfo::getBasicInfo ($project_id);
		if ($project_info == false) {
			$error_info = t('Bad project');
		}
		else if ($curr_fse_id != $section_info ['author_id'] && $user_rights [2] != 't') {
			$error_info = t('You have no right to edit this post!');
		}
		else {
			$filename = DocSectionManager::getSectionContentPath ($section_id, $current_ver_code, 'org');
			$fp = fopen ($filename, "r");
			if ($fp) {
				$author_id = trim (fgets ($fp));
				$type_handle = trim (fgets ($fp));
				$attached_files = fgets ($fp);
				$section_subject = trim (fgets ($fp));
				$section_content = fread ($fp, filesize($filename));
				fclose ($fp);

				$json = Loader::helper('json');
				$attached_files = $json->decode ($attached_files);
				if (is_array ($attached_files) == false) {
					$error_info = t('Section content file is bad or lost!');
				}

				$form_action = "/fse_settings/projects/edit_post";
			}
			else {
				$error_info = t('Filesytem error!');
			}
		}
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
	<h4 class="modal-title"><?php echo t ('Edit Post') ?></h4>
</div>
<div class="modal-body">
<?php
if (isset ($error_info)) {
?>
	<p><?php echo $error_info ?></p>
<?php
}
else {
	# we use formToken to avoid duplicated validation of parameters
	$form_token = hash_hmac ('md5', time (), $section_id);
	$_SESSION ['formToken4EditPost'] = $form_token;
?>
	<form id="formEditPost" method="post" action="<?php echo $form_action ?>" role="form">
		<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
		<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
		<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
		<input type="hidden" name="volumeHandle" value="<?php echo $section_info ['volume_handle'] ?>" />
		<input type="hidden" name="partHandle" value="<?php echo $section_info ['part_handle'] ?>" />
		<input type="hidden" name="chapterHandle" value="<?php echo $section_info ['chapter_handle'] ?>" />
		<input type="hidden" name="sectionID" value="<?php echo $section_id ?>" />
		<input type="hidden" name="formToken" value="<?php echo $form_token ?>" />

		<input type="hidden" name="typeHandle" value="<?php echo $type_handle ?>" />

		<div class="form-group">
			<label for="postSubject" class="control-label">
				<?php echo t('Title'); ?>
			</label>
			<input class="form-control" type="text" name="postSubject"
					value="<?php echo h5($section_subject) ?>" />
		</div>

		<div class="form-group">
			<label for="postContent" class="control-label">
				<?php echo t('Content') ?>
			</label>
			<textarea class="form-control"
					name="postContent" rows="5" required="true"><?php echo $section_content ?></textarea>
<?php
			if (strncasecmp ($type_handle, 'post-answer', 11)) {
				$my_text = t('Markdown Extra enabled; 20 characters at least.');
			}
			else {
				$my_text = t('Only computer code; at least 20 characters.');
			}
?>
				<p class="help-block"><?php echo $my_text; ?></p>

		</div>

<?php
/* Currently, do not change the attached files */
	$al = Loader::helper ('concrete/asset_library');
	for ($i = 0; $i < ProjectInfo::MAX_ATTACHED_FILES; $i++) {
		if ($attached_files [$i] > 0) {
?>
		<input type="hidden" name="attachmentFile<?php echo $i ?>" value="<?php echo $attached_files [$i] ?>" />
<?php
		}
	}

/*
		<div class="form-group">
			<label for="attachmentFile0">Attached Files: </label>
	$al = Loader::helper ('concrete/asset_library');
	for ($i = 0; $i < ProjectInfo::MAX_ATTACHED_FILES; $i++) {
		if ($attached_files [$i] > 0) {
			$attached_file = File::getByID ($attached_files [$i]);
			echo $al->file ("attachmentFile$i", "attachmentFile$i", t('Choose another File'), $attached_file);
		}
		else {
			echo $al->file("attachmentFile$i", "attachmentFile$i", t('Upload or Choose File'));
		}
	}
		</div>
*/
?>
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
	<button id="btnSaveChanges" type="button" class="btn btn-primary" disabled="disabled"
			data-doing-text="<?php echo t ('Saving...') ?>"
			data-done-text="<?php echo t ('Saved') ?>">
		<?php echo t ('Save Changes') ?>
	</button>
<?php
}
?>
</div>

<script lang="JavaScript">
$('#formEditPost input[name="postSubject"]').change (function () {
	$('#btnSaveChanges').removeAttr ('disabled');
});

$('#formEditPost textarea[name="postContent"]').change (function () {
	$('#btnSaveChanges').removeAttr ('disabled');
});

$('#formEditPost').submit (function (e) {
	$('#btnSaveChanges').attr ('disabled', 'disabled');
	$('#btnSaveChanges').bootstrapBtn ('doing');
});

$('#btnSaveChanges').click (function () {
	$('#formEditPost input[name="inputHiddenSubmit"]').click();
});

</script>
