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

include('inc/head.php');
?>

<body>

<div class="full-stack">
<?php
	include('inc/header.php');
?>
	<article class="formal-content">
		<section>
			<?php  $a = new Area('Banner'); $a->display($c); ?>
		</section>
		<section class="container">
			<div class="row">
				<section class="col-sm-8 col-md-3 col-lg-3">
				<?php  $a = new Area('Side Bar'); $a->display($c); ?>
				</section>

				<section class="col-sm-8 col-md-6 col-lg-6">
				<?php  $a = new Area('Main'); $a->display($c); ?>
				</section>

				<section class="col-sm-8 col-md-3 col-lg-3">
				<?php  $a = new Area('Another Side Bar'); $a->display($c); ?>
				</section>
			</div>
		</section>
	</article>

<?php
	include('inc/footer.php');
?>

</div>

<?php
include('inc/dynamic-nav.php');
Loader::element ('footer_required');
?>

</body>
</html>
