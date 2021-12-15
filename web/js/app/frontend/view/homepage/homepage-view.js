define(function (require) {
  var Backbone = require('backbone'),
    GalleryItem = require('view/homepage/gallery/gallery-item'),
    CommonView = require('view/common/common-view');
  require('slick');

  return CommonView.extend({
    events: _.extend({}, CommonView.prototype.events, {

    }),
    initialize: function() {
      CommonView.prototype.initialize.apply(this, arguments);
      var self = this;
      this.galleryItems = [];

      this.$('.inspect-content__item').each(function(index, $el){
        var item = new GalleryItem({
          el: $el
        });
        self.galleryItems.push(item);
      });
    },
    render: function() {
      CommonView.prototype.render.apply(this, arguments);
      var self = this;
      this.$('.header-slider').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        infinite: false
      });

      this.$('.header-slider').on('beforeChange' ,function (event, slick, currentSlide, nextSlide) {
        var $curSlide = slick.$slides[nextSlide];
        if (typeof $($curSlide).find('img').src == "undefined") {

          $($curSlide).find('img').attr('src', $($curSlide).find('img').attr('data-src'));
          $($curSlide).find('img').show();
        }
      });

      this.$('.slider-panel.top').slick({
        arrows: true,
        appendArrows: this.$('.header-content__wrap .slide-controls'),
        nextArrow: "<span class=\"slide-controls__next\"></span>",
        prevArrow: "<span class=\"slide-controls__prev disabled\"></span>",
        asNavFor: '.header-slider',
        fade: true,
        infinite: false,
        cssEase: 'fast'
      });

      this.slickInitVerticalSlider(); //инициализация вертикального слайдера, синхрон с навигацией
      this.slickInitHorizontalSlider(); //инициализация вертикального слайдера, синхрон с навигацией

      var self = this,
        $verticalSlider = this.$('.vertical-slider'),
        $horizontalSlider = this.$('.horizontal-slider'),
        wScrollTop = $(window).scrollTop();

      if ($verticalSlider.length) {
        if (wScrollTop + $(window).height() >= $verticalSlider.offset().top) {
          this.lazyLoadImageSliderVertical();
        }
      }

      if ($horizontalSlider.length) {
        if (wScrollTop + $(window).height() >= $horizontalSlider.offset().top) {
          this.lazyLoadImageSliderHorizontal();
        }
      }

      $(window).scroll(function (event) {
        self.st = $(this).scrollTop();
        if ($verticalSlider.length) {
          if (self.st + $(window).height() + 200 >= $verticalSlider.offset().top) {
            self.lazyLoadImageSliderVertical();
          }
        }
        if ($horizontalSlider.length) {
          if (self.st + $(window).height() >= $horizontalSlider.offset().top) {
            self.lazyLoadImageSliderHorizontal();
          }
        }
        self.initializeHeaderMenu(event)
      });

      this.$('.header-menu__search').on('click', function() {
        self.$('.header-menu__input').focus();
      });

      this.lazyLoadImageSliderHeader()

      return this;
    },
    lazyLoadImageSliderHeader: function() {

      var $headerSliderFirstImage = this.$('.header-slider img[data-src]')[0];

      if ($headerSliderFirstImage) {
        this._loadImage($headerSliderFirstImage);
      }
    },
    lazyLoadImageSliderVertical: function() {
      var $verticalSliderImages = this.$('.vertical-slider img[data-src]');

      if ($verticalSliderImages.length) {
        this._loadImages($verticalSliderImages);
        this.slickInitVerticalSlider()
      }
    },
    lazyLoadImageSliderHorizontal: function() {
      var $horizontalSliderImages = this.$('.horizontal-slider img[data-src]');

      if ($horizontalSliderImages.length) {
        this._loadImages($horizontalSliderImages);
        this.slickInitVerticalSlider()
      }
    },
    _loadImages: function (ImageArr) {
      [].forEach.call(ImageArr, (img) => {
        this._loadImage(img);
      });
    },
    _loadImage: function (img) {
      if (!img.hasAttribute('src')) {
        img.setAttribute('src', img.getAttribute('data-src'));
        img.onload = function () {
          img.removeAttribute('data-src');
        };
      }
    },
    slickInitHorizontalSlider: function () {
      if ( $('.horizontal-slider').hasClass('slick-initialized') ) {
        $('.horizontal-slider').slick('refresh');
      }
      if ( !$('.horizontal-slider').hasClass('slick-initialized') ) {
        $('.horizontal-slider').slick({
          swipe: true,
          rows: 0,
          arrows: false,
          infinite: false,
          slidesToShow: 3,
          slidesToScroll: 1,
          centerMode: false,
          centerPadding: `20px`,
          vertical: false,
          verticalSwiping: false,
          variableWidth: true,
          asNavFor: '.slider-panel.use',
          responsive: [
            {
              breakpoint: 1024,
              settings: {
                centerMode: true,
                infinite: true,
                verticalSwiping: false,
                vertical: false,
                variableWidth: true,
                slidesToShow: 1,
                slidesToScroll: 1,
              }
            }
          ]
        });

        $('.slider-panel.use').slick({
          asNavFor: '.horizontal-slider',
          swipe: false,
          arrows: true,
          fade: true,
          infinite: false,
          slidesToShow: 3,
          slidesToScroll: 1,
          cssEase: 'fast',
          appendArrows: $('.use .slide-controls'),
          nextArrow: "<span class=\"slide-controls__next\"></span>",
          prevArrow: "<span class=\"slide-controls__prev disabled\"></span>",
          responsive: [
            {
              breakpoint: 1024,
              settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
                infinite: true,
                centerMode: true,
              }
            }
          ]
        });
      }

      $('.horizontal-slider').on('wheel', (function(e) {
        e.preventDefault();

        if (e.originalEvent.deltaY > 0) {
          $(this).slick('slickNext');
        } else {
          $(this).slick('slickPrev');
        }
      }));

    },
    slickInitVerticalSlider: function() {
      //если slick активен обновим, что бы пересчиталась высота слайда
      if ( $('.vertical-slider').hasClass('slick-initialized') ) {
        $('.vertical-slider').slick('refresh');
      }
      //если нет активируем
      if ( !$('.vertical-slider').hasClass('slick-initialized') ) {
        $('.vertical-slider').slick({
          swipe: true,
          rows: 0,
          arrows: false,
          infinite: true,
          slidesToShow: 1,
          slidesToScroll: 1,
          centerPadding: `250px`,
          centerMode: true,
          vertical: true,
          verticalSwiping: true,
          asNavFor: '.slider-panel.learn',
          responsive: [
            {
              breakpoint: 1024,
              settings: {
                centerMode: false,
                verticalSwiping: false,
                vertical: false,
                variableWidth: true,
                centerPadding: 0,
              }
            },
            {
              breakpoint: 1439,
              settings: {
                centerPadding: `80px`,
              }
            },
            {
              breakpoint: 1919,
              settings: {
                centerPadding: `110px`,
              }
            }
          ]
        });
        $('.slider-panel.learn').slick({
          asNavFor: '.vertical-slider',
          swipe: false,
          arrows: true,
          slidesToShow: 1,
          slidesToScroll: 1,
          appendArrows: $('.learn .slide-controls'),
          nextArrow: "<span class=\"slide-controls__next\"></span>",
          prevArrow: "<span class=\"slide-controls__prev disabled\"></span>",
          fade: true,
          infinite: true,
          cssEase: 'fast'
        });
      }
      // смена слайда при наведении
      var timer;
      $('.vertical-slider .slick-slide').mouseover(function(event){
        var currentItem = event.target;
        if (!$(currentItem).hasClass('slick-current')) {

          var currentId = currentItem.getAttribute('data-slick-index'),
            currentSlide = $('.vertical-slider').slick('slickCurrentSlide');

          if( currentId > currentSlide) {
            timer = setTimeout(function () {
              $('.vertical-slider').slick('slickNext')
            }, 1000);
          } else {
            timer = setTimeout(function () {
              $('.vertical-slider').slick('slickPrev')
            }, 1000)
          }
        }
      }).mouseleave(function() {
        clearTimeout(timer)
      });

      var interval;
      var x = 2;
      $('.vertical-slider').mouseenter(function() {
        countdown();
      }).mouseleave(function(){
        $('.vertical-slider').off('wheel');
        clearTimeout(interval);
        x = 2;
      });

      function countdown() {
        x--;
        if (x <= 0) {
          $('.vertical-slider').on('wheel', (function(e) {
            e.preventDefault();
            if (e.originalEvent.deltaY > 0) {
              $(this).slick('slickNext');
            } else {
              $(this).slick('slickPrev');
            }
          }));
        } else {
          interval = setTimeout(countdown, 1000);
        }
      }
    },
  })
});
