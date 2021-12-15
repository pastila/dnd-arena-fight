(function($){
  $.widget('ui.jstree_choice', {
    options: {
      json_data: [],
      multiple: false
    },
    _create: function(){
      this._createTree()
    },
    _createTree: function(){
      var self = this,
          el = this.element;
      
      $(el).jstree({        
        json_data: { data: self.options.json_data },
        themes:{
          "theme" : "classic",
          "dots" : false,
          "icons" : false
        },
        checkbox: {
          real_checkboxes: true,
          two_state: true,
          checked_parent_open: true,
          real_checkboxes_names: function(n) {            
            return self.generateName(n.data('id'));
          }
        },
        plugins: ["themes", "json_data", "checkbox"]
      })
      .bind("loaded.jstree", function(jstree, data) {
        var checked = data.inst.get_checked(null, true);
        $.each(checked, function(i, object) {
          $(object).parents(".jstree-closed").each(function () { data.inst.open_node(this, false, true); });
        });
        
      }).bind("before.jstree", function (e, data) {
          if (data.func === "check_node") {
              if (data.inst.get_checked().length >= 1 && !self.options.multiple) {
                  data.inst.uncheck_all();      
              }
          }
      })
      .delegate("a", "click.jstree", function (event, data) { 
              event.preventDefault();
              $(event.target).find('.jstree-checkbox').click() 
       })
    },
    generateName: function(id) {
      var name = this.options.name + (this.options.multiple ? '[]' : '');
      return [name, id];
    }
  })
})(jQuery)


