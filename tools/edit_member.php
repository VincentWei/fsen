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

$domain_handle = $_REQUEST['domainHandle'];
$section_id = $_REQUEST['sectionID'];
$current_ver_code = (int)$_REQUEST['currentVerCode'];

require_once ('helpers/check_login.php');
require_once ('helpers/fsen/DocSectionManager.php');
require_once ('helpers/fsen/ProjectInfo.php');
require_once ('helpers/fsen/FSEInfo.php');

if (!fse_try_to_login ()) {
	$error_info = t('You are not signed in.');
}
else if (preg_match ("/^[a-f0-9]{32}$/", $section_id) && $domain_handle == 'misc') {

	$db = Loader::db ();
	$section_info = DocSectionManager::getSectionInfo ($domain_handle, $section_id);
	if (count ($section_info) == 0) {
		$error_info = t('No such section ID!');
	}
	else if ($current_ver_code > $section_info['max_ver_code']) {
		$error_info = t('Bad request: bad version!');
	}
	else {
		$project_id = $section_info ['project_id'];
		$doc_lang = substr ($project_id, -2);
		$form_action = "/fse_settings/projects/edit_member_roles";
		$project_info = ProjectInfo::getBasicInfo ($project_id);
		if ($project_info == false) {
			$error_info = t('Bad project.');
		}
		else if (substr (ProjectInfo::getUserRights ($project_id, $_SESSION['FSEInfo']['fse_id']), 0, 1) != 't') {
			$error_info = t('You have no right to edit member roles.');
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
			}

			$json = Loader::helper('json');
			$attached_files = $json->decode ($attached_files);
			if (is_array ($attached_files) == false) {
				$error_info = t('Section content file is bad or lost!');
			}
			else if (strncmp ($type_handle, "member", 6) != 0) {
				$error_info = t('Section is not a member section!');
			}
			else {
				if ($attached_files [0] > 0) {
					$attached_file_0 = File::getByID ($attached_files [0]);
				}
				$type_fragments = explode (":", $type_handle);
				if (count ($type_fragments) != 5) {
					$error_info = t('Bad member section!');
				}
				else {
					$member_username = $type_fragments[2];
					$member_fse_info = FSEInfo::getBasicProfile ($member_username);
					if ($member_fse_info == false) {
						$error_info = t('Bad member username!');
					}
					else {
						$roles = ProjectInfo::getUserRoles ($project_id, $member_fse_info['fse_id']);
						$member_roles = $roles ['member_roles'];
						if ($member_roles == '') {
							$error_info = t('Not a valid member!');
						}
						else if ($member_roles == 'owner') {
							$error_info = t('You can not edit roles of the owner!');
						}
					}
				}
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
	<h4 class="modal-title"><?php echo t ('Edit Member Roles') ?></h4>
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
	<form id="formMemberRoles" method="post" action="<?php echo $form_action ?>" class="validate form-horizontal">
		<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
		<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
		<input type="hidden" name="areaHandle" value="<?php echo $_REQUEST['areaHandle'] ?>" />
		<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
		<input type="hidden" name="volumeHandle" value="na" />
		<input type="hidden" name="partHandle" value="na" />
		<input type="hidden" name="chapterHandle" value="na" />
		<input type="hidden" name="sectionID" value="<?php echo $section_id ?>" />

		<div class="form-group">
			<label class="col-md-4 control-label" for="memberUsername">
				<?php echo t('FSEN Username: ') ?>
			</label>
			<div class="col-md-8">
				<span><?php echo $member_username ?></span>
			</div>
			<input type="hidden" name="memberUsername" value="<?php echo $member_username ?>" />
		</div>

		<div class="form-group">
			<label class="col-md-4 control-label" for="memberDisplayName">
				<?php echo t('Member Name: ') ?>
			</label>
			<div class="col-md-8">
				<?php echo  $form->text ('memberDisplayName', $roles ['display_name'],
					array ("required" => "true", "pattern" => ".{2,64}", 'class' => 'form-control')); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="col-md-4 control-label" for="memberDescription">
				<?php echo t('Description: ') ?>
			</label>
			<div class="col-md-8">
				<?php echo  $form->text ('memberDescription', $roles ['description'],
					array ("required" => "true", "pattern" => ".{5,255}", 'class' => 'form-control')); ?>
			</div>
		</div>

		<legend>
			<?php echo t('Member Roles:') ?>
		</legend>

		<div class="checkbox">
			<label for="memberRole0">
				<input <?php echo strpos ($member_roles, 'g-adm')?'checked="checked"':'' ?>
					type="checkbox" name="memberRole0" value="g-adm" />
					<?php echo t('Administrator, who has the same rights as the owner of the project.') ?>
			</label>
		</div>

		<div class="checkbox">
			<label for="memberRole1">
				<input <?php echo strpos ($member_roles, 'c-adm')?'checked="checked"':'' ?>
					type="checkbox" name="memberRole1" value="c-adm" />
					<?php echo t('Forum administrator, who can manage all community forums.') ?>
			</label>
		</div>

		<div class="checkbox">
			<label for="memberRole2">
				<input <?php echo strpos ($member_roles, 'p-edt')?'checked="checked"':'' ?>
					type="checkbox" name="memberRole2" value="p-edt">
					<?php echo t('Project page editor, who can edit any sections of the project.') ?>
			</label>
		</div>

		<div class="checkbox">
			<label for="memberRole3">
				<input <?php echo strpos ($member_roles, 'c-cmt')?'checked="checked"':'' ?>
					type="checkbox" name="memberRole3" value="c-cmt">
					<?php echo t('Commiter of the project.') ?>
			</label>
		</div>

		<div class="checkbox">
			<label for="memberRole4">
				<input <?php echo strpos ($member_roles, 'g-mmb')?'checked="checked"':'' ?>
					type="checkbox" name="memberRole4" value="g-mmb">
					<?php echo t('General member of the project.') ?>
			</label>
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
	<button id="btnMemberOk" type="button" class="btn btn-primary" disabled="true"
			data-doing-text="<?php echo t ('Submitting...') ?>"
			data-done-text="<?php echo t ('Done') ?>">
		<?php echo t ('Ok') ?>
	</button>
<?php
}
?>
</div>

<script type="text/javascript">
$('#formMemberRoles input[name="memberDisplayName"]').change (function () {
		$('#btnMemberOk').removeAttr ('disabled');
});

$('#formMemberRoles input[name="memberDescription"]').change (function () {
		$('#btnMemberOk').removeAttr ('disabled');
});

$('#formMemberRoles').submit (function (e) {
	$('#btnMemberOk').attr ('disabled', 'disabled');
	$('#btnMemberOk').bootstrapBtn ('doing');
});

$('#btnMemberOk').click (function () {
	$('#formMemberRoles input[name="inputHiddenSubmit"]').click();
});
</script>

