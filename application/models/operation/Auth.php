<?php

class Model_Operation_Auth {

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_Operation_Auth
   */
  public static function getInstance()
  {
    if (null === self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  /**
   * Reset the singleton instance
   *
   * @return void
   */
  public static function resetInstance()
  {
    self::$_instance = null;
  }

  public $currentUserId = 0;
  public $hasAccess     = 0;

  public function __construct() {
    if(Model_User::getInstance()->hasEntries() == false) {
      $this->currentUserId = 0;
      $this->hasAccess     = 1;
    } else {

    }
  }

}