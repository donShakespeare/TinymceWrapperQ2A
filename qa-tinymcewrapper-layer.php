<?php
/*
File: qa-plugin/tinymcewrapper/qa-tinymcewrapper-layer.php
Description: The most powerful RTE with full markdown support

https://github.com/donShakespeare/
http://www.leofec.com/modx-revolution/
(c) 2016 by donShakespeare: MODX & Content Editor Specialist
Deo Gratias!
*/

class qa_html_theme_layer extends qa_html_theme_base
{
		public function body_hidden()
		{
				if(qa_opt("plugin_tinymcewrapper_enable_misc") && !qa_opt('plugin_tinymcewrapper_disable') && (qa_opt('plugin_tinymcewrapper_disable_mobile') + qa_is_mobile_probably() !== 2)){
						function getMyPathy($location)
						{
						  $getMyPathy = str_replace($_SERVER['DOCUMENT_ROOT'], '', $location);
						  return $getMyPathy;
						}
						parent::body_hidden();
						$scriptsrc    = qa_opt('plugin_tinymcewrapper_cdn_url');
						$tinymcewrapperMain = QA_HTML_THEME_LAYER_URLTOROOT. "tinymceplugins/donshakespeare_handmade.min.js";
						if(!qa_opt("plugin_tinymcewrapper_skin_select") || qa_opt("plugin_tinymcewrapper_skin_select") == "default"){
						  $selectedSkin = "";
						}
						else{
						  $selectedSkin = QA_HTML_THEME_LAYER_URLTOROOT. "tinymceskins/" . qa_opt("plugin_tinymcewrapper_skin_select");
						}
		    $miscConfig = str_replace(array(
		          "[[+ID]]",
		          "[[+BAR]]",
		          "[[+SKIN]]"
		        ), array(
		          qa_opt("plugin_tinymcewrapper_misc_selector"),
		          "#tinymceWrapperBubbleBar",
		          $selectedSkin
		        ), qa_opt("plugin_tinymcewrapper_misc"));
						$allowedPages = qa_opt("plugin_tinymcewrapper_misc_pages");
						$allowedPages = array_map('trim', explode(',', $allowedPages));
						$thisPage = $this->request;
						if(in_array($thisPage, $allowedPages) || in_array(dirname($thisPage), $allowedPages) ){
						// if($thisPage){ //for debug only
								$this->output(
									'
									<div id=tinymceWrapperBubbleBar></div>
									<script>
											// alert("'.$thisPage.'") //for debug only
											tinymcewrapperPluginSrc = "'.QA_HTML_THEME_LAYER_URLTOROOT.'";
											tinymcewrapperSwitch = "";
											tinymcewrapperDisableSwitch = "";
											tinymcewrapperViewer = "";
											tinymcewrapperViewerDisabled = "";
											tinymcewrapperUniMDkey = "";
											tinymcewrapperQtitleMinLength = "";
									</script>
									<script src="'.$scriptsrc.'"></script>
									<script src="'.$tinymcewrapperMain.'"></script>
									<script>
									'.$miscConfig.'
									</script>
									'
								);
					}
				}
		}
}
