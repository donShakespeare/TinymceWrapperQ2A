<?php
/*
File: qa-plugin/tinymcewrapper/qa-tinymcewrapper.php
Description: The most powerful RTE with full markdown support

donshakespeare @ MODX forums
https://github.com/donShakespeare

To God almighty be all glory
*/
class qa_tinymcewrapper_editor
{
  private $urltoroot;

  public function load_module($directory, $urltoroot)
  {
    $this->urltoroot = $urltoroot;
  }
  public function calc_quality($content, $format)
  {
    // if(!qa_opt('plugin_tinymcewrapper_disable') && (qa_opt('plugin_tinymcewrapper_disable_mobile') + qa_is_mobile_probably() !== 2)){
    if ($format == 'html' || $format == 'markdown')
      return 1.0;
    elseif ($format == '')
      return 0.8;
    else
      return 0;
    // }
  }
  public function get_field(&$qa_content, $content, $format, $fieldname, $rows)
  {
    $tinymcewrapperUniMDkey = 1;
    if(strpos(qa_opt("plugin_tinymcewrapper_uni_md"), 'twExoticMarkdownEditor') !== false) {
      $tinymcewrapperUniMDkey = 2;
    }
    if(!qa_opt('plugin_tinymcewrapper_disable') && (qa_opt('plugin_tinymcewrapper_disable_mobile') + qa_is_mobile_probably() !== 2)){
      $scriptsrc    = qa_opt('plugin_tinymcewrapper_cdn_url');
      $tinymcewrapperMain    = $this->urltoroot.'tinymceplugins/donshakespeare_handmade.min.js';
      $scriptPluginSrc   = array("
        tinymcewrapperPluginSrcCssFile = '".qa_opt('plugin_tinymcewrapper_css_url')."';
        tinymcewrapperPluginSrc = '".$this->urltoroot."';
        tinymcewrapperSwitch = '".qa_opt("plugin_tinymcewrapper_switch")."';
        tinymcewrapperDisableSwitch = ".qa_opt('plugin_tinymcewrapper_disable_switch').";
        tinymcewrapperViewer = '".qa_opt('plugin_tinymcewrapper_md_select')."';
        tinymcewrapperViewerDisabled = ".qa_opt('plugin_tinymcewrapper_disable_md').";
        tinymcewrapperUniMDkey = ".$tinymcewrapperUniMDkey.";
        tinymcewrapperQtitleMinLength = ".qa_opt('min_len_q_title').";
        tinymcewrapperQtitleMaxLength = ".qa_opt('max_len_q_title').";
        tinymcewrapperQminTags = ".qa_opt('min_num_q_tags').";
        tinymcewrapperQmaxTags = ".qa_opt('max_num_q_tags').";
        ");
      $alreadyadded = false;
      if (isset($qa_content['script_src'])) {
        foreach ($qa_content['script_src'] as $testscriptsrc) {
          if ($testscriptsrc == $scriptsrc)
            $alreadyadded = true;
        }
      }
      if(!$alreadyadded) {
        $qa_content['script_src'][] = $scriptsrc;
        $qa_content['script_src'][] = $tinymcewrapperMain;
        $qa_content['script_lines'][] = $scriptPluginSrc;
      }

      if($format){
        $thisFormat = 'Format: <i>'.$format.'</i>';
      }
      else{
        $thisFormat = 'Format: <i>new</i>';
      }
      
      if($fieldname === 'content'){
        $twFieldnamePattern = 'q';
      } else{
        $twFieldnamePattern = substr($fieldname, 0, 1);
      }

      $donshakespeareDiv = '
      <div class="tinymcewrapperWrap content_type_'.$twFieldnamePattern.'">
        <div class="tinymcewrapper_prev" id="tinymcewrapper_prev_' . $fieldname . '"></div>
        <div class="tinymcewrapper_format" data-kind="'.$twFieldnamePattern.'" data-q_min_content="'.qa_opt('min_len_q_content').'" data-a_min_content="'.qa_opt('min_len_a_content').'" data-c_min_content="'.qa_opt('min_len_c_content').'" data-origin="tinymcewrapper_' . $fieldname . '_txt" data-fieldname="' . $fieldname . '"><span class="tinymcewrapper_this_format" title="The format of this present post">'.$thisFormat.'</span></div>
        <div class="tinymcewrapper_bar" id="tinymcewrapper_' . $fieldname . '_static"></div>
        <div class="tex2jax_ignore tinymcewrapper_donshakespeare_editor tinymcewrapper_starter" id="tinymcewrapper_' . $fieldname . '" data-kind="'.$twFieldnamePattern.'" data-q_min_content="'.qa_opt('min_len_q_content').'" data-a_min_content="'.qa_opt('min_len_a_content').'" data-c_min_content="'.qa_opt('min_len_c_content').'" data-origin="tinymcewrapper_' . $fieldname . '_txt">'.qa_sanitize_html($content, false, true).'</div>
        <textarea name="'.$fieldname.'" class="tinymcewrapper_txt" style=width:100%;min-height:250px id="tinymcewrapper_' . $fieldname . '_txt" >'.qa_sanitize_html($content, false, true).'</textarea>
      </div>';
      return array(
        'type' => 'custom',
        'html'=> $donshakespeareDiv
      );
    }
    else{
      return array(
        'tags' => 'name="'.$fieldname.'" data-kind="tinymcewrapper_'.$fieldname.'" class = "qa-form-tall-text tinymcewrapper_noEditor"',
        'value' => qa_sanitize_html($content, false, true),
        'rows' => $rows
      );
    }
  }
  public function load_script($fieldname)
  {
    if(!function_exists('getMyPath')) {
      function getMyPath($location)
      {
        $getMyPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $location);
        return $getMyPath;
      }
    }
    if(!function_exists('readyEditor')) {
      function readyEditor($item, $fieldname){
        if(!qa_opt("plugin_tinymcewrapper_skin_select") || qa_opt("plugin_tinymcewrapper_skin_select") == "default"){
          $selectedSkin = "";
        }
        else{
          $selectedSkin = getMyPath(__DIR__). "/tinymceskins/" . qa_opt("plugin_tinymcewrapper_skin_select");
        }

        $modeSuffix = "";
        $mode = "true";
        if(qa_opt('plugin_tinymcewrapper_inline_switch')){
          $modeSuffix = "_txt";
          $mode = "false";
        }
        
        return str_replace(array(
          "[[+ID]]",
          "[[+MODE]]",
          "[[+FLOAT]]",
          "[[+STATIC]]",
          "[[+SKIN]]"
        ), array(
          "#tinymcewrapper_" . $fieldname . $modeSuffix,
          $mode,
          "#tinymceWrapperBubbleBar",
          "#tinymcewrapper_" . $fieldname . "_static",
          $selectedSkin
        ), qa_opt($item));
      }
    }
    if(!qa_opt('plugin_tinymcewrapper_disable') && (qa_opt('plugin_tinymcewrapper_disable_mobile') + qa_is_mobile_probably() !== 2)){
      if(qa_opt('plugin_tinymcewrapper_switch') == "universal"){
        if(isset($_GET["editor"]) && (int)$_GET["editor"] === 1 && !qa_opt('plugin_tinymcewrapper_disable_switch')){
          qa_opt("plugin_tinymcewrapper_switch_uni", 1);
          return readyEditor('plugin_tinymcewrapper_uni_rte', $fieldname);
        }
        elseif(isset($_GET["editor"]) && (int)$_GET["editor"] === 2 && !qa_opt('plugin_tinymcewrapper_disable_switch')){
          qa_opt("plugin_tinymcewrapper_switch_uni", 2);
          return readyEditor('plugin_tinymcewrapper_uni_md', $fieldname);
        }
        elseif((int)qa_opt("plugin_tinymcewrapper_switch_uni") === 1 && !qa_opt('plugin_tinymcewrapper_disable_switch')){
          return readyEditor('plugin_tinymcewrapper_uni_rte', $fieldname);
        }
        elseif((int)qa_opt("plugin_tinymcewrapper_switch_uni") === 2 && !qa_opt('plugin_tinymcewrapper_disable_switch')){
          return readyEditor('plugin_tinymcewrapper_uni_md', $fieldname);
        }
        else{
          return readyEditor('plugin_tinymcewrapper_uni_rte', $fieldname);
        }
      }
      elseif(qa_opt('plugin_tinymcewrapper_switch') == "individual"){
        if($fieldname === 'content'){
          $fieldnamePattern = 'q';
        } else{
          $fieldnamePattern = substr($fieldname, 0, 1);
        }
        return readyEditor('plugin_tinymcewrapper_'.$fieldnamePattern.'_config', $fieldname);
      }
    }
  }
  function option_default($option)
  {

    if(!function_exists('getMyPath')) {
      function getMyPath($location)
      {
        $getMyPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $location);
        return $getMyPath;
      }
    }

