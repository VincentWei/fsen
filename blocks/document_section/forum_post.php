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

/*
$page = Page::getCurrentPage ();
$page_path = $page->getCollectionPath ();
$page_id = $page->getCollectionID ();
$path_frags = explode ('/', trim ($page_path, '/'));
$project_id = $path_frags[1];
*/

if ($currentVerCode > 0) {
	$last_author_id = $author_id;
	$last_edit_time = $edit_time;
	$last_author_name_info = FSEInfo::getNameInfo ($last_author_id);

	$full_content_file_name = DocSectionManager::getSectionContentPath ($sectionID, 0, 'org');
	$fp = fopen ($full_content_file_name, "r");
	if ($fp) {
		$author_id = trim (fgets ($fp));
		$fstats = fstat($fp);
		$edit_time = date ('Y-m-d H:i', $fstats['mtime']) . ' CST';
		fclose ($fp);
		unset ($fp);

		$author_name_info = FSEInfo::getNameInfo ($author_id);
	}
}

$request_part = "?fsenDocLang=$doc_lang&domainHandle=$domainHandle&sectionID=$sectionID&currentVerCode=$currentVerCode";
$visible_class = 'visible-on-logged-in';
$link_style = false;

$type_fragments = explode (":", $type_handle);
switch ($type_fragments[0]) {
	case 'post-question':
		$panel_style = 'panel-primary';
		$link_style = 'bg-primary';
		break;
	case 'post-answer':
		$panel_style = 'panel-success';
		break;
	case 'post-note':
		$panel_style = 'panel-info';
		break;
	case 'post-reference':
		$panel_style = 'panel-warning';
		break;
	default:
		$panel_style = 'panel-default';
		break;
}
unset ($type_fragments);
?>

<div style="position:relative;padding-bottom:20px;">
<section class="panel <?php echo $panel_style ?>" id="section-<?php echo $sectionID ?>">
<div class="panel-heading">
	<div class="media">
		<a class="media-left" href="<?php echo FSEInfo::getPersonalHomeLink ($author_name_info) ?>"><img class="middle-avatar" src="<?php echo $author_name_info['avatar_url'] ?>" alt="Avatar"></a>
		<div class="media-body">
			<div class="post-heading">
				<h4>
					<?php if (strlen($content_subject)) echo h5($content_subject); else echo t('(No title)');?> 
				</h4>
				<p>
					<small><span class="glyphicon glyphicon-pen"></span>
					<?php echo FSEInfo::getPersonalHomeLink ($author_name_info, true, $link_style) ?>
					<span class="glyphicon glyphicon-clock"></span>
					<?php echo $edit_time ?></small>
				</p>

<?php
if (isset ($last_author_id)) {
?>
				<p>
					<small><span class="glyphicon glyphicon-edit"></span>
					<?php echo FSEInfo::getPersonalHomeLink ($last_author_name_info, true, $link_style) ?>
					<span class="glyphicon glyphicon-clock"></span>
					<?php echo $last_edit_time ?></small>
				</p>
<?php
}
?>
			</div>
		</div>
	</div>
</div>

<div class="panel-body">
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
</div>
</section>

<footer class="section-block" style="bottom:-5px;">
	<section class="block-left <?php echo $visible_class ?>">
		<p>
			<a title="Edit Post" class="launch-modal"
				href="/index.php/tools/edit_post.php<?php echo $request_part ?>">&#9997;</a>
<?php
if ($currentVerCode > 0) {
?>
			<a title="Undo Last Edit" class="launch-modal"
				href="/index.php/tools/recover_post.php<?php echo $request_part ?>">&#8630;</a>
<?php
}
?>
			<a title="Delete Post" class="launch-modal"
				href="/index.php/tools/delete_post.php<?php echo $request_part ?>">&#10006;</a>
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
</div>
