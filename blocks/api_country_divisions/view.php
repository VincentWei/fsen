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
$access_token = $ret_info->message;

$ret_info = file_get_contents ('http://api.fullstackengineer.net/list/countries/items/' . $access_token . '/zh_CN');
$ret_info = json_decode ($ret_info);
?>

<fieldset>
	<legend>
		<?php echo t('Demo for Countries/Divisions') ?>
	</legend>

	<label class="control-label">
		<?php echo t('Country/Region') ?>
	</label>
	<select id="selectCountries" class="form-control">
		<option value="0">
			<?php echo t('N/A') ?>
		</option>
<?php
foreach ($ret_info->items as $country) {
?>
		<option value="<?php echo $country->numeric_code ?>"><?php echo $country->name ?></option>
<?php
}
?>
	</select>

	<label class="control-label">
		<?php echo t('State/Province') ?>
	</label>
	<select id="selectProvinces" class="form-control">
		<option value="0">
			<?php echo t('N/A') ?>
		</option>
	</select>

	<label class="control-label">
		<?php echo t('City/District') ?>
	</label>
	<select id="selectDistricts" class="form-control">
		<option value="0">
			<?php echo t('N/A') ?>
		</option>
	</select>

	<label class="control-label">
		<?php echo t('County') ?>
	</label>
	<select id="selectCounty" class="form-control">
		<option value="0">
			<?php echo t('N/A') ?>
		</option>
	</select>
</fieldset>

<script type="text/javascript">
$('#selectCountries').change (function () {
	var $location_province = $('#selectProvinces');
	$('#selectProvinces option').each (function () {
	       	$(this).remove ();
	});
	$('<option value="0"><?php echo t('N/A') ?></option>').appendTo ($location_province);

	var $location_district = $('#selectDistricts');
	$('#selectDistricts option').each (function () {
	       	$(this).remove ();
	});
	$('<option value="0"><?php echo t('N/A') ?></option>').appendTo ($location_district);

	var country_id = $('#selectCountries').children ('option:selected').val ();
	if (country_id != '0') {
		$location_province.attr ("disabled", "true");
		var fsenAPI = 'http://api.fullstackengineer.net/list/divisions/items/<?php echo $access_token ?>/' + country_id + '/zh_CN?callback=?';
		$.getJSON (fsenAPI, '').done (function (data) {
				$.each (data.items, function (i, item) {
					$('<option>' + item.name + '</option>').attr ("value", item.division_id).appendTo ("#selectProvinces");
				});

				$('#selectProvinces').removeAttr ("disabled");
			});
	}
});

$('#selectProvinces').change (function () {
	var $location_district = $('#selectDistricts');
	$('#selectDistricts option').each (function () {
	       	$(this).remove ();
	});
	$('<option value="0"><?php echo t('N/A') ?></option>').appendTo ($location_district);

	var country_id = $('#selectCountries').children ('option:selected').val ();
	var province_id = $('#selectProvinces').children ('option:selected').val ();
	if (country_id != '0' && province_id != "0") {
		$location_district.attr ("disabled", "true");
		var fsenAPI = 'http://api.fullstackengineer.net/list/divisions/items/<?php echo $access_token ?>/' + province_id + '/zh_CN?callback=?';
		$.getJSON (fsenAPI, '').done (function (data) {
				$.each (data.items, function (i, item) {
					$('<option>' + item.name + '</option>').attr ("value", item.division_id).appendTo ("#selectDistricts");
				});

				$('#selectDistricts').removeAttr ("disabled");
			});
	}
});

$('#selectDistricts').change (function () {
	var $location_county = $('#selectCounty');
	$('#selectCounty option').each (function () {
	       	$(this).remove ();
	});
	$('<option value="0"><?php echo t('N/A') ?></option>').appendTo ($location_county);

	var country_id = $('#selectCountries').children ('option:selected').val ();
	var province_id = $('#selectProvinces').children ('option:selected').val ();
	var district_id = $('#selectDistricts').children ('option:selected').val ();
	if (country_id != '0' && province_id != "0" && district_id != '0') {
		$location_county.attr ("disabled", "true");
		var fsenAPI = 'http://api.fullstackengineer.net/list/divisions/items/<?php echo $access_token ?>/' + district_id + '/zh_CN?callback=?';
		$.getJSON (fsenAPI, '').done (function (data) {
				$.each (data.items, function (i, item) {
					$('<option>' + item.name + '</option>').attr ("value", item.division_id).appendTo ("#selectCounty");
				});

				$('#selectCounty').removeAttr ("disabled");
			});
	}
});

</script>

