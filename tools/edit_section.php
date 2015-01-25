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
	else if ($current_ver_code > $section_info['max_ver_code']) {
		$error_info = t('Bad request!');
	}
	else {
		$project_id = $section_info ['project_id'];
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

			$form_action = "/fse_settings/projects/edit_section";
		}
	}
}
else {
	$error_info = t('Bad Request!');
}

require_once(DIR_FILES_ELEMENTS_CORE . '/dialog_header.php');
?>

<div class="ccm-ui">

<?php
$jsh = Loader::helper('concrete/interface');
$al = Loader::helper ('concrete/asset_library');
$form = Loader::helper('form');

if (isset ($error_info)) {
	print '<div class="ccm-error">' . $error_info . '</div>';
	print '<br><br>';
	print '<div class="ccm-buttons dialog-buttons">';
	print $jsh->button_js(t('Close'), 'jQuery.fn.dialog.closeTop()', 'left');
	print '</div>';
	exit (0);
}

$form_token_name = 'formToken4EditSection';
$form_token = hash_hmac ('md5', time (), $section_id);
$_SESSION [$form_token_name] = $form_token;

$doc_lang = substr ($project_id, -2);


$type_fragments = explode (":", $type_handle);
if (count ($type_fragments) >= 4) {
	/* defined wrapper and style */
	$content_type = $type_fragments [0];
	$content_format = $type_fragments [1];
	if ($content_type == 'code') {
		$content_code_lang = $content_format;
		$content_format = 'plain';
	}
	$content_wrapper = $type_fragments [2];
	$content_style = $type_fragments [3];
	if (isset ($type_fragments [4])) {
		$content_alignment = $type_fragments [4];
	}
	else {
		$content_alignment = 'none';
	}
}
else {
	if (strncmp ($type_handle, "code", 4) == 0) {
		$content_type = "code";
		$content_code_lang = substr ($type_handle, 5);
		$content_format = 'plain';
	}
	else {
		switch ($type_handle) {
		case 'markdown':
		case 'markdown_extra':
		case 'media_wiki':
			$content_type = 'plain';
			$content_format = $type_handle;
			$content_code_lang = 'plain';
			break;
		case 'plain':
		case 'pre':
		case 'quotation':
		case 'address':
			$content_type = $type_handle;
			$content_format = 'plain';
			$content_code_lang = 'plain';
			break;
		}
	}

	$content_wrapper = 'none';
	$content_style = 'none';
	$content_alignment = 'none';
}
?>

