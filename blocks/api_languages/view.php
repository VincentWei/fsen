<?php
/**
 * This file is a part of FullStackEngineer.Net Project.
 *
 * FullStackEngineer.Net is a web site for hosting webpages
 * (especially the documents, forums) of open source projects.
 *
 * FullStackEngineer.Net project itself is an open source project.
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

$app_key = '86aa4f51050b63f199635d970d38f8dd167af1fa5cd412c32c8bf80935af397a';
$ret_info = file_get_contents ('http://api.fullstackengineer.net/access_token/get/' . $app_key);
$ret_info = json_decode ($ret_info);

$ret_info = file_get_contents ('http://api.fullstackengineer.net/list/languages/items/' . $ret_info->message . '/zh_CN');
$ret_info = json_decode ($ret_info);
?>

<fieldset>
	<legend>
		<?php echo t('Demo for Languanges') ?>
	</legend>

	<label class="control-label">
		<?php echo t('Language') ?>
	</label>
	<select class="form-control">
<?php
foreach ($ret_info->items as $lang) {
?>
	<option value="<?php echo $lang->iso_639_1_code ?>"><?php echo $lang->name ?></option>
<?php
}
?>
	</select>
</fieldset>

