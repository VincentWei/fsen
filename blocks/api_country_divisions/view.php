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

require_once ('helpers/misc.php');

$app_key = '86aa4f51050b63f199635d970d38f8dd167af1fa5cd412c32c8bf80935af397a';
$caller_id = get_caller_id ();
$ret_info = file_get_contents ('http://api.fullstackengineer.net/access_token/get/' . "$app_key/$caller_id");
$ret_info = json_decode ($ret_info);
$access_token = $ret_info->message;

$ret_info = file_get_contents ('http://api.fullstackengineer.net/list/countries/items/' . $access_token . '/zh_CN');
$ret_info = json_decode ($ret_info);
?>

<fieldset>
	<legend>
		<?php echo t('Demo for Countries/Divisions') ?>
	</legend>

	<form class="form-inline">
		<div class="form-group">
			<label class="control-label">
				<?php echo t('Country/Region: ') ?>
			</label>
			<div class="btn-group">
				<button class="btn btn-default dropdown-toggle" type="button" id="buttonCountry"
						data-toggle="dropdown" aria-expanded="true">
					Country/Region
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu" style="max-height:260px;overflow-y:scroll;"
						role="menu" aria-labelledby="buttonCountry" >
	<?php
	$img_url = 'http://assets.fullstackengineer.net/images/flags/png-w32/';
	foreach ($ret_info->items as $country) {
	?>
					<li role="presentation">
						<a class="country-item" role="menuitem" tabindex="-1" href="#"
								data-value="<?php echo $country->numeric_code ?>">
							<img src="<?php echo $img_url . strtolower ($country->alpha_2_code) . '.png'; ?>"
									style="height:16px;width:auto" />
							<?php echo $country->name ?>
						</a>
					</li>
	<?php
	}
	?>
				</ul>
			</div>

			<label class="control-label">
				<?php echo t('Or use select control: ') ?>
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
		</div>
	</form>

	<div class="form-group">
	</div>

	<div class="form-group">
		<label class="control-label">
			<?php echo t('State/Province') ?>
		</label>
		<select id="selectProvinces" class="form-control">
			<option value="0">
				<?php echo t('N/A') ?>
			</option>
		</select>
	</div>

	<div class="form-group">
		<label class="control-label">
			<?php echo t('City/District') ?>
		</label>
		<select id="selectDistricts" class="form-control">
			<option value="0">
				<?php echo t('N/A') ?>
			</option>
		</select>
	</div>

	<div class="form-group">
		<label class="control-label">
			<?php echo t('County') ?>
		</label>
		<select id="selectCounties" class="form-control">
			<option value="0">
				<?php echo t('N/A') ?>
			</option>
		</select>
	</div>
</fieldset>

<script type="text/javascript">
$('.country-item').click (function (e) {
	e.preventDefault ();

	$('#selectProvinces option').each (function () {
	       	$(this).remove ();
	});
	$('<option value="0"><?php echo t('N/A') ?></option>').appendTo ('#selectProvinces');

	$('#selectDistricts option').each (function () {
	       	$(this).remove ();
	});
	$('<option value="0"><?php echo t('N/A') ?></option>').appendTo ('#selectDistricts');

	$('#selectCounties option').each (function () {
	       	$(this).remove ();
	});
	$('<option value="0"><?php echo t('N/A') ?></option>').appendTo ('#selectCounties');

	var country_id = $(this).attr ('data-value');
	if (country_id != '0') {
		var html = $(this).html ();
		html = html + '<span class="caret"></span>';
		$('#buttonCountry').html (html);
		$('#selectCountries').val (country_id);

		$('#selectProvinces').attr ("disabled", "true");
		var fsenAPI = 'http://api.fullstackengineer.net/list/divisions/items/<?php echo $access_token ?>/' + country_id + '/zh_CN?callback=?';
		$.getJSON (fsenAPI, '').done (function (data) {
				$.each (data.items, function (i, item) {
					$('<option>' + item.name + '</option>').attr ("value", item.division_id).appendTo ("#selectProvinces");
				});

				$('#selectProvinces').removeAttr ("disabled");
			});
	}
});

$('#selectCountries').change (function () {
	$('#selectProvinces option').each (function () {
	       	$(this).remove ();
	});
	$('<option value="0"><?php echo t('N/A') ?></option>').appendTo ('#selectProvinces');

	$('#selectDistricts option').each (function () {
	       	$(this).remove ();
	});
	$('<option value="0"><?php echo t('N/A') ?></option>').appendTo ('#selectDistricts');

	$('#selectCounties option').each (function () {
	       	$(this).remove ();
	});
	$('<option value="0"><?php echo t('N/A') ?></option>').appendTo ('#selectCounties');

	var country_id = $('#selectCountries').children ('option:selected').val ();
	if (country_id != '0') {
		$('#selectProvinces').attr ("disabled", "true");
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
	$('#selectDistricts option').each (function () {
	       	$(this).remove ();
	});
	$('<option value="0"><?php echo t('N/A') ?></option>').appendTo ('#selectDistricts');

	$('#selectCounties option').each (function () {
	       	$(this).remove ();
	});
	$('<option value="0"><?php echo t('N/A') ?></option>').appendTo ('#selectCounties');

	var country_id = $('#selectCountries').children ('option:selected').val ();
	var province_id = $('#selectProvinces').children ('option:selected').val ();
	if (country_id != '0' && province_id != "0") {
		$('#selectDistricts').attr ("disabled", "true");
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
	$('#selectCounties option').each (function () {
	       	$(this).remove ();
	});
	$('<option value="0"><?php echo t('N/A') ?></option>').appendTo ('#selectCounties');

	var country_id = $('#selectCountries').children ('option:selected').val ();
	var province_id = $('#selectProvinces').children ('option:selected').val ();
	var district_id = $('#selectDistricts').children ('option:selected').val ();
	if (country_id != '0' && province_id != "0" && district_id != '0') {
		$('#selectCounties').attr ("disabled", "true");
		var fsenAPI = 'http://api.fullstackengineer.net/list/divisions/items/<?php echo $access_token ?>/' + district_id + '/zh_CN?callback=?';
		$.getJSON (fsenAPI, '').done (function (data) {
				$.each (data.items, function (i, item) {
					$('<option>' + item.name + '</option>').attr ("value", item.division_id).appendTo ("#selectCounties");
				});

				$('#selectCounties').removeAttr ("disabled");
			});
	}
});

</script>