<form method="post" action="<?php echo $form_action ?>" enctype="multipart/form-data" class="validate form-horizontal">
	<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
	<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
	<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
	<input type="hidden" name="volumeHandle" value="<?php echo $section_info ['volume_handle'] ?>" />
	<input type="hidden" name="partHandle" value="<?php echo $section_info ['part_handle'] ?>" />
	<input type="hidden" name="chapterHandle" value="<?php echo $section_info ['chapter_handle'] ?>" />
	<input type="hidden" name="sectionID" value="<?php echo $section_id ?>" />

	<input type="hidden" name="formTokenName" value="<?php echo $form_token_name ?>" />
	<input type="hidden" name="formToken" value="<?php echo $form_token ?>" />

	<div class="form-group">
		<label for="contentType" class="col-md-4 control-label">
			<?php echo t('Content Type: ') ?>
		</label>
		<div class="col-md-4">
			<?php echo $form->select ('contentType', array (
					'plain' => t('Plain Section'),
					'pre' => t('Preformatted Text'),
					'code' => t('Code'),
					'quotation' => t('Quotation'),
					'quotation-reverse' => t('Quotation Reverse'),
					'address' => t('Address')), $content_type, array ("class" => "form-control")); ?>
		</div>
	</div>

	<div class="form-group">
		<label for="contentFormat" class="col-md-4 control-label">
			<?php echo t('Content Format: ') ?>
		</label>
		<div class="col-md-4">
			<?php echo $form->select ('contentFormat', array (
					'markdown' => 'Markdown',
					'markdown_extra' => 'Markdown Extra',
					'media_wiki' => 'Media Wiki Text',
					'plain' => 'Plain',
					), $content_format, array ("class" => "form-control")); ?>
		</div>
		<p>
			<?php echo t('Plain is only valid for Code and Preformatted type.') ?>
		</p>
	</div>

	<div class="form-group">
		<label for="contentCodeLang" class="col-md-4 control-label">
			<?php echo t('Code Language: ') ?>
		</label>
		<div class="col-md-4">
			<select name="contentCodeLang" class="form-control">
				<?php Loader::element('computer_language_list', array ('selected_value' => $content_code_lang)); ?>
			</select>
		</div>
		<p>
			<?php echo t('Only valid for Code type.') ?>
		</p>
	</div>

	<div class="form-group">
		<label for="contentWrapper" class="col-md-4 control-label">
			<?php echo t('Content Wrapper: ') ?>
		</label>
		<div class="col-md-4">
			<select name="contentWrapper" class="form-control">
				<?php Loader::element('content_wrapper_list', array ('selected_value' => $content_wrapper)); ?>
			</select>
		</div>
		<p>
			<?php echo t('Refer to <a target="_blank" href="http://getbootstrap.com">HERE</a> for Bootstrap.') ?>
		</p>
	</div>

	<div class="form-group">
		<label for="contentStyle" class="col-md-4 control-label">
			<?php echo t('Content Style: ') ?>
		</label>
		<div class="col-md-4">
			<select name="contentStyle" class="form-control">
				<?php Loader::element('content_style_list', array ('selected_value' => $content_style)); ?>
			</select>
		</div>
		<p>
			<?php echo t('Only valid when Wrapper is not none.') ?>
		</p>
	</div>

	<div class="form-group">
		<label for="contentAlignment" class="col-md-4 control-label">
			<?php echo t('Content Alignment: ') ?>
		</label>
		<div class="col-md-4">
			<select name="contentAlignment" class="form-control">
				<?php Loader::element('content_alignment_list', array ('selected_value' => $content_alignment)); ?>
			</select>
		</div>
		<p>
			<?php echo t('Only valid when Wrapper is not none.') ?>
		</p>
	</div>

	<div style="margin-bottom:15px;">
		<label for="sectionSubject"><?php echo t('Title (optional): ') ?></label>
		<input type="text" class="form-control" name="sectionSubject"  maxlength="40" style="width:100%;"
					value="<?php echo h5($section_subject) ?>"/>
	</div>

	<div style="margin-bottom:15px;">
		<label for="sectionContent">
			<?php echo t('Content (20 characters at least): ') ?>
		</label>
       	<?php echo $form->textarea ('sectionContent', $section_content,
				array ("required" => "true", "rows" => "7", "style" => "width:100%;")); ?>
	</div>

	<div id="fsen-addNewSection-fileRows">
	</div>

	<div style="margin-bottom:15px">
		<label for="attachmentFile0">
			<?php echo t('Attached Files: ') ?>
		</label>
<?php
		for ($i = 0; $i < ProjectInfo::MAX_ATTACHED_FILES; $i++) {
			if ($attached_files [$i] > 0) {
				$attached_file = File::getByID ($attached_files [$i]);
				echo $al->file ("attachmentFile$i", "attachmentFile$i", t('Choose another File'), $attached_file);
			}
			else {
				echo $al->file("attachmentFile$i", "attachmentFile$i", t('Upload or Choose File'));
			}
		}
?>
	</div>

	<input type="submit" name="fsen-add-section-submit" value="submit" style="display: none"
		id="fsen-form-submit-button" />

	<div class="ccm-buttons dialog-buttons">
		<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();"
			class="btn ccm-button-left cancel"><?php echo t('Cancel') ?></a>
		<a href="javascript:void(0)" onclick="$('#fsen-form-submit-button').get(0).click()"
			class="ccm-button-right accept btn primary"><?php echo t('Save') ?> <i class="icon-ok icon-white"></i></a>
	</div>
</form>
</div>

<?php
require_once(DIR_FILES_ELEMENTS_CORE . '/dialog_footer.php');
?>
