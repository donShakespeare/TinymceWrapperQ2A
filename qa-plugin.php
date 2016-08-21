<?php
/*
File: qa-plugin/tinymcewrapper/qa-plugin.php
Description: The most powerful RTE with full markdown support

donshakespeare @ MODX forums
https://github.com/donShakespeare

To God almighty be all glory
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

qa_register_plugin_module('editor', 'qa-tinymcewrapper.php', 'qa_tinymcewrapper_editor', 'TinymceWrapper (Pure CDN + full Markdown)');
qa_register_plugin_module('viewer', 'qa-tinymcewrapper-viewer.php', 'qa_tinymcewrapper_viewer', 'TinymceWrapper (Pure CDN + full Markdown)');
qa_register_plugin_layer('qa-tinymcewrapper-layer.php', 'TinymceWrapper (Pure CDN + full Markdown)');