    $tinymcewrapper_config_rte = $tinymcewrapper_config_md = $check_tinymcewrapper_config_misc = $check_tinymcewrapper_elfinder = '';
    $check_tinymcewrapper_config_rte  = __DIR__ . '/backup_json_defaults/tinymceInit_rte.json';
    $check_tinymcewrapper_config_md    = __DIR__ . '/backup_json_defaults/tinymceInit_md.json';
    $check_tinymcewrapper_config_misc    = __DIR__ . '/backup_json_defaults/tinymceInit_misc_rte.json';
    $check_tinymcewrapper_elfinder    = __DIR__ . '/backup_json_defaults/elfinder_volumes.json';
    if(file_exists($check_tinymcewrapper_config_rte)) {
      $tinymcewrapper_config_rte = file_get_contents($check_tinymcewrapper_config_rte);
    }
    if(file_exists($check_tinymcewrapper_config_md)) {
      $tinymcewrapper_config_md = file_get_contents($check_tinymcewrapper_config_md);
    }
    if(file_exists($check_tinymcewrapper_config_misc)) {
      $tinymcewrapper_config_misc = file_get_contents($check_tinymcewrapper_config_misc);
    }
    if(file_exists($check_tinymcewrapper_elfinder)) {
      $check_tinymcewrapper_elfinder = file_get_contents($check_tinymcewrapper_elfinder);
    }

