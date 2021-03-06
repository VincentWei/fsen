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
$domain_handle = $txt->sanitize ($_GET ['domainHandle']);
$section_id = $txt->sanitize ($_GET ['sectionID']);
$oldest_comment_id = (int)$txt->sanitize ($_GET ['oldestCommentID']);

$json = Loader::helper ('json');
class ReturnInfo {
	public $status;
	public $detail;
}

$ret_info = new ReturnInfo;
$ret_info->status = 'error';
$ret_info->detail = t('N/A');

if (!in_array ($domain_handle, ProjectInfo::$mDomainList)) {
	$ret_info->detail = t('Bad domain');
	echo $json->encode ($ret_info);
	exit (0);
}

if (!preg_match ("/^[a-f0-9]{32}$/", $section_id)) {
	$ret_info->detail = t('Bad section');
	echo $json->encode ($ret_info);
	exit (0);
}

$comments = DocSectionManager::getEarlierComments ($domain_handle, $section_id, $oldest_comment_id);
if (count ($comments) == 0) {
	$ret_info->status = 'nodata';
	$ret_info->detail = '';
	echo $json->encode ($ret_info);
	exit (0);
}

$ret_info->status = 'success';
$ret_info->detail = '';
foreach ($comments as $comment) {
	$author_name_info = FSEInfo::getNameInfo ($comment['author_id']);
	if ($author_name_info == false) {
		continue;
	}

	unset ($replied_name_info);
	if (preg_match ("/^[0-9a-f]{32}$/", $comment ['replied_author_id'])) {
		$replied_name_info = FSEInfo::getNameInfo ($comment ['replied_author_id']);
		if ($replied_name_info == false) {
			unset ($replied_name_info);
		}
	}

	$ret_info->detail .= '
<li id="liComment' . $comment['id'] . '" class="list-group-item" data-value="' . $comment['id'] . '">
	<div class="media">
		<span class="badge">14</span>
			<a class="media-left" href="#">
				<img class="small-avatar" src="' . $author_name_info['avatar_url'] . '" alt="avatar">
			</a>
		<section class="media-body">
			<h6 class="media-heading">
				' . FSEInfo::getPersonalHomeLink ($author_name_info, true) . '
			</h6>
			<p><small>
				' . (isset ($replied_name_info)?(t('Reply to ') . FSEInfo::getPersonalHomeLink ($replied_name_info, true) . ': ') : '') . h5 ($comment['body']) . '
			</small></p>
		</section><!-- media-body -->
		<footer class="comment-block">
			<div class="block-left">
				<p>
					<span class="glyphicon glyphicon-clock"></span> ' . $comment['create_time'] . '
				</p>
			</div>
			<div class="block-right">
				<ul>
					<li><a class="reply-comment" href="#"
							data-name="' . $author_name_info['nick_name'] . '"
							data-value="' . $comment['id'] . '">' . t('Reply') . '</a>
					</li>';

	if ($comment['author_id'] == $curr_fse_id) {
		$ret_info->detail .= '
					<li><a href="' . "$delete_action?domainHandle=$domain_handle&sectionID=$section_id&commentID=" . $comment['id'] . '"
							data-value="' . $comment['id'] . '"
							class="delete-comment">' . t('Delete') . '</a>
					</li>';
	}

	$ret_info->detail .= '
				</ul>
			</div>
		</footer>
		<div role="divReplyFormPlaceholder">
		</div>
	</div><!-- media -->
</li>';
}

echo $json->encode ($ret_info);
exit (0);

?>

