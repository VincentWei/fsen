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

$this->inc('inc/head.php');

$doc_lang = $this->controller->get ('fsenDocLang');
if (!isset ($doc_lang)) {
	$doc_lang = 'en';
}
?>
<body>
<div class="full-stack">

	<header class="global-header">
		<section class="header-block block-left">
			<a href="/" title="Logo" id="logo">
				<p>To be</p>
				<p><span>Full</span> <span>Stack</span> <span>Engineer</span></p>
			</a>
		</section>
		<section class="header-block block-right">
			<nav>
				<ul id="GlobalNavList">
					<li>
						<a class="inline-list"
								href="/<?php echo $doc_lang ?>/project" ><?php echo t('Projects') ?></a>
					</li>
					<li>
						<a class="inline-list"
								href="/<?php echo $doc_lang ?>/community"><?php echo t('Code Arena') ?></a>
						</li>
					<li>
						<a class="inline-list"
								href="/<?php echo $doc_lang ?>/blog"><?php echo t('Blogs') ?></a>
					</li>
					<li>
						<a class="inline-list"
								href="/<?php echo $doc_lang ?>/help"><?php echo t('Help') ?></a>
					</li>
					<li>
						<a class="inline-list"
								href="/<?php echo $doc_lang ?>/engineer/<?php echo $_SESSION['FSEInfo']['user_name'] ?>"
								title="Personal homepage">
							<span class="glyphicon glyphicon-user"></span>
							<?php echo $_SESSION['FSEInfo']['nick_name'] ?></a>
					</li>
					<li>
						<a class="inline-list only-icon" href="/fse_logout/logout"
								title="Sign out">
							<span class="glyphicon glyphicon-log-out"></span></a></li>
				</ul>
			</nav>
		</section>
	</header>

	<div class="v-seperator">
	</div>

	<article class="ccm-ui">
<?php
if (isset($error)) {
	if ($error instanceof Exception) {
		$_error[] = $error->getMessage();
	} else if ($error instanceof ValidationErrorHelper) {
		$_error = array();
		if ($error->has()) {
			$_error = $error->getList();
		}
	} else {
		$_error = $error;
	}

	if (count($_error) > 0) {
		Loader::element('system_errors', array('format' => 'block', 'error' => $_error));
	}
}

if (isset($message)) {
?>
		<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">×</button>
<?php echo nl2br(Loader::helper('text')->entities($message))?>
		</div>

<?php
} else if (isset($success)) {
?>
		<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button>
<?php echo nl2br(Loader::helper('text')->entities($success))?>
		</div>
<?php
}
?>
	</article>

	<article class="formal-content">
		<section>
			<?php  $a = new Area('Banner'); $a->display($c); ?>
		</section>

		<section class="container">
			<div class="row">
				<section class="col-sm-6 col-md-3 col-lg-3">
				<?php  $a = new Area('Side Bar'); $a->display($c); ?>
				</section>

				<section class="col-sm-6 col-md-9 col-lg-9">
				<?php  $a = new Area('Main'); $a->display($c); ?>
				</section>
			</div>
		</section>

		<div class="v-seperator">
		</div>

	</article>
<?php
	$this->inc('inc/footer.php', array ('doc_lang' => $doc_lang));
	$this->inc('inc/status-bar.php');
?>

</div>

<div id="globalModal" class="full-stack modal fade" tabindex="-1"
		role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" lang="<?php echo $doc_lang ?>">
	<div class="modal-dialog">
		<div class="modal-content">
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script lang="JavaScript">
$(document).ready (function() {
	$('.launch-modal').click (function (event) {
		event.preventDefault ();
		var href = $(this).attr ('href');
		$('#globalModal div.modal-content').load (href, function () {
			$("#globalModal").modal ();
		});
	});
});
</script>

<?php
Loader::element ('footer_required');
?>

</body>
</html>
