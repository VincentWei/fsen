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
#$ret_info = file_get_contents ('http://api.fullstackengineer.net/access_token/get/' . $app_key);
#$ret_info = json_decode ($ret_info);

$ret_info = file_get_contents ('http://api.fullstackengineer.net/list/open_source_licenses/items');
$ret_info = json_decode ($ret_info);
?>

<fieldset>
	<legend>
		Demo for Open Cloud API (Simple List).
	</legend>

	<label class="control-label">
		Computer Language
	</label>
	<select id="selectLanguages" class="form-control">
	</select>

	<label class="control-label">
		Open Source License
	</label>
	<select id="selectLicenses" class="form-control">
<?php
foreach ($ret_info->items as $osl) {
?>
	<option value="<?php echo $osl->short_name ?>"><?php echo $osl->name ?></option>
<?php
}
?>
	</select>
</fieldset>

<script type="text/javascript">
(function () {
	var fsenAPI = "http://api.fullstackengineer.net/list/computer_languages/items?callback=?";
	$.getJSON (fsenAPI, '').done (function (data) {
			$.each (data.items, function (i, item) {
				$('<option>' + item.name + '</option>').attr ("value", item.short_name).appendTo ("#selectLanguages");
			});
		});
})();
</script>

