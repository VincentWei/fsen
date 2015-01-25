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
<!DOCTYPE HTML>
<!--[if IE]><![endif]-->
<!--[if lt IE 7 ]> <html lang="en" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html xmlns:wb="http://open.weibo.com/wb">
<!--<![endif]-->
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta name="apple-touch-fullscreen" content="yes" />
<?php
$this->addHeaderItem('<link rel="stylesheet" type="text/css" href="/concrete/css/jquery-ui.css" />');
$this->addHeaderItem('<link rel="stylesheet" type="text/css" href="/concrete/css/ccm.app.css" />');
$this->addHeaderItem('<link rel="stylesheet" type="text/css" href="/css/bootstrap.css" />');
$this->addHeaderItem('<link rel="stylesheet" type="text/css" href="/css/sh/shCore.css" />');
$this->addHeaderItem('<link rel="stylesheet" type="text/css" href="/css/sh/shThemeDefault.css" />');
$this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . $this->getThemePath() . '/css/global.css" />');
$this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . $this->getThemePath() . '/css/flat-form.css" />');

$this->addHeaderItem('<script type="text/javascript" charset="utf-8" src="/index.php/tools/required/i18n_js"></script>');
$this->addHeaderItem('<script type="text/javascript" charset="utf-8" src="/concrete/js/jquery-ui.js"></script>');
$this->addHeaderItem('<script type="text/javascript" charset="utf-8" src="/concrete/js/jquery.form.js"></script>');
$this->addHeaderItem('<script type="text/javascript" charset="utf-8" src="/concrete/js/jquery.rating.js"></script>');
$this->addHeaderItem('<script type="text/javascript" charset="utf-8" src="/concrete/js/ccm.app.js"></script>');
$this->addHeaderItem('<script type="text/javascript" charset="utf-8" src="/js/bootstrap.min.js"></script>');

$this->addHeaderItem('<script type="text/javascript" charset="utf-8" src="/js/sh/shCore.js"></script>');
$this->addHeaderItem('<script type="text/javascript" charset="utf-8" src="/js/sh/shAutoloader.js"></script>');
$this->addHeaderItem('<script type="text/javascript" charset="utf-8" src="/js/fsen.js"></script>');
$this->addHeaderItem('<script type="text/javascript" charset="utf-8" src="/js/md5.js"></script>');

Loader::element('header_required');
?>
</head>
