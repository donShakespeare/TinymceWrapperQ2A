<?php
/*
File: qa-plugin/tinymcewrapper/qa-tinymcewrapper.php
Description: The most powerful RTE with full markdown support

donshakespeare @ MODX forums
https://github.com/donShakespeare

To God almighty be all glory
*/
class qa_tinymcewrapper_viewer
{
  private $plugindir;

  public function calc_quality($content, $format)
  {
    if(!qa_opt('plugin_tinymcewrapper_disable_md')){
      return $format == 'markdown' ? 2.0 : 0.5;
    }
  }
  public function get_html($content, $format, $options)
  {
    if(!qa_opt('plugin_tinymcewrapper_disable_md')){
      if(isset($options['blockwordspreg'])) {
        require_once QA_INCLUDE_DIR . 'qa-util-string.php';
        $content = qa_block_words_replace($content, $options['blockwordspreg']);
      }

      $stripTags = qa_opt("plugin_tinymcewrapper_strip_html");
      if ((int)$stripTags === 1 && (int)qa_opt("plugin_tinymcewrapper_strip_html_v_e") === 1) {
        // Markdown links, that looks like tag
        $input = preg_replace('#<(.*?(://|@).*?)>#', '&lt;$1&gt;', $content);
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
        $content = strip_tags($input, $stripTags);
      }

      $content = str_replace("&gt;", ">", $content);
      $twMDoptions = qa_opt('plugin_tinymcewrapper_md_select') ?: "parsedownExtra";
      if($twMDoptions == "markdown") {
        require_once $this->plugindir . 'markdown/Michelf/Markdown.inc.php';
        $content = \Michelf\Markdown::defaultTransform($content);
      } elseif($twMDoptions == "markdownE") {
        require_once $this->plugindir . 'markdown/Michelf/MarkdownExtra.inc.php';
        $content = \Michelf\MarkdownExtra::defaultTransform($content);
      } elseif($twMDoptions == "parsedown") {
        require_once $this->plugindir . 'markdown/parsedown/Parsedown.php';
        $Parsedown = new Parsedown();
        $content   = $Parsedown->text($content);
      } else { //default state
        $twMDoptions = "parsedownExtra";
        require_once $this->plugindir . 'markdown/parsedown/Parsedown.php';
        require_once $this->plugindir . 'markdown/parsedown/ParsedownExtra.php';
        $ParsedownExtra = new ParsedownExtra();
        $content        = $ParsedownExtra->text($content);
      }
      // $html = $twMDoptions . $content;
      $html = $content;
      return qa_sanitize_html($html); //security reasons
    }
  }
  public function get_text($content, $format, $options)
  {
    if(!qa_opt('plugin_tinymcewrapper_disable_md')){
      $viewer = qa_load_module('viewer', '');
      $text   = $viewer->get_text($content, 'html', array());
      return $text;
    }
  }
}
