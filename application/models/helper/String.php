<?php

class Model_Helper_String {

  public static $_bbCode = array("<", ">",
                                  "[list]", "[*]", "[/list]",
                                  "[img]", "[/img]",
                                  "[b]", "[/b]",
                                  "[u]", "[/u]",
                                  "[i]", "[/i]",
                                  '[color=', "[/color]",
                                  '[size=', "[/size]",
                                  '[url=', "[/url]",
                                  '[mail=', "[/mail]",
                                  "[code]", "[/code]",
                                  "[quote]", "[/quote]",
                                  ']');

  public static $_htmlCode =  array("&lt;", "&gt;",
                                    "<ul>", "<li>", "</ul>",
                                    '<img style="max-width: 500px;" src="', '">',
                                    "<b>", "</b>",
                                    "<u>", "</u>",
                                    "<i>", "</i>",
                                    '<span style="color:', "</span>",
                                    '<span style="font-size:', "</span>",
                                    '<a target="_blank" href="', "</a>",
                                    '<a href="mailto:', "</a>",
                                    "<code>", "</code>",
                                    '<section class="quote">', "</section>",
                                    '">');
  public static function html2bb($text) {
    $newtext = str_replace(self::$_htmlCode, self::$_bbCode, $text);

    return $newtext;
  }

  public static function bb2html($text) {
    $newtext = str_replace(self::$_bbCode, self::$_htmlCode, $text);

    return $newtext;
  }

  public static function makeUrl($text){
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
    preg_match_all($reg_exUrl, $text, $matches);
    $usedPatterns = array();
    foreach($matches[0] as $pattern){
      if(!array_key_exists($pattern, $usedPatterns)){
        $usedPatterns[$pattern]=true;
        $text = str_replace  ($pattern, '<a href="'.$pattern.'" target="_blank" rel="nofollow">'.$pattern.'</a> ', $text);
      }
    }
    return $text;
  }

  public static function newLineToBr($data) {
    return str_replace("\n", '<br>', $data);
  }

  public static function brToNewLine($data) {
    return str_replace('<br>', "\n", $data);
  }

  public static function purifyHTML($data){
    // Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

    // Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

    // Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do
    {
      // Remove really unwanted tags
      $old_data = $data;
      $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    }
    while ($old_data !== $data);

    // we are done...
    return $data;
  }

  public static function handleCommonRenderSettings($content) {
    if(strpos($content, '[quote]') !== FALSE && strpos($content, '[/quote]') !== FALSE)
      $content = str_replace(array('[quote]', '[/quote]'), array('<section class="quote">', '</section>'), $content);

    return $content;
  }

  public static function shortenName($name, $troubleshoot = 30) {
    return strlen($name) > $troubleshoot ? substr($name, 0, $troubleshoot - 3).'...' : $name;
  }
}