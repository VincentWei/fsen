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

require_once('helpers/check_login.php');
require_once('helpers/fsen/DocSectionManager.php');
require_once('helpers/fsen/ProjectInfo.php');

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
		$error_info = t('The section has been deleted!');
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
		else if (($user_right = ProjectInfo::getUserEditRight ($project_id, $domain_handle, $section_info['volume_handle'],
				$section_info['part_handle'], $section_info['chapter_handle'], $_SESSION['FSEInfo']['fse_id'])) != 0) {
			switch ($user_right) {
			case ProjectInfo::EDIT_PAGE_USER_BANNED:
				$error_info = t('You are banned currently due to the violation against the site policy!');
				break;
			case ProjectInfo::EDIT_PAGE_USER_NO_RIGHT:
				$error_info = t('You have no right to edit this section!');
				break;
			default:
				$error_info = t('Bad request!');
				break;
			}
		}
		else {
			$form_action = "/fse_settings/projects/set_section_version";

			class ContentVersion {
				public $pub_time;
				public $author_id;
				public $type_handle;
				public $attached_files;
				public $content_subject;
				public $org_content;
				public $html_content;
			}

			$version_codes = array ();
			$content_versions = array ();
			for ($ver_code = 0; $ver_code <= $section_info['max_ver_code']; $ver_code++) {
				$content_version = new ContentVersion;
				$filename = DocSectionManager::getSectionContentPath ($section_id, $ver_code, 'org');
				$fp = fopen ($filename, "r");
				if ($fp) {
					$fstats = fstat($fp);
					$content_version->pub_time = $pub_time = date ('Y-m-d H:i', $fstats['mtime']) . ' CST';
					$content_version->author_id = trim (fgets ($fp));
					$content_version->type_handle = trim (fgets ($fp));
					$content_version->attached_files = fgets ($fp);
					$content_version->content_subject = trim (fgets ($fp));
					$content_version->org_content = fread ($fp, filesize($filename));
					fclose ($fp);

					$filename = DocSectionManager::getSectionContentPath ($section_id, $ver_code, 'html');
					if (file_exists ($filename)) {
						$content_version->html_content = file_get_contents ($filename);
					}
					else {
						$content_version->html_content = NULL;
					}

					$version_codes ["$ver_code"] = "Version $ver_code ($pub_time)";
					$content_versions [] = $content_version;
				}
				else {
					$content_versions [] = NULL;
				}
			}

			$json = Loader::helper('json');
			$content_versions_json = $json->encode ($content_versions);
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
	<h4 class="modal-title"><?php echo t ('Add New Member') ?></h4>
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

	$form_token_name = 'formToken4ChangeVersion';
	$form_token = hash_hmac ('md5', time (), $section_id);
	$_SESSION [$form_token_name] = $form_token;

?>
	<form id="formSetNewVersion" method="post" action="<?php echo $form_action ?>" class="validate form-horizontal">
		<input type="hidden" name="fsenDocLang" value="<?php $doc_lang ?>" />
		<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
		<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
		<input type="hidden" name="volumeHandle" value="<?php echo $section_info ['volume_handle'] ?>" />
		<input type="hidden" name="partHandle" value="<?php echo $section_info ['part_handle'] ?>" />
		<input type="hidden" name="chapterHandle" value="<?php echo $section_info ['chapter_handle'] ?>" />
		<input type="hidden" name="sectionID" value="<?php echo $section_id ?>" />

		<input type="hidden" name="formTokenName" value="<?php echo $form_token_name ?>" />
		<input type="hidden" name="formToken" value="<?php echo $form_token ?>" />

		<div class="form-group">
			<label for="newVerCode" class="col-md-4 control-label"><?php echo t('Current version: ') ?>
			</label>
			<div class="col-md-8">
				<?php echo $form->select ('newVerCode', $version_codes, $current_ver_code,
						array ('class' => 'form-control')); ?>
			</div>
		</div>

		<legend><?php echo t('Preview') ?></legend>
		<div class="full-stack">
			<section id="previewSECTIONCONTENT" class="preview">
					<?php echo $content_versions[$current_ver_code]->html_content ?>
			</section>
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
	<button id="btnSetNewVersion" type="button" class="btn btn-primary" disabled="true"
			data-doing-text="<?php echo t ('Submitting...') ?>"
			data-done-text="<?php echo t ('Done') ?>">
		<?php echo t ('Ok') ?>
	</button>
<?php
}
?>
</div>

<script lang="text/javascript">
var content_versions_json = <?php echo $content_versions_json ?>;
$("#newVerCode").change (function () {
	var ver_code = parseInt ($(this).val (), 10);
	$("#previewSECTIONCONTENT").html (content_versions_json[ver_code]['html_content']);
	if (ver_code != <?php echo $current_ver_code ?>) {
		$("#btnSetNewVersion").removeAttr ("disabled");
	}
	else {
		$("#btnSetNewVersion").attr ("disabled", "true");
	}
});

$('#formSetNewVersion').submit (function (e) {
	$("#btnSetNewVersion").attr ('disabled', 'disabled');
	$("#btnSetNewVersion").bootstrapBtn ('doing');
});

$("#btnSetNewVersion").click (function () {
	$('#formSetNewVersion input[name="inputHiddenSubmit"]').click();
});
</script>

