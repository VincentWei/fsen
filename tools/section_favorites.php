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

require_once('helpers/check_login.php');
require_once('helpers/misc.php');
require_once('helpers/fsen/DocSectionManager.php');
require_once('helpers/fsen/ProjectInfo.php');

$domain_handle = $_REQUEST['domainHandle'];
$section_id = $_REQUEST['sectionID'];
$current_ver_code = $_REQUEST['currentVerCode'];

if (!fse_try_to_login ()) {
	$error_info = t('You are not signed in.');
}
else if (!preg_match ("/^[a-f0-9]{32}$/", $section_id)) {
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
		$author_name_info = false;
		$filename = DocSectionManager::getSectionContentPath ($section_id, $current_ver_code, 'org');
		$fp = fopen ($filename, "r");
		if ($fp) {
			$author_id = trim (fgets ($fp));
			$fstats = fstat($fp);
			$edit_time = date ('Y-m-d H:i', $fstats['mtime']) . ' CST';
			fclose ($fp);
			unset ($fp);
		}

		$curr_fse_id = $_SESSION['FSEInfo']['fse_id'];
		if ($author_id != $curr_fse_id) {
			$error_info = t('You are not the author of this section.');
		}
		else {
			$author_name_info = FSEInfo::getNameInfo ($author_id);
			$filename = DocSectionManager::getSectionContentPath ($section_id, $current_ver_code, 'html');
			$html_content = file_get_contents ($filename);
			if ($html_content == false || $author_name_info == false) {
				$error_info = t('Bad author or section content.');
			}
			else {
				$project_id = $section_info ['project_id'];
				$doc_lang = substr ($project_id, -2);
				$project_info = ProjectInfo::getBasicInfo ($project_id);
				if ($project_info == false) {
					$error_info = t('Bad project');
				}
				else {
					$uh = Loader::helper('concrete/urls');
					$bt = BlockType::getByHandle('document_section');
					$form_action = $uh->getBlockTypeToolsURL ($bt) . '/new_comment.php';
					$delete_action = $uh->getBlockTypeToolsURL ($bt) . '/delete_comment.php';
					$fetch_earlier_action = $uh->getBlockTypeToolsURL ($bt) . '/fetch_earlier_action_comments.php';
					$form_token = hash_hmac ('md5', time (), $section_id);
					$_SESSION ['formToken4CommentSection'] = $form_token;
					unset ($uh);
					unset ($bt);
				}
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
	<h4 class="modal-title"><?php echo t ('Praise') ?></h4>
</div>
<div class="modal-body">
<?php
if (isset ($error_info)) {
?>
	<p><?php echo $error_info ?></p>
<?php
}
else {
	$comments = DocSectionManager::getCachedActionComments ($domain_handle, $section_id);
?>
	<section class="panel panel-default">
		<div class="panel-heading">
			<div class="media">
				<a class="media-left" href="<?php echo FSEInfo::getPersonalHomeLink ($author_name_info) ?>"><img class="small-avatar" src="<?php echo $author_name_info['avatar_url'] ?>" alt="Avatar"></a>
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
					aria-expanded="false" aria-controls="sectionOriginalText"><?php echo t('Original Text') ?>
					<span class="caret"></span>
				</button>
			</div>
		</div> <!-- panel-heading -->

		<div class="panel-body">
			<section id="sectionOriginalText" class="collapse preview formal-font-style">
				<?php echo $html_content ?>
			</section>
		</div><!-- panel-body -->

		<div class="panel-footer" style="max-height:300px;overflow-y:scroll;">
			<ul id="ulComments" class="list-group">
<?php
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

		if ($comment['action'] == DocSectionManager::COMMENT_ACTION_FAVORITE) {
			$body = '<span class="glyphicon glyphicon-heart" style="color:#eb7350;"></span>';
		}
		else {
			$body = h5 ($comment['body']);
		}
?>
				<li id="liComment<?php echo $comment['id'] ?>" class="list-group-item"
						data-value="<?php echo $comment['id'] ?>">
					<div class="media">
						<span class="badge"><?php echo $author_name_info['heat_level'] ?></span>
						<a class="media-left" href="<?php echo FSEInfo::getPersonalHomeLink ($author_name_info) ?>">
							<img class="small-avatar" src="<?php echo $author_name_info['avatar_url'] ?>" alt="avatar">
						</a>
						<section class="media-body">
							<h6 class="media-heading">
								<?php echo FSEInfo::getPersonalHomeLink ($author_name_info, true) ?>
							</h6>
							<p><small>
								<?php echo (isset ($replied_name_info)? (t('Reply to ') . FSEInfo::getPersonalHomeLink ($replied_name_info, true) . ': ') : '') . $body ?>
							</small></p>
						</section><!-- media-body -->
						<footer class="comment-block">
							<div class="block-left">
								<p>
									<span class="glyphicon glyphicon-clock"></span> <?php echo $comment['create_time'] ?>
								</p>
							</div>
							<div class="block-right">
								<ul>
									<li><a class="reply-comment" href="#"
											data-name="<?php echo $author_name_info['nick_name'] ?>"
											data-value="<?php echo $comment['id'] ?>"><?php echo t('Reply') ?></a>
									</li>
<?php
			if ($comment['author_id'] == $curr_fse_id) {
?>
									<li><a href="<?php echo "$delete_action?domainHandle=$domain_handle&sectionID=$section_id&commentID=" . $comment['id'] ?>"
											data-value="<?php echo $comment['id'] ?>"
											class="delete-comment"><?php echo t('Delete') ?></a>
									</li>
<?php
			}
?>
								</ul>
							</div>
						</footer>
						<div role="divReplyFormPlaceholder">
						</div>
					</div><!-- media -->
				</li>
<?php
	}
?>
			</ul>
			<button type="button" id="btnFetchEarlier" class="btn btn-success btn-lg btn-block btn-sm"
					data-doing-text="<?php echo t ('Fetching...') ?>"
					data-nomore-text="<?php echo t ('No more') ?>"
					data-error-text="<?php echo t ('Error!') ?>"
					data-done-text="<?php echo t ('Fetched') ?>">
				<?php echo t('Fetch Earlier Comments...') ?>
			</button>
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

<div id="divFormReplyComment" class="alert alert-info" style="display:none;margin:10px 0;">
	<form id="formReplyComment" method="post" action="<?php echo $form_action ?>">
		<input type="hidden" name="fsenDocLang" value="<?php echo $doc_lang ?>" />
		<input type="hidden" name="domainHandle" value="<?php echo $domain_handle ?>" />
		<input type="hidden" name="sectionID" value="<?php echo $section_id ?>" />
		<input type="hidden" name="formTokenName" value="formToken4CommentSection" />
		<input type="hidden" name="formToken" value="<?php echo $form_token ?>" />

		<input type="hidden" name="commentReplyTo" value="0" />
		<input type="hidden" name="commentAction" value="<?php echo DocSectionManager::COMMENT_ACTION_TEXT ?>" />

		<div class="row">
			<div class="col-md-12">
				<div class="input-group">
					<input class="form-control" name="commentBody"
							required="true" placeholder="<?php echo t('Your Reply') ?>" />
					<span class="input-group-btn">
						<button id="btnReplyComment" class="btn btn-primary" type="submit"
								data-doing-text="<?php echo t ('Posting...') ?>"
								data-done-text="<?php echo t ('Posted') ?>"><?php echo t('Reply') ?></button>
					</span>
				</div><!-- /input-group -->
			</div><!-- /.col-md-12 -->
		</div><!-- row -->
	</form>
</div>

<script lang="JavaScript">
function reply_comment (e)
{
	e.preventDefault();
	var $form_placeholder = $(this).parents('footer.comment-block').next();
	$('#divFormReplyComment').detach().appendTo($form_placeholder);
	$('#divFormReplyComment').show();

	$('#formReplyComment input[name=commentReplyTo]').val ($(this).attr('data-value'));
}

function delete_comment (e)
{
	e.preventDefault ();
	var href = $(this).attr ('href');
	$.get ($(this).attr ('href'), function (data) {
		var ret_info = eval ('(' + data + ')');
		if (ret_info.status == 'success') {
			$('#liComment' + ret_info.detail).remove ();
			var curr_nr_comments = parseInt ($('#spanNrComments' + '<?php echo $section_id ?>').text ());
			curr_nr_comments -= 1;
			if (curr_nr_comments < 0)
				curr_nr_commnets = 0;
			$('#spanNrComments' + '<?php echo $section_id ?>').text (curr_nr_comments);
		}
	});
}

$('.reply-comment').click (reply_comment);
$('.delete-comment').click (delete_comment);

$('#formReplyComment').submit (function (e) {
	e.preventDefault();

	$('#btnReplyComment').attr ('disabled', 'disabled');
	$('#btnReplyComment').bootstrapBtn ('doing');

	/* post comment via Ajax */
	var options = {
		success:			function (data) {
			var ret_info = eval ('(' + data + ')');
			$('#btnReplyComment').removeAttr ('disabled');
			$('#btnReplyComment').bootstrapBtn ('reset');
			if (ret_info.status == 'success') {
				$('#ulComments').prepend (ret_info.detail);
				$('.reply-comment').click (reply_comment);
				$('.delete-comment').click (delete_comment);
				var curr_nr_comments = parseInt ($('#spanNrComments' + '<?php echo $section_id ?>').text ());
				curr_nr_comments += 1;
				$('#spanNrComments' + '<?php echo $section_id ?>').text (curr_nr_comments);
			}
			else {
				$('#btnReplyComment').popover ({title: 'Error Occured',
						content: ret_info.detail, triggers: 'manual focus', placement: 'top'});
				$('#btnReplyComment').popover ('show');
			}
		},
		error:				function (data) {
			alert (data.responseText);
			$('#btnReplyComment').removeAttr ('disabled');
			$('#btnReplyComment').bootstrapBtn ('reset');
		},
		clearForm:			true,
		resetForm:			true,
		timeout:			5000
	};
	$(this).ajaxSubmit (options);

	return false;
});

$('#btnFetchEarlier').click (function (e) {

	$('#btnFetchEarlier').attr ('disabled', 'disabled');
	$('#btnFetchEarlier').bootstrapBtn ('doing');

	var oldest_comment_id = $('#ulComments').children('li:last-child').attr ('data-value');
	/* fetch earlier comments via Ajax */
	$.ajaxSetup ({async: true});
	$.get ('<?php echo $fetch_earlier_action . "?domainHandle=$domain_handle&sectionID=$section_id&oldestCommentID=" ?>' + oldest_comment_id, function (data) {
		var ret_info = eval ('(' + data + ')');
		if (ret_info.status == 'success') {
			$('#ulComments').append (ret_info.detail);
			$('.reply-comment').click (reply_comment);
			$('.delete-comment').click (delete_comment);
			$('#btnFetchEarlier').removeAttr ('disabled');
			$('#btnFetchEarlier').bootstrapBtn ('reset');
		}
		else if (ret_info.status == 'nodata') {
			$('#btnFetchEarlier').bootstrapBtn ('nomore');
		}
		else if (ret_info.status == 'error') {
			$('#btnFetchEarlier').bootstrapBtn ('error');
		}
	});
});
</script>
