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
$current_ver_code = $_REQUEST['currentVerCode'];

require_once('helpers/misc.php');
require_once('helpers/fsen/DocSectionManager.php');
require_once('helpers/fsen/ProjectInfo.php');

if (!preg_match ("/^[a-f0-9]{32}$/", $section_id)) {
	$error_info = t('Bad section.');
}
else if (!in_array ($domain_handle, ProjectInfo::$mDomainList)) {
	$error_info = t('Bad domain.');
}
else {
	$section_info = DocSectionManager::getSectionInfo ($domain_handle, $section_id);

	if (count ($section_info) == 0) {
		$error_info = t('No such section!');
	}
	else if ($section_info['status'] != 0) {
		$error_info = t('The section has been deleted or shielded.');
	}
	else {
		$page_subject = Page::getByID ($section_info ['page_id'])->getCollectionName ();
		$author_name_info = false;
		$filename = DocSectionManager::getSectionContentPath ($section_id, $current_ver_code, 'org');
		$fp = fopen ($filename, "r");
		if ($fp) {
			$author_id = trim (fgets ($fp));
			$tmp = trim (fgets ($fp));
			$tmp = fgets ($fp);
			$content_subject = trim (fgets ($fp));
			$fstats = fstat($fp);
			$edit_time = date ('Y-m-d H:i', $fstats['mtime']) . ' CST';
			fclose ($fp);
			unset ($fp);
			unset ($fstats);
			unset ($tmp);

			$author_name_info = FSEInfo::getNameInfo ($author_id);
		}
		$filename = DocSectionManager::getSectionContentPath ($section_id, $current_ver_code, 'html');
		$html_content = file_get_contents ($filename);

		if ($html_content == false || $author_name_info == false) {
			$error_info = t('Bad author or section content.');
		}
		else {
			$author_info = FSEInfo::getBasicProfile ($author_name_info['user_name']);
			$project_id = $section_info ['project_id'];
			$doc_lang = substr ($project_id, -2);
			$project_info = ProjectInfo::getBasicInfo ($project_id);
			if ($project_info == false) {
				$error_info = t('Bad project');
			}
			else {
				$curr_fse_id = $_SESSION['FSEInfo']['fse_id'];
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
	<h4 class="modal-title"><?php echo t ('Share to ...') ?></h4>
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
	<section class="panel panel-default">
		<div class="panel-heading">
			<div class="media">
				<a class="media-left" href="<?php echo FSEInfo::getPersonalHomeLink ($author_name_info) ?>"><img class="small-avatar" src="<?php echo $author_info['avatar_url'] ?>" alt="Avatar"></a>
				<div class="media-body">
					<div class="post-heading">
						<h4>
							<?php echo FSEInfo::getPersonalHomeLink ($author_name_info, true) ?>
						</h4>
						<p>
							<small><span class="glyphicon glyphicon-clock"></span>
							<?php echo $edit_time ?></small>
						</p>

					</div>
				</div>
			</div> <!-- media -->

			<div class="toggle-button btn-group">
				<button type="button" class="btn btn-default" data-toggle="collapse" data-target="#sectionOriginalText"
					aria-expanded="true" aria-controls="sectionOriginalText"><?php echo t('Original Text') ?>
					<span class="caret"></span>
				</button>
			</div>
		</div> <!-- panel-heading -->

		<div class="panel-body">
			<section id="sectionOriginalText" class="collapse in preview formal-font-style">
				<?php echo $html_content ?>
			</section>

		</div><!-- panel-body -->

		<div class="panel-footer">
			<ul id="listComments" class="list-group">
			<wb:share-button addition="full" type="button" picture_search="false" language="zh_cn"
				default_text="<?php
									$tmp = strip_tags($html_content);
									$tmp = t('[FSEN]') . h5($page_subject) . ' ' . h5($content_subject) . ' ' . $tmp;
									if (mb_strlen ($tmp) > 200) {
										$tmp = mb_substr ($tmp, 0, 200) . '…';
									}
									echo $tmp;
								?>">
			</wb:share-button>
			</ul>
		</div><!-- panel-footer -->

	</section>
<?php
}
?>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">
		<?php echo t ('Cancel') ?>
	</button>
</div>

<script src="http://tjs.sjs.sinajs.cn/open/api/js/wb.js" type="text/javascript" charset="utf-8"></script>

