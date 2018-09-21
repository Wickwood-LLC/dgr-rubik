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
        windowTop = document.body.scrollTop    // tells how far our screen is currently from the top of the page
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

  /** Function to recalculate the heights of item elements to make
   * the items in same row to have same height.
   */
  var dgr_equalheight = function(items_selector, parent){
    // Associative array to store items by row.
    var elements_by_row = {};
    // Index items by row
    $(items_selector, parent).each(function() {
      var $el = $(this);
      $($el).height('auto');
      var position = $el.position().top;
      if(!elements_by_row.hasOwnProperty(position)) {
        // Initialize array for the row.
        elements_by_row[position] = [];
      }
      elements_by_row[position].push($el);
    });

    for(var position in elements_by_row) {
      // Find height of tallest item in current row.
      var max_height = 0;
      for (i = 0 ; i < elements_by_row[position].length ; i++) {
        if (max_height < elements_by_row[position][i].height()) {
          max_height = elements_by_row[position][i].height();
        }
      }
      // Make all items in the row to have same height as tallest one.
      for (i = 0 ; i < elements_by_row[position].length ; i++) {
        elements_by_row[position][i].height(max_height);
      }
    }
  };

  var dgr_lazyload_image_timeout;
  /**
   * Make equal height for items within .featured-display container.
   */
  var dgr_perform_equalheight = function() {
    // We avoid frequent recalculation of heights using clearTimeout and setTimeout.
    clearTimeout(dgr_lazyload_image_timeout);
    dgr_lazyload_image_timeout = setTimeout(function(){
      if ($(window).width() > 943) {
        $('.featured-display:visible').each(function() {
          dgr_equalheight('.views-row', $(this));
        });
      }
      else {
        $('.featured-display .views-row').css({
          'height': 'auto',
        })
      }
    }, 500);
  };

  Drupal.behaviors.responsiveEqualHeight = {
    attach: function (context, settings) {
      $(document).ready(function() {
        dgr_perform_equalheight();
      });

      $(document).on("DOMNodeInserted", function() {
        dgr_perform_equalheight();
      });

      $(window).resize(function(){
        dgr_perform_equalheight();
      });
    }
  };

}(jQuery));
