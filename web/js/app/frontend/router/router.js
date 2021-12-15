define(function (require) {
  var Backbone = require('backbone'),
      CommonView = require('view/common/common-view'),
      LearnView = require('view/learn/learn-view'),
      LearnTextShowPageView = require('view/learn/text/learn-text-show-page-view'),
      HomepageView = require('view/homepage/homepage-view'),
      LoginView = require('view/security/login-view'),
      ArticleQuestionPageView = require('view/article/question/article-question-page-view'),
      ArticleQuestionIndexPageView = require('view/article/question/article-question-index-page-view'),
      ArticleTextIndexPageView = require('view/article/text/article-text-index-page-view'),
      ArticleTextShowPageView = require('view/article/text/article-text-show-page-view'),
      VideoView = require('view/video/video-view');
  return Backbone.Router.extend({
    defaultPageAction: function (View, options) {
      var view = new View({
        el: $('body'),
        options: options
      });
      jQuery(function () {
        view.render();
      });
    },
    commonAction: function () {
      this.defaultPageAction(CommonView)
    },
    loginAction: function(){
      this.defaultPageAction(LoginView)
    },
    homepageAction: function() {
      this.defaultPageAction(HomepageView)
    },
    videoAction: function() {
      this.defaultPageAction(VideoView)
      /*var view = new VideoView({
        el: $('body .container.video'),
        options: options
      });
      jQuery(function () {
        view.render();
      });*/
    },
    articleQuestionAction: function(id){
      this.defaultPageAction(ArticleQuestionPageView);
    },
    articleQuestionsCategoryAction: function(){
      this.defaultPageAction(ArticleQuestionIndexPageView);
    },
    articleTextIndexAction: function(){
      this.defaultPageAction(ArticleTextIndexPageView);
    },
    articleTextShowAction: function(){
      this.defaultPageAction(ArticleTextShowPageView);
    },
    learnTextShowAction: function(){
      this.defaultPageAction(LearnTextShowPageView);
    },
    learnAction: function () {
      this.defaultPageAction(LearnView)
    },
    initialize: function () {
      var routePrefix = urlPrefix || '';
      if (routePrefix.length && routePrefix[0] == '/') {
        routePrefix = routePrefix.substr(1);
      }
      if (routePrefix.length > 0 && routePrefix.slice(-1) != '/') {
        routePrefix += '/';
      }
      this.route(routePrefix + '*path', this.commonAction);
      this.route(routePrefix, this.homepageAction);
      if (routePrefix.length > 1){
        this.route(routePrefix.slice(0, -1), this.homepageAction);
      }
      this.route(routePrefix + 'study', this.learnAction);
      this.route(routePrefix + 'video-lessons', this.videoAction);
      this.route(routePrefix + 'login', this.loginAction);
      this.route(routePrefix + 'qna', this.articleQuestionsCategoryAction);
      this.route(routePrefix + 'qna/:category', this.articleQuestionsCategoryAction);
      this.route(routePrefix + 'questions/:slug', this.articleQuestionAction);
      this.route(routePrefix + 'articles', this.articleTextIndexAction);
      this.route(routePrefix + 'articles/:id', this.articleTextShowAction);
      this.route(routePrefix + 'study/:id', this.learnTextShowAction);
    }
  });
});