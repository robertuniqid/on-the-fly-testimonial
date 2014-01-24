/**
 * @author Andrei-Robert Rusu
 */
Component.TestimonialAdd = {

  ComponentId        : 0,
  Container          : null,
  ContainerId        : null,
  PlayerContainer    : null,
  PlayerContainerId  : null,
  TrackList          : {},
  TrackCount         : 0,
  CurrentTrackNumber : 0,

  Init : function(information) {

    if(typeof information.container != "object") {
      alert('Testimonial Add - Invalid Container');
      return;
    }

    this.Container = information.container;
    this.ComponentId = information.component_id;

    if(typeof this.Container.attr('id') === "undefined")
      this.Container.attr('id', 'component-testimonial-add-' + this.ComponentId);

    this.ContainerId = this.Container.attr('id');

    this.arrangeContent();
    this.bindSubmit();

    var objectInstance = this;

    jQuery(window).bind('resize screenrotate', function(){
      objectInstance.arrangeContent();
    });

    objectInstance.imageUpload();
  },

  bindSubmit : function() {
    var objectInstance = this;

    this.Container.bind('submit', function(event) {
      event.preventDefault();

      if(jQuery(this).hasClass('on-going'))
        return;

      jQuery(this).slideUp('slow');
      jQuery(this).addClass('on-going');

      objectInstance.addTestimonial(jQuery(this).serialize());
    })
  },

  addTestimonial : function(information) {
    try {
      var JsonClient = new AjaxFramework.Client();
      JsonClient.setAjaxMethod('Testimonial.add');
      JsonClient.setData(information, true);
      JsonClient.setRequestMethod('POST');
      JsonClient.setResponseGlue('JSON');
      JsonClient.setOkCallBack(this.ajaxAddTestimonialOk);
      JsonClient.setErrorCallBack(this.ajaxAddTestimonialError);
      JsonClient.setResponseObjectSpace(this);
      JsonClient.Run();
    } catch(ex){
      console.log(ex);
    }
  },

  ajaxAddTestimonialOk : function(data) {
    this.Container.html('<p class="alert alert-success">' + data.text + '</p>');
    this.Container.append(data.html);

    this.Container.slideDown('slow');
  },

  ajaxAddTestimonialError : function(data) {
    this.Container.html('<p class="alert alert-danger">Could not establish connection to server.</p>');
  },


  arrangeContent : function() {
    this.Container.find('> .rightSide textarea').css('height', '0');

    var leftSideHeight  = parseInt(this.Container.find('> .leftSide').height(), 10),
        rightSideHeight = parseInt(this.Container.find('> .rightSide').height(), 10);

    if(leftSideHeight > rightSideHeight) {
      this.Container.find('> .rightSide textarea').css('height',
          leftSideHeight - rightSideHeight
      );
    }

    this.Container.find(' figure > img').each(function(){
      jQuery(this).parent().css('height', jQuery(this).height());
    });
  },

  imageUpload : function () {
    var objectInstance = this;

    this.Container.find('.pictureUpload').uploadify({
      'height'      : '28',
      'width'       : '140',
      'uploader'    : 'ajax?am=Testimonial.uploadPicture',
      'cancelImg'   : 'assets/uploadify/uploadify-cancel.png',
      'swf'         : 'assets/uploadify/uploadify.swf',
      'buttonText'  : 'Upload New',
      'scriptData'  : { },
      'onUploadSuccess'  : function(file, response) {
        response = $.parseJSON(response);

        objectInstance.Container.find('figure > img').attr('src', response.new_path);
        objectInstance.Container.find('.image_hidden_input').val(response.new_path);
      }
    });
  }

};
