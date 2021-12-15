define(function(require){
  var Backbone = require('backbone');
  
  return Backbone.Model.extend({
     defaults: {
       page: 1
     },
     initialize: function() {
       this.enabledCounters = [];
       this.availableCounters = this.get('available_per_page');
     },
     getCounters: function(){
      var self = this,
          nbresults = this.get('nbresults'),
          per_pager = this.get('per_page');
      
      this.enabledCounters = [];
      for (var i in this.availableCounters)
      {
        if( nbresults >= this.availableCounters[i] || 
           (i !== 0 && this.enabledCounters[i-1] <= nbresults ) || 
            this.availableCounters[i] <= per_pager) 
          
          this.enabledCounters.push(this.availableCounters[i]);
      }
      
      return this.enabledCounters;
     },
     getPages: function(){
       return this.get('pages');
     },
     toJSON: function() {
       return {
         counters: this.getCounters(),
         pages:    this.getPages(),        
         links:    this.get('links'),
         per_page: this.get('per_page'),
         nbresults: this.get('nbresults'),
         haveToPaginate: this.get('links').length > 1,
         first_link: this.get('pages').first_link,
         nb_links: this.get('nb_links'),
         last_link:   this.get('pages').last_link
       }
     }
  });
});
  