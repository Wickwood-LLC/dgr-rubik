(function ($) {
  Drupal.behaviors.collapseBlock = {
    attach: function (context, settings) {
    	$('.panelizer-view-mode.node-embedded-to-profile>h3').off("click");				// This is to prevent the yoyo effect wherein the block opens and closes immediately
    	$('.panelizer-view-mode.node-embedded-to-profile>h3').on("click", function() {
    		var $this = $(this);
    		$this.parents('.panelizer-view-mode.node-embedded-to-profile').find('.panel-display').first().slideToggle();
    		$this.toggleClass('open');
		  });
    }
  };

  Drupal.behaviors.stickyHeader = {
    attach: function (context, settings) {
      var stickyTop;
      var headerWidth;
      var headerHeight;
      var windowTop;
      var currentPosition;
      var $header;
      var topSpacing;

      $header = $('#block-panels-mini-header');
      topSpacing = $('#admin-menu').height();
      headerHeight = $header.height();        // gets the height of our header

      $(document).ready(sticky);
      $(window).on("resize mresize", sticky);

      function sticky() {
        headerWidth = $header.width();          // gets the width of the container
        $header.css({
          // width: "initial",
        });
        if ($('sticky-header')) {
          $header.removeClass('sticky-header');
        }
        headerHeight = $header.height();        // gets the height of our header

        stickyTop = $header.offset().top;       // tells how far our target element is from the top of the page
        windowTop = $(window).scrollTop();    // tells how far our screen is currently from the top of the page
        currentPosition = stickyTop - windowTop + headerHeight;    // tells how far our target element is from where our screen is currently
        topSpacing = $('#admin-menu').height();

        $('#header').css({
          "margin-bottom": '0',
        });

        // console.log('Distance from top of page: ' + stickyTop);
        // console.log('Position on load ' + currentPosition);

        if (currentPosition < 0) {   // if target element goes above the screen
          $header.addClass('sticky-header');

          $('#header').css({
            'margin-bottom': headerHeight,
          });
        }
        else {
          $header.removeClass('sticky-header');

          $('#header').css({
            'margin-bottom': '0',
          });
        }

        if ($('#admin-menu').length) {
          if (currentPosition < 0) {   // if target element goes above the screen
            if ($('#navbar-administration').css('display') !== 'none') {  // if navbar administration menu is being used
              $header.css({
                top: $('#navbar-bar').height(),
              });
            }
            else {
              $header.css({
                top: topSpacing,
              });
            }
          }
          else {
            $header.css({
              top: '0',
            });
          }
        }

        // console.log("Top spacing is " + topSpacing);
      }

      $(window).scroll(function(){ // scroll event 
        windowTop = $(window).scrollTop();    // tells how far our screen is currently from the top of the page
        currentPosition = stickyTop - windowTop + headerHeight;    // tells how far our target element is from where our screen is currently

        // console.log('Distance from top of page: ' + stickyTop);
        // console.log('Current position: ' + currentPosition);

        if (currentPosition < 0) {   // if target element goes above the screen
          $header.addClass('sticky-header');

          $('#header').css({
            'margin-bottom': headerHeight,
          })
        }
        else if (currentPosition >= 0) {
          $header.removeClass('sticky-header');

          $('#header').css({
            'margin-bottom': '0',
          })
        }

        if ($('#admin-menu').length) {
          if (currentPosition < 0) {   // if target element goes above the screen
            if ($('#navbar-administration').css('display') !== 'none') {    // if navbar administration menu is being used
              $header.css({
                top: $('#navbar-bar').height(),
              });
            }
            else {
              $header.css({
                top: topSpacing,
              });
            }
          }
          else {
            $header.css({
              top: '0',
            });
          }
        }

        // console.log("Top spacing is " + topSpacing);
      });
    }
  };

  Drupal.behaviors.wrapTitles = {
    attach: function (context, settings) {
      $(window).on("load resize", wrap);

      function wrap() {
        $('.boxed').each(function() {                 // find a boxed element
          if ($(this).find('.pane-title').length) {   // if it has a pane title
            $(this).css({
              paddingTop: $(this).find('.pane-title').height(),   // adjust top padding to accommodate this absolute-positioned pane title and prevent overlapping
            });
          }
        })
      }

      $('body').on("DOMNodeInserted", ".boxed", wrap);  // for dynamically loaded elements (after page load)
    }
  };

  Drupal.behaviors.responsiveEqualHeight = {    
    attach: function (context, settings) {
      equalheight = function(container){

        var currentTallest = 0,
            currentRowStart = 0,
            rowDivs = new Array(),
            $el,
            topPosition = 0;
        $(container).each(function() {

          $el = $(this);
          $($el).height('auto')
          topPostion = $el.position().top;

          if (currentRowStart != topPostion) {
            for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
              rowDivs[currentDiv].height(currentTallest);
            }
            rowDivs.length = 0; // empty the array
            currentRowStart = topPostion;
            currentTallest = $el.height();
            rowDivs.push($el);
          } else {
            rowDivs.push($el);
            currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
          }
          for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
            rowDivs[currentDiv].height(currentTallest);
          }
        });
      }

      $(document).ready(function() {
        if ($(window).width() > 943) {
          equalheight('.featured-display .views-row');
        }
        else {
          $('.featured-display .views-row').css({
            'height': 'auto',
          })
        }
      });

      $(document).on("DOMNodeInserted", function() {
        if ($(window).width() > 943) {
          equalheight('.featured-display .views-row');
        }
        else {
          $('.featured-display .views-row').css({
            'height': 'auto',
          })
        }
      });

      $(window).resize(function(){
        if ($(window).width() > 943) {
          equalheight('.featured-display .views-row');
        }
        else {
          $('.featured-display .views-row').css({
            'height': 'auto',
          })
        }
      });
    }
  };

}(jQuery));
