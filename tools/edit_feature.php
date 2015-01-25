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
else if (preg_match ("/^[a-f0-9]{32}$/", $section_id) && in_array ($domain_handle, ProjectInfo::$mDomainList)) {
	$db = Loader::db ();
	$section_info = DocSectionManager::getSectionInfo ($domain_handle, $section_id);
	if (count ($section_info) == 0) {
		$error_info = t('No such section ID!');
	}
	else if ($current_ver_code > $section_info['max_ver_code']) {
		$error_info = t('Bad request!');
	}
	else {
		$volume_handle = $section_info ['volume_handle'];
		$project_id = $section_info ['project_id'];
		$doc_lang = substr ($project_id, -2);
		$project_info = ProjectInfo::getBasicInfo ($project_id);
		if ($project_info == false) {
			$error_info = t('Bad project');
		}
		else if (substr (ProjectInfo::getUserRights ($project_id, $_SESSION['FSEInfo']['fse_id']), 1, 1) != 't') {
			$error_info = t('You have no right to edit content of this project!');
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
			else if (strncmp ($type_handle, "feature", 7) != 0) {
				$error_info = t('Section is not feature!');
			}
			else {
				if ($attached_files [0] > 0) {
					$attached_file_0 = File::getByID ($attached_files [0]);
				}
				$type_fragments = explode (":", $type_handle);
				if (count ($type_fragments) >= 4) {
					$feature_icon = $type_fragments[1];
				}
				else {
					$feature_icon = substr ($type_handle, 8);
				}
			}
		}
	}
}
else {
	$error_info = t('Bad Request!');
}

if (!isset ($error_info)) {
	if ($domain_handle == 'home') {
		$form_action = "/fse_settings/projects/edit_section";
	}
	else {
		$error_info = t('Bad domain or volume!');
	}
}
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">
		<span aria-hidden="true">&times;</span>
		<span class="sr-only"><?php echo t ('Close') ?></span>
	</button>
	<h4 class="modal-title"><?php echo t ('Edit Feature') ?></h4>
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
	<form id="formEditFeature" method="post" action="<?php echo $form_action ?>" class="validate form-horizontal">
		<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
		<input type="hidden" name="projectID" value="<?php echo $project_id ?>" />
		<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
		<input type="hidden" name="volumeHandle" value="<?php echo $volume_handle ?>" />
		<input type="hidden" name="sectionID" value="<?php echo $section_id ?>" />
		<input type="hidden" name="contentType" value="feature" />
		<input type="hidden" name="contentFormat" value="<?php echo $feature_icon ?>" />
		<input type="hidden" name="contentWrapper" value="none" />
		<input type="hidden" name="contentStyle" value="none" />
		<input type="hidden" name="contentAlignment" value="none" />

		<div class="form-group">
			<label for="featureIcon" class="col-md-4 control-label"><?php echo t('Feature Icon')?></label>
			<div class="col-md-8">
				<div class="dropdown">
					<button type="button" class="btn dropdown-toggle" id="dropdownMenuGlyphicon" data-toggle="dropdown">
						<span class="glyphicon glyphicon-<?php echo $feature_icon ?>"></span>  <span class="caret"></span>
					</button>

					<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenuGlyphicon">
		<?php
			$glyphicon_list = array ('heart-empty', 'heart', 'glass', 'leaf', 'magic', 'beach-umbrella',
						'music', 'note', 'fire', 'magnet', 'binoculars', 'road', 'search', 'coins', 'airplane',
						'star-empty', 'star', 'lightbulb', 'umbrella', 'book', 'bookmark', 'cogwheel', 'cogwheels',
						'adjust', 'adjust-alt', 'more', 'electricity', 'euro', 'usd', 'yen', 'gbp', 'sun', 'globe',
						'thumbs-up', 'crown', 'cloud-download', 'cloud-upload', 'send', 'tractor',
						'circle-exclamation-mark', 'circle-ok', 'circle-info', 'circle-question-mark', 'iphone');
			foreach ($glyphicon_list as $glyphicon) {
		?>
						<li role="presentation">
							<a class="menuitem" data-value="<?php echo $glyphicon ?>"
									role="menuitem" tabindex="-1" href="#">
								<span class="glyphicon glyphicon-<?php echo $glyphicon ?>"></span>
								<?php echo $glyphicon ?>
							</a>
						</li>
		<?php
			}
		?>
					</ul>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="sectionSubject" class="col-md-4 control-label"><?php echo t('Subject: ') ?></label>
			<div class="col-md-8">
					<?php echo  $form->text ('sectionSubject', $section_subject,
						array ("required" => "true", "pattern" => ".{2,40}", "class" => " form-control")); ?>
			</div>
		</div>

		<div class="form-group">
			<label for="sectionContent" class="col-md-4 control-label"><?php echo t('Content: ') ?></label>
			<div class="col-md-8">
					<?php echo  $form->text ('sectionContent', $section_content,
						array ("required" => "true", "pattern" => ".{10,255}", "class" => " form-control")); ?>
				<p class="help-block"><?php echo t('10 characters at least; Markdown enabled.') ?></p>
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
	<button id="btnEditFeature" type="button" class="btn btn-primary"
			data-doing-text="<?php echo t ('Submitting...') ?>"
			data-done-text="<?php echo t ('Done') ?>">
		<?php echo t ('Ok') ?>
	</button>
<?php
}
?>

<script type="text/javascript">
$('.dropdown-toggle').dropdown();
$('.menuitem').click (function (event) {
	event.preventDefault ();
	var glyphicon_name = $(this).attr ('data-value');
	$('#dropdownMenuGlyphicon').html ('<span class="glyphicon glyphicon-' + glyphicon_name + '"></span>  <span class="caret"></span>');
	$('#dropdownMenuGlyphicon').dropdown('toggle');
	$('#formEditFeature input[name="contentFormat"]').val (glyphicon_name);
});

$('#formEditFeature').submit (function (e) {
	$('#btnEditFeature').attr ('disabled', 'disabled');
	$('#btnEditFeature').bootstrapBtn ('doing');
});

$('#btnEditFeature').click (function () {
	$('#formEditFeature input[name="inputHiddenSubmit"]').click();
});

</script>

