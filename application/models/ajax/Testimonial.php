<?php

class Model_Ajax_Testimonial {

  public static function uploadPicture() {
    $image = $_FILES['Filedata'];

    if(!Model_Operation_File::hasFileExtension($image['name'], Model_Operation_File::$image_type_extensions)){
      $response = array(
        'status'  =>  'error',
        'error'   =>  str_replace(
          '%allowed_extensions%',
          implode(', ', Model_Operation_File::$image_type_extensions),
          'File Extension not allowed : %allowed_extensions%'
        )
      );

      echo json_encode($response);
      exit;
    }


    $random_string = (md5(time()) . substr(md5(time() / 2), 0 , 8));

    $file_name  = ltrim(str_replace(rand(1, 9), '_' , $random_string), '_');

    $image_name =  $file_name . Model_Operation_File::getFileExtension($image['name'], true);

    $target = BASE_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'people_images' . DIRECTORY_SEPARATOR . $image_name;

    move_uploaded_file($_FILES['Filedata']['tmp_name'], $target);

    chmod($target, 0777);

    $status = array(
      'status'    =>  'ok',
      'new_path'  =>  'assets/people_images/' . $image_name,
      'image_name'=>  $image_name
    );

    echo json_encode($status);
    exit;
  }

  public static function add() {
    $testimonial = Model_Helper_Request::getParam('testimonial');

    $testimonial = Model_Helper_Array::purifyArray($testimonial);

    if(Model_Constant::DISABLED_TESTIMONIAL_ADD == true) {
      return array(
        'status' => 'ok',
        'text'   => 'Not added, shame. this error should never come up',
        'html'   => Application_View::getInstance()->partial('testimonial_single.phtml',
          array('testimonial'  =>  $testimonial)
        )
      );
    }

    $testimonialStatus = Model_Testimonial::getInstance()->insert($testimonial);

    return array(
      'status' => 'ok',
      'text'   => Model_Constant::LANG_TESTIMONIAL_ADDED,
      'html'   => Application_View::getInstance()->partial('testimonial_single.phtml',
        array('testimonial'  =>  $testimonial)
      )
    );
  }

  public static function preview() {
    $testimonial = Model_Helper_Request::getParam('testimonial');

    $testimonial = Model_Helper_Array::purifyArray($testimonial);

    return array(
      'status' => 'ok',
      'html'   => Application_View::getInstance()->partial('testimonial_single.phtml',
        array('testimonial'  =>  $testimonial)
      )
    );
  }

}