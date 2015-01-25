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

$uh = Loader::helper('concrete/urls');
$bt = BlockType::getByHandle('document_section');
$fetch_comment_info_action = $uh->getBlockTypeToolsURL ($bt) . '/fetch_section_comment_info.php?fsenDocLang=' . $doc_lang;
$fetch_latest_comments = $uh->getBlockTypeToolsURL ($bt) . '/fetch_latest_comments.php?fsenDocLang=' . $doc_lang;
?>

<div id="globalModal" class="full-stack modal fade" tabindex="-1"
		role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" lang="<?php echo $doc_lang ?>">
	<div class="modal-dialog">
		<div class="modal-content">
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script lang="JavaScript">
$(document).ready (function() {
	$.ajaxSetup({async:true});

	$('.launch-modal').click (function (event) {
		event.preventDefault ();
		var href = $(this).attr ('href');
		$('#globalModal div.modal-content').load (href, function () {
			$("#globalModal").modal ();
		});
	});

	$('.section-comments').hover (function (e) {
		var is_mobile = '<?php echo $is_mobile_theme ?>';
		if (is_mobile == 'true') {
			return;
		}

		var domain_handle = '<?php echo $domain_handle ?>';
		var section_id = $(this).parent().parent().attr ('data-value');
		$.get ('<?php echo $fetch_latest_comments ?>' + '&domainHandle=' + domain_handle + '&sectionID=' + section_id + '&nrComments=5', function (data) {
				var ret_info = eval ('(' + data + ')');
				if (ret_info.status == 'success' && ret_info.nr_total_comments > 0) {
					$('#spanNrComments' + ret_info.section_id).text (ret_info.nr_total_comments);
					var $a = $('#spanNrComments' + ret_info.section_id).parent();
					$a.popover ({trigger: 'manual', placement: 'top', html: true,
								title: ret_info.title, content: ret_info.detail});
					$a.popover ('show');
				}
			});
		}, function (e) {
			var is_mobile = '<?php echo $is_mobile_theme ?>';
			if (is_mobile == 'true') {
				return;
			}

			var domain_handle = '<?php echo $domain_handle ?>';
			$(this).popover ('destroy');
		}
	);

	$('.list-comment-info').each (function () {
		var domain_handle = '<?php echo $domain_handle ?>';
		var section_id = $(this).attr ('data-value');
		$.get ('<?php echo $fetch_comment_info_action ?>' + '&domainHandle=' + domain_handle + '&sectionID=' + section_id, function (data) {
			var ret_info = eval ('(' + data + ')');
			if (ret_info.status == 'success') {
				if (ret_info.comment_info.nr_shares > 0) {
					$('#spanNrShares' + ret_info.comment_info.section_id).text (ret_info.comment_info.nr_shares);
				}
				if (ret_info.comment_info.nr_comments > 0) {
					$('#spanNrComments' + ret_info.comment_info.section_id).text (ret_info.comment_info.nr_comments);
				}
				$('#spanNrPraise' + ret_info.comment_info.section_id).text (ret_info.comment_info.nr_praise);
				if (ret_info.comment_info.praised != 0) {
					$('#aSectionPraise' + ret_info.comment_info.section_id).css ('color', '#eb7350');
				}
				$('#spanNrFavorites' + ret_info.comment_info.section_id).text (ret_info.comment_info.nr_favorites);
				if (ret_info.comment_info.favorited != 0) {
					$('#aSectionFavorites' + ret_info.comment_info.section_id).css ('color', '#eb7350');
				}
			}
		});
	});

	$.get ("/fse_login/login/<?php echo "$is_mobile_theme/$project_id?fsenDocLang=$doc_lang" ?>", function (data) {
		var obj = eval ('(' + data + ')');

		$('#ulGlobalNavList').append (obj.detail);
		after_get_login_status (obj, <?php echo $is_mobile_theme ?>);

		if (obj.status == 'ok') {
			$('.section-praise').click (function (event) {
				event.preventDefault ();
				if ($(this).attr ('data-author-name') == obj.user_name) {
					var href = $(this).attr ('href');
					$('#globalModal div.modal-content').load (href, function () {
						$("#globalModal").modal ();
					});
				}
				else {
					var href = $(this).attr ('data-target');
					$.get (href, function (data) {
						var ret_info = eval ('(' + data + ')');
						if (ret_info.status == 'praised') {
							$('#aSectionPraise' + ret_info.section_info.id).css ('color', '#eb7350');
						}
						else if (ret_info.status == 'canceled') {
							$('#aSectionPraise' + ret_info.section_info.id).css ('color', '#ccc');
						}
						$('#spanNrPraise' + ret_info.section_info.id).text (ret_info.section_info.nr_praise);
					});
				}
			});

			$('.section-favorites').click (function (event) {
				event.preventDefault ();
				if ($(this).attr ('data-author-name') == obj.user_name) {
					var href = $(this).attr ('href');
					$('#globalModal div.modal-content').load (href, function () {
						$("#globalModal").modal ();
					});
				}
				else {
					var href = $(this).attr ('data-target');
					$.get (href, function (data) {
						var ret_info = eval ('(' + data + ')');
						if (ret_info.status == 'favorited') {
							$('#aSectionFavorites' + ret_info.section_info.id).css ('color', '#eb7350');
						}
						else if (ret_info.status == 'canceled') {
							$('#aSectionFavorites' + ret_info.section_info.id).css ('color', '#ccc');
						}
						$('#spanNrFavorites' + ret_info.section_info.id).text (ret_info.section_info.nr_favorites);
					});
				}
			});
		}
		else {
			$('.section-praise').click (function (event) {
				event.preventDefault ();
				var href = $(this).attr ('href');
				$('#globalModal div.modal-content').load (href, function () {
					$("#globalModal").modal ();
				});
			});

			$('.section-favorites').click (function (event) {
				event.preventDefault ();
				var href = $(this).attr ('href');
				$('#globalModal div.modal-content').load (href, function () {
					$("#globalModal").modal ();
				});
			});
		}
	});

});
</script>

