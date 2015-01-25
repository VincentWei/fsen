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
defined('C5_EXECUTE') or die("Access Denied.");

require_once ('helpers/fsen/FSEInfo.php');
require_once ('helpers/fsen/DocSectionManager.php');
require_once ('helpers/fsen/ProjectInfo.php');

Loader::model ('fsen_localization');
FSENLocalization::setupInterfaceLocalization4AjaxRequest ();

$txt = Loader::helper ('text');
$doc_lang = $_REQUEST ['fsenDocLang'];
$$nr_requested = (int)$txt->sanitize ($_GET ['nrRequested']);
if ($nr_requested <= 0 || $nr_requested > 20) {
	$nr_requested = 20;
}

$json = Loader::helper ('json');
class ReturnInfo {
	public $status;
	public $detail;
}

$ret_info = new ReturnInfo;
$ret_info->status = 'error';
$ret_info->detail = '';

if (!in_array ($doc_lang, array ('zh', 'en'))) {
	$ret_info->detail = t('Bad request!');
	echo $json->encode ($ret_info);
	exit (0);
}

$sections = Cache::get ('LatestCommentedSections', $doc_lang);
if ($sections == false) {
	$db = Loader::db ();
	$sections = $db->getAll ("SELECT id, author_id, curr_ver_code, page_id,
		nr_comments, nr_praise, nr_favorites,
		project_id, domain_handle, volume_handle, part_handle, chapter_handle
	FROM fsen_document_sections_$doc_lang WHERE heat_level > 0
	ORDER BY heat_level DESC, create_time DESC LIMIT 20");
	Cache::set ('LatestCommentedSections', $doc_lang, $sections, 180);
}

$ret_info->status = 'success';

$nr = 0;
foreach ($sections as $pst) {
	if ($nr >= $nr_requested) {
		break;
	}

	$author_info = FSEInfo::getNameInfo ($pst['author_id']);
	if ($author_info == false) {
		continue;
	}

	$comments = DocSectionManager::getCachedComments ($domain_handle, $pst['id']);
	if (count ($comments) == 0) {
		continue;
	}

	$plain_content = DocSectionManager::getPlainContent ($pst['id'], $pst['curr_ver_code']);
	$link = ProjectInfo::assemblePath ($pst['project_id'], $pst['domain_handle'],
					$pst['volume_handle'], $pst['part_handle'], $pst['chapter_handle']);
	$link .= '#section-' . $pst['id'];
	if (strlen ($plain_content['title']) == 0) {
		$page = Page::getByID ($pst['page_id']);
		$plain_content['title'] = $page->getCollectionName ();
	}

	$ret_info->detail .= '
<div class="panel panel-default">
<div class="panel-body">
	<div class="media" style="margin-top:15px">
		<a class="media-left" href="' . FSEInfo::getPersonalHomeLink($author_info) . '">
			<img class="middle-avatar" src="' . $author_info['avatar_url'] . '" alt="' . $author_info['nick_name'] . '">
		</a>
		<div class="media-body">
			<h4 class="media-heading">
				<a href="' . $link . '">' . h5($plain_content['title']) . '</a>
				<span class="label label-primary">' . $pst['nr_comments'] . '</span>
				<span class="label label-success">' . $pst['nr_praise']. '</span>
			</h4>
			<p>
				' . $plain_content['content'] . '
			</p>
			<hr/>';

	foreach ($comments as $comment) {
		$author_info = FSEInfo::getNameInfo ($comment['author_id']);
		if ($author_info == false) {
			continue;
		}

		$replied_name_info = false;
		if (preg_match ("/^[0-9a-f]{32}$/", $comment ['replied_author_id'])) {
			$replied_name_info = FSEInfo::getNameInfo ($comment ['replied_author_id']);
		}

		$ret_info->detail .= '
			<div class="media" style="margin-top:5px;">
				<a class="media-left" href="' . FSEInfo::getPersonalHomeLink($author_info) . '">
					<img class="small-avatar" src="' . $author_info['avatar_url'] . '"
							alt="' . $author_info['nick_name'] . '">
				</a>
				<div class="media-body">
					<p style="line-height: 1; margin-top:0; margin-bottom:0;">
						<small><strong class="text-info">' . $author_info['nick_name'] . '</strong></small>
					</p>
					<p style="line-height: 1; margin-top:0; margin-bottom:0;">
						<small>';
		if ($replied_name_info != false) {
			$ret_info->detail .= t('Reply to ') . '<strong class="text-info">' . $replied_name_info['nick_name'] . ': </strong>';
		}
		$ret_info->detail .= h5($comment['body']) . '</small>
					</p>
				</div>
			</div>';
	}

	$ret_info->detail .= '
		</div>
	</div>
</div>
</div>';

	$nr ++;
}

echo $json->encode ($ret_info);
exit (0);

?>

