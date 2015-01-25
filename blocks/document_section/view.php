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

require_once ('helpers/fsen/DocSectionManager.php');
require_once ('helpers/fsen/FSEInfo.php');

$doc_lang = $_REQUEST['fsenDocLang'];
if (!isset ($doc_lang))
	$doc_lang = 'en';

$full_content_file_name = DocSectionManager::getSectionContentPath ($sectionID, $currentVerCode, 'html');
$html_content = file_get_contents ($full_content_file_name);

$full_content_file_name = DocSectionManager::getSectionContentPath ($sectionID, $currentVerCode, 'org');

$fp = fopen ($full_content_file_name, "r");
if ($fp) {
	$author_id = trim (fgets ($fp));
	$type_handle = trim (fgets ($fp));
	$attached_files = fgets ($fp);
	$content_subject = trim (fgets ($fp));
	$fstats = fstat($fp);
	$edit_time = date ('Y-m-d H:i', $fstats['mtime']) . ' CST';
	fclose ($fp);
	unset ($fp);

	$author_name_info = FSEInfo::getNameInfo ($author_id);
}

$json = Loader::helper('json');
$attached_files = $json->decode ($attached_files);
unset ($json);

$uh = Loader::helper('concrete/urls');
$bt = BlockType::getByHandle('document_section');
$toggle_praise_url = $uh->getBlockTypeToolsURL ($bt) . '/toggle_praise.php';
$toggle_favorite_url = $uh->getBlockTypeToolsURL ($bt) . '/toggle_favorite.php';
unset ($uh);
unset ($bt);

