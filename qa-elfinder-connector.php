<?php
/*
https://github.com/donShakespeare/
http://www.leofec.com/modx-revolution/
(c) 2016 by donShakespeare: MODX & Content Editor Specialist
Deo Gratias!
*/

// error_reporting(E_ALL); // Set E_ALL for debuging
include_once dirname(dirname(dirname(__FILE__))).'/qa-include/qa-base.php';
include_once QA_INCLUDE_DIR.'qa-app-users.php';
// define("Q2A_BASE_PATH", '../../'); //- works partially
// define("Q2A_BASE_PATH", __FILE__.'/../../'); //- works flawlessly
// define("Q2A_BASE_URL", dirname($_SERVER['PHP_SELF']).'/../../');
$pre_path = $_SERVER['DOCUMENT_ROOT'] . parse_url(qa_opt('site_url'), PHP_URL_PATH); //experimental, allows connector file to be anywhere
define("Q2A_BASE_PATH", $pre_path);
define("Q2A_BASE_URL", qa_opt('site_url'));
if (qa_get_logged_in_userid() === null){
    die('<div style="position: fixed; margin: auto;width: 400px;height:200px;text-align:center;top:0;bottom:0;left:0;right:0;"><h1>IT SEEMS YOU DO NOT HAVE PERMISSION TO USE THIS COOL MANAGER</h1></div>');
}
include_once dirname(__FILE__).'/elfinder/php/elFinderConnector.class.php';
include_once dirname(__FILE__).'/elfinder/php/elFinder.class.php';
include_once dirname(__FILE__).'/elfinder/php/elFinderVolumeDriver.class.php';
include_once dirname(__FILE__).'/elfinder/php/elFinderVolumeLocalFileSystem.class.php';
include_once dirname(__FILE__).'/elfinder/php/elFinderVolumeFTP.class.php';

function getMyParams($name){
  if (isset($_GET[$name]) && !empty($_GET[$name])) {
    $getParam = filter_var($_GET[$name], FILTER_SANITIZE_STRING);
    $getParam = rawurldecode($getParam);
    return $getParam;
  }
 }

function access($attr, $path, $data, $volume) {
    return strpos(basename($path), '.') === 0   
        ? !($attr == 'read' || $attr == 'write')
        :  null;
}

function volArray($volJSONready, $user){
    foreach ($volJSONready[$user] as $child){
        if (is_array($child)) {
            autoCreatePersonalFolder($volJSONready[$user][0]['path'], $volJSONready[$user][0]['url']);
            return $volJSONready[$user];
        }
        autoCreatePersonalFolder($volJSONready[$user]['path'], $volJSONready[$user]['url']);
        // if (!qa_opt("plugin_tinymcewrapper_elfinder_personal")) {
        //     if (strpos($volJSONready[$user]['path'], qa_get_logged_in_handle()) !== false || strpos($volJSONready[$user]['url'], qa_get_logged_in_handle()) !== false) {
        //         unset($volJSONready[$user]);
        //         return;
        //         // return array("");
        //     }
        // }
        return array($volJSONready[$user]);
    }
}

function autoCreatePersonalFolder($pathToBuild, $url){
    if (qa_opt("plugin_tinymcewrapper_elfinder_personal") && $pathToBuild && strpos($pathToBuild, qa_get_logged_in_handle()) !== false && strpos($url, qa_get_logged_in_handle()) !== false) {
        if (!file_exists($pathToBuild)) {
            mkdir($pathToBuild, 0755, true);
        }
    }
}

$q2aUserLevels = array( 
    QA_USER_LEVEL_SUPER => 'super_users',
    QA_USER_LEVEL_ADMIN => 'admin_users',
    QA_USER_LEVEL_MODERATOR => 'moderator_users',
    QA_USER_LEVEL_EDITOR => 'editor_users',
    QA_USER_LEVEL_EXPERT => 'expert_users',
    QA_USER_LEVEL_APPROVED => 'approved_users',
    QA_USER_LEVEL_BASIC => 'basic_users'
);

$volJSON = str_replace(array(
    "[[+Q2A_BASE_PATH]]",
    "[[+Q2A_BASE_URL]]",
    "[[+START]]",
    "[[+Q2A_USERNAME]]"
    ), array(
    Q2A_BASE_PATH,
    Q2A_BASE_URL,
    getMyParams('folder'),
    qa_opt("plugin_tinymcewrapper_elfinder_personal") ? qa_get_logged_in_handle() : "@@@@ & ^ ) .. invalid String"
    ), qa_opt("plugin_tinymcewrapper_elfinder"));

$opts = array('roots' => volArray(json_decode($volJSON, true), $q2aUserLevels[qa_get_logged_in_level()] ?: "N/A"));
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();

