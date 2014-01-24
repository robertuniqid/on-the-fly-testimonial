<?php

class Model_Helper_Array {

  public static function purifyArray($array) {
    foreach($array as $key => $value)
      $array[$key] = Model_Helper_String::purifyHTML($value);

    return $array;
  }

}