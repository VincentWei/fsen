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
?>

<form id="formDELETEAPPKEY" style="display:none;" method="post" action="/fse_settings/applications/delete_app_key">
	<input name="appKey" type="hidden" />
</form>

<form id="formNEWAPPKEY" method="post" action="/fse_settings/applications/new_app_key">
	<fieldset class="flat">
		<header>
			<h2>
				<?php echo t('App Keys') ?>
			</h2>
		</header>

<?php
$db = Loader::db ();
$app_keys = $db->getAll ("SELECT app_key, app_name, app_desc, app_url, app_icon_url FROM fse_app_keys WHERE fse_id=?",
			array ($_SESSION ['FSEInfo']['fse_id']));

if (count ($app_keys) > 0) {
?>
		<section class="block-list-with-icon">
			<ul>
<?php
	foreach ($app_keys as $app) {
?>
<li>
	<a class="item-icon" href="<?php echo $app['app_url'] ?>"><img
		src="<?php echo $app['app_icon_url'] ?>" alt="<?php echo $app['app_name'] ?>"></a>
	<a class="item-manage awesome red" data-value="<?php echo $app['app_key'] ?>" href="#"><?php echo t('Delete') ?></a>
	<div class="item-desc">
		<h2><?php echo $app['app_name'] ?></h2>
		<p><?php echo $app['app_desc'] ?><br>
		<?php echo t('App Key:') ?>
		<b><?php echo $app['app_key'] ?></b></p>
	</div>
</li>
<?php
	}
?>

			</ul>
		</section>
<?php
}
else {
?>
		<section class="note">
			<p>
				<?php echo t('You have not created any app key.') ?>
			</p>
		</section>
<?php
}
?>
		<hr />

		<section class="fieldBase">
			<?php echo t('Name') ?>
			<input name="appName" type="text"
				pattern=".{3,32}" required="true"
				title="3 to 32 characters"
				placeholder="My App" />
		</section>
		<section class="description">
<?php echo t('The unique name of your app (required).') ?>
		</section>

		<section class="fieldBase">
			<?php echo t('Description') ?>
			<input name="appDesc" type="text"
				pattern=".{5,255}" required="true"
				title="5 to 255 characters"
				placeholder="This app is a..." />
		</section>
		<section class="description">
<?php echo t('The description of your app (required).') ?>
		</section>

		<section class="fieldBase">
		<?php echo t('App URL') ?>
			<input name="appURL" type="url" maxlength="255"
				placeholder="http://..." />
		</section>
		<section class="description">
<?php echo t('The URL of your app (optional).') ?>
		</section>

		<section class="fieldBase">
		<?php echo t('Icon URL') ?>
			<input name="appIconURL" type="url" maxlength="255"
				placeholder="http://..." />
		</section>
		<section class="description">
<?php echo t('The URL of the icon of your app (optional).') ?>
		</section>

		<section class="fieldBase transparent">
			<input type="submit" value="<?php echo t('Create') ?>" />
		</section>
	</fieldset>
</form>

<script type="text/javascript">
$('.item-manage').click (function (event) {
	event.preventDefault ();

	var app_key = $(this).attr ("data-value");
	$("#formDELETEAPPKEY input[name='appKey']").val (app_key);
	$('#formDELETEAPPKEY').submit ();
});
</script>

