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

require_once ('helpers/check_login.php');
require_once ('helpers/misc.php');
require_once ('helpers/fsen/FSEInfo.php');
require_once ('helpers/fsen/DocSectionManager.php');

Loader::model ('fsen_localization');
FSENLocalization::setupInterfaceLocalization4AjaxRequest ();

$txt = Loader::helper ('text');
$domain_handle = $txt->sanitize ($_POST ['domainHandle']);
$section_id = $txt->sanitize ($_POST ['sectionID']);
$form_token_name = $txt->sanitize ($_POST ['formTokenName']);
$form_token = $txt->sanitize ($_POST ['formToken']);
$comment_action = (int)$txt->sanitize ($_POST ['commentAction']);
$comment_body = $_POST ['commentBody'];
$comment_reply_to = (int)$txt->sanitize ($_POST ['commentReplyTo']);

$json = Loader::helper ('json');
class ReturnInfo {
	public $status;
	public $detail;
}

$ret_info = new ReturnInfo;
$ret_info->status = 'error';
$ret_info->detail = t('N/A');

if (!fse_try_to_login ()) {
	$ret_info->detail = t('Not signed in.');
	echo $json->encode ($ret_info);
	exit (0);
}

if ($_SESSION [$form_token_name] != $form_token) {
	$ret_info->detail = t('Bad request or session expired.');
	echo $json->encode ($ret_info);
	exit (0);
}
//unset ($_SESSION [$form_token_name]);

$comment = DocSectionManager::newComment ($domain_handle, $section_id,
		$_SESSION['FSEInfo']['fse_id'], $comment_action, $comment_body, $comment_reply_to);

if ($comment == false) {
	$ret_info->status = 'error';
	$ret_info->detail = t('Bad comment');
	echo $json->encode ($ret_info);
	exit (0);
}

$author_info = $_SESSION['FSEInfo'];
if (!isset ($fse_info['avatar_url'])) {
	$author_info['avatar_url'] = get_url_from_file_id ($author_info['avatar_file_id']);
}

$uh = Loader::helper('concrete/urls');
$bt = BlockType::getByHandle('document_section');
$delete_action = $uh->getBlockTypeToolsURL ($bt) . '/delete_comment.php';

if (preg_match ("/^[0-9a-f]{32}$/", $comment ['replied_author_id'])) {
	$replied_name_info = FSEInfo::getNameInfo ($comment ['replied_author_id']);
	if ($replied_name_info == false) {
		unset ($replied_name_info);
	}
}

$ret_info->status = 'success';
$ret_info->detail = '
<li id="liComment' . $comment['id'] . '" class="list-group-item">
	<div class="media">
		<span class="badge">' . $author_info['heat_level'] . '</span>
		<a class="media-left" href="#">
			<img class="small-avatar" src="' . $author_info['avatar_url'] . '" alt="avatar">
		</a>
		<section class="media-body">
			<h5 class="media-heading">
				' . FSEInfo::getPersonalHomeLink ($author_info, true) . '
			</h5>
			<p><small>
				' . (isset ($replied_name_info)?(t('Reply to ').FSEInfo::getPersonalHomeLink($replied_name_info, true) . ': '):'') . '
				' . h5 ($comment['body']) . '
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
							data-name="' . $author_info['nick_name'] . '"
							data-value="' . $comment['id'] . '">' . t('Reply') . ' </a>
					</li>
					<li><a href="' . "$delete_action?domainHandle=$domain_handle&sectionID=$section_id&commentID=" . $comment['id'] . '"' . '
							data-value="' . $comment['id'] . '"
							class="delete-comment">' . t('Delete') . '</a>
					</li>
				</ul>
			</div>
		</footer>
		<div role="divReplyFormPlaceholder">
		</div>
	</div><!-- media -->
</li>
';
echo $json->encode ($ret_info);
exit (0);

?>

