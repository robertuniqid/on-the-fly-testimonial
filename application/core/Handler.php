<?php
/**
 * @author Andrei Robert Rusu
 * @throws Exception
 */
class Application_Handler {
  protected static $_instance;


  /**
   * Retrieve singleton instance
   *
   * @return Application_Handler
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

  /**
   * @var Model_Container
   */
  public $view;

  public function __construct() {
    global $actionName;

    $this->init();
  }

  public function init() {
    $this->view = new Model_Container();

    $this->view->title = Model_Constant::SCRIPT_NAME . ' ';
  }

  public function ajaxAction() {
    $dispatcher = new Model_AjaxDispatcher();

    $dispatcher->indexAction();
    exit;
  }

  public function loginAction() {
    $this->view->title .= '| Login';
  }

  public function indexAction() {
    $this->view->title .= '| Home';

    Application_View::getInstance()->loadView('index.phtml', $this->view->getStorage());
  }


  public function errorAction() {
    $this->view->title = '';

    Application_View::getInstance()->loadView('error.phtml', $this->view->getStorage());
  }

  private function _redirectAction($message, $redirect_url, $message_type = 'success') {
    $this->view->message = $message;
    $this->view->redirect_url = $redirect_url;
    $this->view->message_type = $message_type;

    Application_View::getInstance()->loadView('redirect.phtml', $this->view->getStorage());
    exit();
  }

}
