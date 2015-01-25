<?php
defined('C5_EXECUTE') or die("Access Denied.");

Events::extend('on_start', 'FSENLocalization', 'setupInterfaceLocalization4Request', 'models/fsen_localization.php');
Events::extend('on_before_render', 'FSENLocalization', 'setupInterfaceLocalization4Page', 'models/fsen_localization.php');