    $tinymceCDNbase = "//cdn.tinymce.com/4/tinymce.min.js";
    $tinymceSkin = "modxPericles";
    $tinymceMiscSel = "#option_notice_visitor, textarea[id^=option_custom]:not(#option_custom_in_head,#option_custom_header,#option_custom_sidepanel,#option_custom_sidebar,#option_custom_home_content,#option_custom_footer), textarea[name=content], textarea#message";
    $tinymceMiscPages = "admin/users, admin/layout, admin/posting, admin/pages";

    if($option == 'plugin_tinymcewrapper_cdn_url')
      return $tinymceCDNbase;
    if($option == 'plugin_tinymcewrapper_css_url')
      return getMyPath(__DIR__). "/css/style.css";
    if($option == 'plugin_tinymcewrapper_skin_select')
      return $tinymceSkin;
    if($option == 'plugin_tinymcewrapper_disable')
      return 0;
    if($option == 'plugin_tinymcewrapper_strip_html')
      return 0;
    if($option == 'plugin_tinymcewrapper_strip_html_v_e')
      return 1;
    if($option == 'plugin_tinymcewrapper_disable_mobile')
      return 0;
    if($option == 'plugin_tinymcewrapper_disable_md')
      return 0;
    if($option == 'plugin_tinymcewrapper_enable_misc')
      return 0;
    if($option == 'plugin_tinymcewrapper_disable_switch')
      return 0;
    if($option == 'plugin_tinymcewrapper_inline_switch')
      return 0;
    if($option == 'plugin_tinymcewrapper_switch_uni')
      return 1;
    if($option == 'plugin_tinymcewrapper_misc_selector')
      return $tinymceMiscSel;
    if($option == 'plugin_tinymcewrapper_misc_pages')
      return $tinymceMiscPages;
    if($option == 'plugin_tinymcewrapper_elfinder_personal')
      return 0;
    if($option == 'plugin_tinymcewrapper_elfinder')
      return $check_tinymcewrapper_elfinder;
    if($option == 'plugin_tinymcewrapper_misc')
      return $tinymcewrapper_config_misc;
    if($option == 'plugin_tinymcewrapper_q_config')
      return "$tinymcewrapper_config_rte";
    if($option == 'plugin_tinymcewrapper_a_config')
      return $tinymcewrapper_config_md;
    if($option == 'plugin_tinymcewrapper_c_config')
      return $tinymcewrapper_config_rte;
    if($option == 'plugin_tinymcewrapper_uni_rte')
      return $tinymcewrapper_config_rte;
    if($option == 'plugin_tinymcewrapper_switch')
      return "individual";
    if($option == 'plugin_tinymcewrapper_uni_md')
      return $tinymcewrapper_config_md;
    return null;
  }
  function admin_form(&$qa_content)
  {
    if(!function_exists('getMyPath')) {
      function getMyPath($location)
      {
        $getMyPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $location);
        return $getMyPath;
      }
    }
    $tinymcewrapper_config_rte = $tinymcewrapper_config_md = $check_tinymcewrapper_config_misc = $check_tinymcewrapper_elfinder = '';
    $check_tinymcewrapper_config_rte  = __DIR__ . '/backup_json_defaults/tinymceInit_rte.json';
    $check_tinymcewrapper_config_md    = __DIR__ . '/backup_json_defaults/tinymceInit_md.json';
    $check_tinymcewrapper_config_misc    = __DIR__ . '/backup_json_defaults/tinymceInit_misc_rte.json';
    $check_tinymcewrapper_elfinder    = __DIR__ . '/backup_json_defaults/elfinder_volumes.json';
    if(file_exists($check_tinymcewrapper_config_rte)) {
      $tinymcewrapper_config_rte = file_get_contents($check_tinymcewrapper_config_rte);
    }
    if(file_exists($check_tinymcewrapper_config_md)) {
      $tinymcewrapper_config_md = file_get_contents($check_tinymcewrapper_config_md);
    }
    if(file_exists($check_tinymcewrapper_config_misc)) {
      $tinymcewrapper_config_misc = file_get_contents($check_tinymcewrapper_config_misc);
    }
    if(file_exists($check_tinymcewrapper_elfinder)) {
      $check_tinymcewrapper_elfinder = file_get_contents($check_tinymcewrapper_elfinder);
    }
    $saved = false;
    if(qa_clicked('plugin_tinymcewrapper_save_button')) {
      qa_opt('plugin_tinymcewrapper_cdn_url', qa_post_text('plugin_tinymcewrapper_cdn_url_field'));
      qa_opt('plugin_tinymcewrapper_css_url', qa_post_text('plugin_tinymcewrapper_css_url_field'));
      qa_opt('plugin_tinymcewrapper_skin_select', qa_post_text('plugin_tinymcewrapper_skin_select_field'));
      qa_opt('plugin_tinymcewrapper_disable', (int)qa_post_text('plugin_tinymcewrapper_disable_field'));
      qa_opt('plugin_tinymcewrapper_disable_mobile', (int)qa_post_text('plugin_tinymcewrapper_disable_mobile_field'));
      qa_opt('plugin_tinymcewrapper_strip_html', (int)qa_post_text('plugin_tinymcewrapper_strip_html_field'));
      qa_opt('plugin_tinymcewrapper_strip_html_v_e', (int)qa_post_text('plugin_tinymcewrapper_strip_html_v_e_field'));
      qa_opt('plugin_tinymcewrapper_disable_md', (int)qa_post_text('plugin_tinymcewrapper_disable_md_field'));
      qa_opt('plugin_tinymcewrapper_disable_switch', (int)qa_post_text('plugin_tinymcewrapper_disable_switch_field'));
      qa_opt('plugin_tinymcewrapper_inline_switch', (int)qa_post_text('plugin_tinymcewrapper_inline_switch_field'));
      qa_opt('plugin_tinymcewrapper_elfinder', qa_post_text('plugin_tinymcewrapper_elfinder_field'));
      qa_opt('plugin_tinymcewrapper_elfinder_personal', (int)qa_post_text('plugin_tinymcewrapper_elfinder_personal_field'));
      qa_opt('plugin_tinymcewrapper_q_config', qa_post_text('plugin_tinymcewrapper_q_config_field'));
      qa_opt('plugin_tinymcewrapper_a_config', qa_post_text('plugin_tinymcewrapper_a_config_field'));
      qa_opt('plugin_tinymcewrapper_c_config', qa_post_text('plugin_tinymcewrapper_c_config_field'));
      qa_opt('plugin_tinymcewrapper_switch', qa_post_text('plugin_tinymcewrapper_switch_field'));
      qa_opt('plugin_tinymcewrapper_uni_rte', qa_post_text('plugin_tinymcewrapper_uni_rte_field'));
      qa_opt('plugin_tinymcewrapper_enable_misc', (int)qa_post_text('plugin_tinymcewrapper_enable_misc_field'));
      qa_opt('plugin_tinymcewrapper_misc_selector', qa_post_text('plugin_tinymcewrapper_misc_selector_field'));
      qa_opt('plugin_tinymcewrapper_misc', qa_post_text('plugin_tinymcewrapper_misc_field'));
      qa_opt('plugin_tinymcewrapper_misc_pages', qa_post_text('plugin_tinymcewrapper_misc_pages_field'));
      qa_opt('plugin_tinymcewrapper_uni_md', qa_post_text('plugin_tinymcewrapper_uni_md_field'));
      qa_opt('plugin_tinymcewrapper_md_select', qa_post_text('plugin_tinymcewrapper_md_select_field'));
      $saved = true;
    }
    function getChecked($opt, $checkedVal){
      if(qa_opt($opt) == $checkedVal){
        return 'checked ';
      }
      else{
        return '';
      }
    }
    function getSkin($skinName){
      if(qa_opt('plugin_tinymcewrapper_skin_select') == $skinName){
        return 'selected="selected" value="'.$skinName.'"';
      }
      else{
        return 'value="'.$skinName.'"';
      }
    }
    function getParser($parserName){
      if(qa_opt('plugin_tinymcewrapper_md_select') == $parserName){
        return 'selected="selected" value="'.$parserName.'"';
      }
      else{
        return 'value="'.$parserName.'"';
      }
    }

    qa_set_display_rules($qa_content, array(
    'twMCE_universal_config_wrapper' => 'twMCE_universal_enable',
    'twMCE_individual_config_wrapper' => 'twMCE_individual_enable',
    'twMCE_misc_config_wrapper' => 'twMCE_misc_enable',
    'twMCE_misc_config_wrapper_dummy' => 'twMCE_misc_disable'
    ));
    return array(
      'ok' => $saved ? 'TinymceWrapper Init Config saved!' : null,
      'fields' => array(
        array(
          'type' => 'custom',
          'html' => '
          <script>
          var adminCSS = "<style>.twMCE_config_wrapper{box-shadow: 0px 0px 2px 3px #e0e0e0;padding:9px;line-height:1.5;}.twMCE_config_wrapper p{ font-size:70%;}.twMCE_config_wrapper textarea{resize:vertical;min-height:40px;}.twMCE_config_wrapper textarea:hover,.twMCE_config_wrapper textarea:focus{min-height:500px}</style>";
            $("head").append(adminCSS);
            function resetTwEditor(){
              document.getElementById("twMCE_cdn").value = "//cdn.tinymce.com/4/tinymce.min.js";
              document.getElementById("twMCE_css").value = "'. getMyPath(__DIR__). '/css/style.css";
              document.getElementById("twMCE_question").value = document.getElementById("twMCE_rte_bck").value;
              document.getElementById("twMCE_answer").value = document.getElementById("twMCE_rte_bck").value;
              document.getElementById("twMCE_comment").value = document.getElementById("twMCE_rte_bck").value;

              document.getElementById("twMCE_elfinder").value = document.getElementById("twMCE_elfinder_bck").value;
              document.getElementById("twMCE_universal_rte").value = document.getElementById("twMCE_rte_bck").value;
              document.getElementById("twMCE_universal_md").value = document.getElementById("twMCE_md_bck").value;
              document.getElementById("twMCE_misc").value = document.getElementById("twMCE_misc_bck").value;
              document.getElementById("twMCE_misc_selector").value = "#option_notice_visitor, textarea[id^=option_custom]:not(#option_custom_in_head,#option_custom_header,#option_custom_sidepanel,#option_custom_sidebar,#option_custom_home_content,#option_custom_footer), textarea[name=content], textarea#message";
              document.getElementById("twMCE_misc_pages").value = "admin/users, admin/layout, admin/posting, admin/pages";

              document.getElementById("twMCE_elfinder_personal").checked = false;
              document.getElementById("twMCE_disable").checked = false;
              document.getElementById("twMCE_disable_mobile").checked = false;
              document.getElementById("twMCE_disable_md").checked = false;
              document.getElementById("twMCE_strip_html").checked = false;
              document.getElementById("twMCE_strip_html_v").checked = true;
              document.getElementById("twMCE_strip_html_e").checked = false;
              document.getElementById("twMCE_disable_switch").checked = false;
              document.getElementById("twMCE_inline_switch").checked = false;

              document.getElementById("twMCE_universal_enable").checked = false;
              document.getElementById("twMCE_universal_config_wrapper").style.display = "none";
              
              document.getElementById("twMCE_individual_enable").checked = true;
              document.getElementById("twMCE_individual_config_wrapper").style.display = "block";

              document.getElementById("twMCE_misc_disable").checked = true;
              document.getElementById("twMCE_misc_config_wrapper").style.display = "none";

              document.getElementById("twMCE_skin_select").options[document.getElementById("twMCE_skin_select").options.selectedIndex].selected = false;
              document.getElementById("twMCE_markdown_select").options[document.getElementById("twMCE_markdown_select").options.selectedIndex].selected = false;
              return false;
            }
            $(".twChecked").on("mouseup",function() {
              if (this.checked) {
                $(this).trigger("change").val(0);
              }
              else{
                $(this).trigger("change").val(1);
              }
            });
          </script>'
        ),
        array(
          'type' => 'custom',
          'html' => '
          <input type="hidden" id="twMCE_elfinder_bck" value="'.htmlentities($check_tinymcewrapper_elfinder).'" />
          <input type="hidden" id="twMCE_misc_bck" value="'.htmlentities($tinymcewrapper_config_misc).'" />
          <input type="hidden" id="twMCE_rte_bck" value="'.htmlentities($tinymcewrapper_config_rte).'" />
          <input type="hidden" id="twMCE_md_bck" value="'.htmlentities($tinymcewrapper_config_md).'" />
          '
        ),
        array(
          'type' => 'custom',
          'html' => '
          TinyMCE Core Url (CDN, Local/Enterprise)<br>
          <input type="text" class="qa-form-tall-text" id="twMCE_cdn" value="'.qa_opt('plugin_tinymcewrapper_cdn_url').'" name="plugin_tinymcewrapper_cdn_url_field" placeholder="Please enter a valid tinymce.js url" title="Please enter a valid tinymce.js url" required=1 width=100% /><br>
          Editor Field CSS File Url<br>
          <input type="text" class="qa-form-tall-text" id="twMCE_css" value="'.qa_opt('plugin_tinymcewrapper_css_url').'" name="plugin_tinymcewrapper_css_url_field" placeholder="Please enter a valid css file url" title="Please enter a valid css file url" required=1 width=100% /><br>
          <a href=http://skin.tinymce.com target=_blank title="Design your own TinyMCE skin in seconds"> TinyMCE Skins</a>:
          <select id="twMCE_skin_select" name="plugin_tinymcewrapper_skin_select_field" class="qa-form-tall-select">
            <option '.getSkin("modxPericles").'>modxPericles</option>
            <option '.getSkin("default").'>TinyMCE</option>
            <option '.getSkin("fairOphelia").'>fairOphelia</option>
            <option '.getSkin("light").'>light</option>
            <option '.getSkin("fallenMacbeth").'>fallenMacbeth (beta)</option>
          </select> 

          Parser: <select id="twMCE_markdown_select" name="plugin_tinymcewrapper_md_select_field" class="qa-form-tall-select">
            <option '.getParser("parsedownE").'>Parsedown Extra (best)</option>
            <option '.getParser("parsedown").'>Parsedown</option>
            <option '.getParser("markdownE").'>Markdown Extra</option>
            <option '.getParser("markdown").'>Markdown</option>
          </select> <br>

          <input type="checkbox" '.getChecked("plugin_tinymcewrapper_strip_html", 1).' id="twMCE_strip_html" value="'.(int) qa_opt('plugin_tinymcewrapper_strip_html').'" name="plugin_tinymcewrapper_strip_html_field" class="twChecked qa-form-tall-checkbox"> Disable/strip HTML in Markdown at 
          <input title="VISUAL: saves HTML but will not let it be VIEWED" type="radio" '.getChecked("plugin_tinymcewrapper_strip_html_v_e", 1).' id="twMCE_strip_html_v" value= "1" name="plugin_tinymcewrapper_strip_html_v_e_field" class="qa-form-tall-checkbox">Viewer or 
          <input title="DEEP: will never save HTML to DB (cavete!)" type="radio" '.getChecked("plugin_tinymcewrapper_strip_html_v_e", 2).' id="twMCE_strip_html_e" value= "2" name="plugin_tinymcewrapper_strip_html_v_e_field" class="qa-form-tall-checkbox">Editor level
          <br>
          <input type="checkbox" '.getChecked("plugin_tinymcewrapper_disable_md", 1).' id="twMCE_disable_md" value="'.(int) qa_opt('plugin_tinymcewrapper_disable_md').'" name="plugin_tinymcewrapper_disable_md_field" class="twChecked qa-form-tall-checkbox"> Disable TinymceWrapper Markdown Viewer/Parser
          <br>
          <input type="checkbox" '.getChecked("plugin_tinymcewrapper_disable", 1).' id="twMCE_disable" value="'.(int) qa_opt('plugin_tinymcewrapper_disable').'" name="plugin_tinymcewrapper_disable_field" class="twChecked qa-form-tall-checkbox"> Disable TinymceWrapper Editor (no need to delete the plugin)
          <br>
          <input type="checkbox" '.getChecked("plugin_tinymcewrapper_disable_mobile", 1).' id="twMCE_disable_mobile" value="'.(int) qa_opt('plugin_tinymcewrapper_disable_mobile').'" name="plugin_tinymcewrapper_disable_mobile_field" class="twChecked qa-form-tall-checkbox"> Disable TinymceWrapper Editor in Mobile Mode
          <br>
          <input type="checkbox" '.getChecked("plugin_tinymcewrapper_disable_switch", 1).' id="twMCE_disable_switch" value="'.(int) qa_opt('plugin_tinymcewrapper_disable_switch').'" name="plugin_tinymcewrapper_disable_switch_field" class="twChecked qa-form-tall-checkbox"> Disable Magic Universal Url Switching
          <br>
          <input type="checkbox" '.getChecked("plugin_tinymcewrapper_inline_switch", 1).' id="twMCE_inline_switch" value="'.(int) qa_opt('plugin_tinymcewrapper_inline_switch').'" name="plugin_tinymcewrapper_inline_switch_field" class="twChecked qa-form-tall-checkbox"> Enable Iframe Mode (default is Inline Mode - the true perfect WYSIWYG)
          '
        ),
        array(//misc init
          'type' => 'custom',
          'html' => '
          <hr>
          <input type="radio" value="1" id="twMCE_misc_enable" name="plugin_tinymcewrapper_enable_misc_field" '.getChecked("plugin_tinymcewrapper_enable_misc", 1).' />Use Misc tinymce.init (for Everywhere... etc)
          <input type="radio" value="0" id="twMCE_misc_disable" name="plugin_tinymcewrapper_enable_misc_field" '.getChecked("plugin_tinymcewrapper_enable_misc", 0).' />off
          <div id=twMCE_misc_config_wrapper_dummy style="visibility:hidden!important;height:0!important;width:0!important;"></div>
          <div id=twMCE_misc_config_wrapper class="twMCE_config_wrapper">
          <input type="text" value="'.htmlentities(qa_opt('plugin_tinymcewrapper_misc_selector')).'" placeholder="Enter comma-separated list of jQuery/CSS-style Selectors" title="Enter comma-separated list of jQuery/CSS-style Selectors" id="twMCE_misc_selector" name="plugin_tinymcewrapper_misc_selector_field" class="qa-form-tall-text" /><br>
          <p>(jQuery/CSS-style selectors)<br><b>#option_notice_visitor</b>: for new user notice, <b>textarea[name=content]</b>: for custom pages, <b>textarea#message</b>: for private message, <b>textarea[id^=option_custom]</b>: for <b>all</b> Admin inputs that allow HTML (use more specific selector if you wish).<br>Remove ids from <b>:not(#option_custom...)</b> if you want them transformed<br><br><b>Note</b>: you can have unique configuration for each selector by multiplying <i>tinymce.init</i> and altering <i>[[+ID]]</i>. Play around with the configs and enjoy!
          </p>
          <input type="text" value="'.htmlentities(qa_opt('plugin_tinymcewrapper_misc_pages')).'" placeholder="Enter comma-separated list of Page Request Rules" title="Enter comma-separated list of Page Request Rules" id="twMCE_misc_pages" name="plugin_tinymcewrapper_misc_pages_field" class="qa-form-tall-text" />
          <p>(page request rules)<br><b>admin/layout ...</b> editor will fire only on specified pages<br>
          Add <b>message</b> for private message textarea, <br>
          Add <b>user</b> for user wall textarea<br><br>
          <b>Note:</b> this plugin is not responsible for parsing Rich Text Messages
          </p>
          Misc init (*no markdown)<br>
          <textarea class="qa-form-tall-text" id="twMCE_misc" name="plugin_tinymcewrapper_misc_field">'. qa_opt("plugin_tinymcewrapper_misc").'</textarea></div>'
        ),
        array(//universal init
          'type' => 'custom',
          'html' => '
          <input type="radio" value="universal" id="twMCE_universal_enable" name="plugin_tinymcewrapper_switch_field" '.getChecked("plugin_tinymcewrapper_switch", "universal").' />Use Magic Universal tinymce.init (for Q,A,C)
          <div id=twMCE_universal_config_wrapper class="twMCE_config_wrapper">
          <span style=font-size:12px;>Question, Answer and Comments will use only one init/mode. Magically switch by using URL paramater...<br>E.g. use Default field for RTE and Alternative for Markdown: <i>eg.com/the-best-editor-ever?editor=2 (or 1 - default)</i>
          </span><br><br>
          Default init (1)<br>
          <textarea class="qa-form-tall-text" id="twMCE_universal_rte" name="plugin_tinymcewrapper_uni_rte_field">'. qa_opt('plugin_tinymcewrapper_uni_rte').'</textarea><br><br>
          Alternative init (2)<br>
          <textarea class="qa-form-tall-text" id="twMCE_universal_md" name="plugin_tinymcewrapper_uni_md_field">'. qa_opt('plugin_tinymcewrapper_uni_md').'</textarea><br>
          </div>'
        ),

        array(//precise init
          'type' => 'custom',
          'html' => '
          <input type="radio" value="individual" id="twMCE_individual_enable" name="plugin_tinymcewrapper_switch_field" '.getChecked("plugin_tinymcewrapper_switch", "individual").' />Use Individual/Precise tinymce.init (for Q,A,C)
          <div id=twMCE_individual_config_wrapper class="twMCE_config_wrapper">
          <p>Force Question, Answer and Comments to each have own init. No limits, use your imagination!</p>
          Question init<br>
          <textarea class="qa-form-tall-text" id="twMCE_question" name="plugin_tinymcewrapper_q_config_field">'. qa_opt('plugin_tinymcewrapper_q_config').'</textarea><br><br>
          Answer init<br>
          <textarea class="qa-form-tall-text" id="twMCE_answer" name="plugin_tinymcewrapper_a_config_field">'. qa_opt('plugin_tinymcewrapper_a_config').'</textarea><br><br>
          Comment init<br>
          <textarea class="qa-form-tall-text" id="twMCE_comment" name="plugin_tinymcewrapper_c_config_field">'. qa_opt('plugin_tinymcewrapper_c_config').'</textarea>
          </div>
          <script>
            $(".twChecked").on("mouseup",function() {
              if($(this).is(":checked")){
                $(this).trigger("change").val(0);
              }
              else{
                $(this).trigger("change").val(1);
              }
            });
          </script>
          '
        ),
        array(//elfinder json
          'type' => 'custom',
          'html' => '
          <br>
          <hr>
          <br>
          <b>*elFinder Volumes</b> {error-free json only} <br><input type="checkbox" '.getChecked("plugin_tinymcewrapper_elfinder_personal", 1).' id="twMCE_elfinder_personal" value="'.(int) qa_opt('plugin_tinymcewrapper_elfinder_personal').'" name="plugin_tinymcewrapper_elfinder_personal_field" class="twChecked qa-form-tall-checkbox"> Enable Personal Volume<br>
          <div class="twMCE_config_wrapper">
          <p>If specified, <b>Personal Volume</b> will create the folder specified in <b>path</b> (e.g example.com/q2a/media/jonathan0904/<br>
          <b>Note!</b> If your Q2A user-level JSON has <b>multiple volume arrays</b>, Personal Volume targets <b>only the first[0]</b><br>
          <b>Also, [[+Q2A-USERNAME]]</b> must be present in the path and url of your Personal Volume array.
          </p>
          <textarea type="button" id="twMCE_elfinder" value="elFinder Super Settings" class="qa-form-tall-text" name="plugin_tinymcewrapper_elfinder_field">'. qa_opt('plugin_tinymcewrapper_elfinder').'</textarea>
          </div>'
          )
      ),
      'buttons' => array(
        array(
          'label' => 'Reset to Defaults',
          'tags' => 'onclick="resetTwEditor(); return false;" class="qa-form-wide-button qa-form-wide-button-reset"'
        ),
        array(
          'label' => 'Commit Changes',
          'tags' => 'name="plugin_tinymcewrapper_save_button" class="qa-form-wide-button qa-form-wide-button-save"'
        )
      )
    );
  }
  public function focus_script($fieldname)
  {
    if(!qa_opt('plugin_tinymcewrapper_disable') && (qa_opt('plugin_tinymcewrapper_disable_mobile') + qa_is_mobile_probably() !== 2)){
      return "
        tinymcewrapperFocus('".$fieldname."');
      ";
    }
  }
  public function read_post($fieldname)
  {
    $format = 'html';
    $html   = qa_post_text($fieldname);
    if(!qa_opt('plugin_tinymcewrapper_disable') && (qa_opt('plugin_tinymcewrapper_disable_mobile') + qa_is_mobile_probably() !== 2)){
      if(qa_opt("plugin_tinymcewrapper_switch") == "universal"){
        if((int)qa_opt("plugin_tinymcewrapper_switch_uni") === 1){
          $fieldnamePattern  = 'plugin_tinymcewrapper_uni_rte';
        }
        else{
          $fieldnamePattern  = 'plugin_tinymcewrapper_uni_md';
        }
      }
      else{
        if($fieldname === 'content'){
          $fieldnamePattern  = 'plugin_tinymcewrapper_q_config';
        }
        else{
          $fieldnamePattern  = 'plugin_tinymcewrapper_'.substr($fieldname, 0, 1).'_config';
        }
      }
      if (strpos(qa_opt($fieldnamePattern), 'twExoticMarkdownEditor') !== false && !qa_opt('plugin_tinymcewrapper_disable_md')) {
        $format = "markdown";
      }

      $stripTags = qa_opt("plugin_tinymcewrapper_strip_html");
      if ((int)$stripTags === 1 && (int)qa_opt("plugin_tinymcewrapper_strip_html_v_e") === 2) {
        // Markdown links, that looks like tag
        $input = preg_replace('#<(.*?(://|@).*?)>#', '&lt;$1&gt;', $html);
        // Strip tags
        if (is_numeric($stripTags)) { // in the future allow users to specify
            $stripTags = '';
        }
        else { //not enabled in this version
          $tmp = explode(',', $stripTags);
          $tmp2 = array();
          foreach ($tmp as $v) {
              $tmp2[] = '<' . trim($v, '<> ') . '>';
          }
          $stripTags = implode($tmp2);
        }
        $input = strip_tags($input, $stripTags);
      }
      else{
        $input = qa_sanitize_html($html, false, true);
      }
      return array(
        'format' => $format,
        'content' => $input
      );
    }
    else{
      return array(
        'format' => 'markdown',
        'content' => qa_post_text($fieldname),
      );
    }
  }

  public function html_to_text($html)
  {
    $viewer = qa_load_module('viewer', '');
    return $viewer->get_text($html, 'html', array());
  }
}