if ($html_content == false || is_array ($attached_files) == false) {
	echo "<p>Section ($sectionID) is bad or lost!</p>";
}
else if (strncmp ($type_handle, "post", 4) == 0) {
	include 'forum_post.php';
}
else {

	$nr_div_tags = 0;

	$request_part = "?fsenDocLang=$doc_lang&domainHandle=$domainHandle&sectionID=$sectionID&currentVerCode=$currentVerCode";
	if (strncmp ($type_handle, "feature", 7) == 0) {
		$edit_class= 'launch-modal';
		$edit_dialog_url = '/index.php/tools/edit_feature.php';
		$delete_dialog_url = '/index.php/tools/delete_section.php';
		$edit_dialog_title = t('Edit Feature');
		$delete_dialog_title = t('Delete Feature');
		$visible_class = 'visible-on-edit-document-right';
		echo '<div class="section-container">' . PHP_EOL;
		$nr_div_tags += 1;
	}
	else if (strncmp ($type_handle, "member", 6) == 0) {
		$edit_class= 'launch-modal';
		$edit_dialog_url = '/index.php/tools/edit_member.php';
		$delete_dialog_url = '/index.php/tools/delete_member.php';
		$edit_dialog_title = t('Edit Member Roles');
		$delete_dialog_title = t('Delete Member');
		$visible_class = 'visible-on-manage-member-right';
		echo '<div class="section-container">' . PHP_EOL;
		$nr_div_tags += 1;
	}
	else {
		$edit_class= 'dialog-launch';
		$edit_dialog_url = '/index.php/tools/edit_section.php';
		$delete_dialog_url = '/index.php/tools/delete_section.php';
		$edit_dialog_title = t('Edit Section');
		$delete_dialog_title = t('Delete Section');

		/* check if this block locates on blog page */
		$page = Page::getCurrentPage ();
		$page_path = $page->getCollectionPath ();
		$path_frags = explode ('/', trim ($page_path, '/'));
		if ($path_frags[1] == 'blog' && $path_frags[2] != 'na' && $path_frags[3] != 'na') {
			$visible_class = 'visible-on-logged-in';
		}
		else {
			$visible_class = 'visible-on-edit-document-right';
		}
		unset ($page);
		unset ($page_path);
		unset ($path_frags);

		$type_fragments = explode (":", $type_handle);
		if (count ($type_fragments) >= 4) {
			/* defined wrapper and style */
			if ($type_fragments[2] != 'none') {
				echo '<div class="' . $type_fragments[2] . '">' . PHP_EOL;
				$nr_div_tags += 1;
				$style = $type_fragments[3];
				if (isset ($type_fragments[4])) {
					$align = 'text-' . $type_fragments[4];
				}
				else {
					$align = '';
				}

				if ($style == 'well') {
					echo '<div class="section-container ' . "well $align" . '">' . PHP_EOL;
					$nr_div_tags += 1;
				}
				else {
					if ($style != 'none') {
						echo '<div class="section-container ' . "panel panel-$style" . '">' . PHP_EOL;
						if (strlen ($content_subject)) {
							echo '<div class="panel-heading"><h2 class="panel-title">';
							echo h5($content_subject);
							echo '</h2></div>';
						}
						echo '<div class="' . "panel-body $align" . '">' . PHP_EOL;
						$nr_div_tags += 2;
					}
					else {
						echo '<div class="section-container ' . "bg-$style $align" . '">' . PHP_EOL;
						$nr_div_tags += 1;
					}
				}
			}
			else {
				echo '<div class="col-md-12">' . PHP_EOL;
				echo '<div class="section-container">' . PHP_EOL;
				$nr_div_tags += 2;
			}
		}
		else {
			echo '<div class="col-md-12">' . PHP_EOL;
			echo '<div class="section-container">' . PHP_EOL;
			$nr_div_tags += 2;
		}
	}

?>
<section class="section-block" id="section-<?php echo $sectionID ?>">
<?php
echo $html_content . PHP_EOL;
if (count ($attached_files) > 0) {
	echo '<div class="row">' . PHP_EOL;
	foreach ($attached_files as $file_id) {
		$f = File::getByID ($file_id);
		if ($f instanceof File) {
			$fv = $f->getRecentVersion ();
			echo '<section class="col-xs-3 col-md-1 fixed-thumbnail" >' . PHP_EOL;
			echo '<a class="thumbnail" href="' . $fv->getURL() . '">';
			echo '<img class="attached-file" src="' . $fv->getThumbnail (1, false) . '"'
				. 'title="' . htmlspecialchars ($fv->getTitle (), ENT_QUOTES | ENT_HTML5) . '"'
				. 'data-desc="' . htmlspecialchars ($fv->getDescription (), ENT_QUOTES | ENT_HTML5) . '"'
				. 'data-type="' . $fv->getGenericTypeText() . '"'
				. 'data-mime-type="' . $fv->getMimeType() . '"'
				. 'data-value="' . $fv->getURL() . '" />' . PHP_EOL;
			echo '</a>' . PHP_EOL;
			echo '</section>' . PHP_EOL;
		}
	}
	echo '</div>' . PHP_EOL;
}
?>
</section>

<footer class="section-block">
	<section class="author-info">
		<p>
		<?php echo FSEInfo::getPersonalHomeLink ($author_name_info, true) . PHP_EOL ?>
		<?php echo $edit_time . PHP_EOL ?>
		</p>
	</section>
	<section class="block-left <?php echo $visible_class ?>">
		<p>
			<a title="<?php echo $edit_dialog_title ?>" class="<?php echo $edit_class ?>"
				dialog-append-buttons="true"
				dialog-modal="false"
				dialog-width="80%"
				dialog-height="90%"
				dialog-title="<?php echo $edit_dialog_title ?>"
				href="<?php echo $edit_dialog_url . $request_part ?>">&#9997;</a>
<?php
	if (strncmp ($type_handle, "member", 6) != 0) {
?>
			<a title="Change Version" class="launch-modal"
				href="/index.php/tools/change_section_version.php<?php echo $request_part ?>">&#8630;</a>
<?php
	}
?>
			<a title="<?php echo $delete_dialog_title ?>" class="launch-modal"
				href="<?php echo $delete_dialog_url . $request_part ?>">&#10006;</a>
		</p>
	</section>
	<section class="block-right">
		<ul class="list-comment-info" data-value="<?php echo $sectionID ?>">
			<li class="inline-list small">
				<a class="launch-modal section-shares"
						href="/index.php/tools/share_section.php<?php echo $request_part ?>">
					<span class="glyphicon glyphicon-new-window"></span>
					<span id="spanNrShares<?php echo $sectionID ?>"></span>
				</a>
			</li>
			<li class="inline-list small">
				<a class="launch-modal section-comments"
						tabindex="0"
						data-toggle="popover"
						href="/index.php/tools/section_comments.php<?php echo $request_part ?>">
					<span class="glyphicon glyphicon-comments"></span>
					<span id="spanNrComments<?php echo $sectionID ?>">0</span>
				</a>
			</li>
			<li class="inline-list small">
				<a id="aSectionPraise<?php echo $sectionID ?>" class="section-praise"
						data-author-name="<?php echo $author_name_info['user_name'] ?>"
						data-target="<?php echo $toggle_praise_url . $request_part ?>"
						href="/index.php/tools/section_action_comments.php<?php echo $request_part ?>">
					<span class="glyphicon glyphicon-thumbs-up"></span>
					<span id="spanNrPraise<?php echo $sectionID ?>">0</span>
				</a>
			</li>
			<li class="inline-list small">
				<a id="aSectionFavorites<?php echo $sectionID ?>" class="section-favorites"
						data-author-name="<?php echo $author_name_info['user_name'] ?>"
						data-target="<?php echo $toggle_favorite_url . $request_part ?>"
						href="/index.php/tools/section_action_comments.php<?php echo $request_part ?>">
					<span class="glyphicon glyphicon-heart"></span>
					<span id="spanNrFavorites<?php echo $sectionID ?>">0</span>
				</a>
			</li>
		</ul>
	</section>
</footer>
<?php
	for ($i = 0; $i < $nr_div_tags; $i++) {
		echo '</div>' . PHP_EOL;
	}
}
?>
