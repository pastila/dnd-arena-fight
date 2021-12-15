requirejs.config({
  "baseUrl": "/js/app/frontend",
  "waitSeconds": 600,
  "paths": {
    "underscore": "/js/vendor/underscore/underscore",
    "backbone": "/js/vendor/backbone/backbone",
    "jquery": "/js/vendor/jquery-3.2.1",
    "lazyload": "/js/vendor/jquery.lazyload",
    "jquery.easing": "/js/vendor/jquery.easing.1.3",
    "jquery-validate": "/js/vendor/jquery-validate/jquery-validate",
    "slick": "/js/vendor/slick/slick"
  },
  "shim": {
    "backbone": {"deps": ["underscore"], "exports": "Backbone"},
    "underscore": {"exports": "_"},
    "jquery.easing": ["jquery"]
  }
});

define('jquery', [], function () {
  return jQuery;
});

require([
  'backbone',
  'router/router'
], function (Backbone, Router) {

  if (typeof ObjectCache != 'object') {
    ObjectCache = {};
  }

  var router = new Router();

  Backbone.history.start({
    hashChange: false,
    pushState: true
  });
});
