/**
 * @author Andrei-Robert Rusu
 * @type {{ActiveComponents: number, _map: {}, _components: {}, _buildComponents: {}, _componentCollectionMap: {}, Init: Function, GetComponent: Function, RunComponentFromObject: Function, _setComponentInCollection: Function, FetchComponentInComponentCollection: Function, RunComponent: Function, _defineMAP: Function}}
 */
var Component = {

  ActiveComponents : 0,
  _map : {},
  _components : {},
  _buildComponents : {},
  _componentCollectionMap : {},

  Init : function() {
    this._defineMAP();
    var objectInstance = this;

    jQuery('[data-component]').each(function(event){
       objectInstance.RunComponentFromObject(jQuery(this));
    });
  },

  GetComponent : function(component_id) {
    return this._buildComponents[component_id];
  },

  RunComponentFromObject : function(element) {
    var objectInstance = this;

    if(typeof element.attr('data-component-is-dispatched') !== "undefined"
        && element.attr('data-component-is-dispatched') == true)
      return;

    if(element.attr('data-component') !== "undefined") {
      if(typeof Component._map[element.attr('data-component')] == "object") {
        var component_name = element.attr('data-component');
        var component = Component._map[component_name];

        var attributes = element.getAttributes();

        var params = {};

        jQuery.each(attributes, function(key, value){
          if(key.startsWith('data-component')) {
            var name = key.replace('data-component-', '');

            name = name.replace(/-/g, '_');

            params[name] = value;
          }
        });

        params.container = element;

        Component.RunComponent(component_name, params);
      }
    }
  },

  _setComponentInCollection : function(component_id, component_name) {
    if(typeof this._componentCollectionMap[component_name] != "object")
      this._componentCollectionMap[component_name] = [];

    this._componentCollectionMap[component_name][this._componentCollectionMap[component_name].length] = component_id;
  },

  FetchComponentInComponentCollection : function(component_name) {
    if(typeof component_name == "string")
      return this._componentCollectionMap[component_name];

    var objectInstance = this,
        information = [];

    jQuery.each(component_name, function(key, name){
      if(typeof objectInstance._componentCollectionMap[name] != "undefined")
        information = information.concat(objectInstance._componentCollectionMap[name])
    });


    return information;
  },

  RunComponent : function(component_name, params) {
    var objectInstance = this;
    var component = Component._map[component_name];
    var component_id = ++Component.ActiveComponents;

    params.component_id = component_id;

    this._setComponentInCollection(component_id, component_name);

    if(!jQuery.isFunction(component.handler)) {
      jQuery.getScript('assets/scripts/component/' + component.file)
          .done(function(script, textStatus) {
            objectInstance._defineMAP();
            component = Component._map[component_name];

            Component._buildComponents[component_id] = jQuery.extend(true, {}, component.handler);
            Component._buildComponents[component_id].Init(params);

            params.container.attr('data-component-is-dispatched', 1);
          });
    } else {
      Component._buildComponents[component_id] = jQuery.extend(true, {}, component.handler);
      Component._buildComponents[component_id].Init(params);

      params.container.attr('data-component-is-dispatched', 1);
    }
  },

  _defineMAP : function() {
    this._map = {
      'testimonial-add'  : {
        'file'   : 'testimonial-add.js',
        'handler': Component.TestimonialAdd
      }
    };
  }

};

$.fn.hasAttr = function(name) {
  return this.attr(name) !== undefined;
};

(function($) {
  $.fn.getAttributes = function() {
    var attributes = {};

    if( this.length ) {
      $.each( this[0].attributes, function( index, attr ) {
        attributes[ attr.name ] = attr.value;
      } );
    }

    return attributes;
  };
})(jQuery);