(function (window, undefined) {
  'use strict';

  // Wait for DOM to be ready
  $(document).ready(function () {

    console.log('Custom menu script loaded');
    console.log('$.app exists:', typeof $.app !== 'undefined');

    // Use native addEventListener with capture phase to ensure our handler runs first
    var menuToggles = document.querySelectorAll('.menu-toggle, .modern-nav-toggle');

    menuToggles.forEach(function (toggle) {
      // Remove any existing listeners
      toggle.addEventListener('click', function (e) {
        // Prevent default action immediately
        e.preventDefault();
        e.stopPropagation();

        var body = document.body;
        var isMobile = window.innerWidth < 1200;

        console.log('Menu toggle clicked. Mobile:', isMobile);

        // Manual toggle primarily for mobile or if app.menu fails
        if (isMobile) {
          if (body.classList.contains('menu-open')) {
            body.classList.remove('menu-open');
            body.classList.add('menu-hide');
            // Remove overlay if present
            $('.sidenav-overlay').removeClass('d-block').addClass('d-none');
          } else {
            body.classList.remove('menu-hide');
            body.classList.add('menu-open');
            // Show overlay
            $('.sidenav-overlay').removeClass('d-none').addClass('d-block');
          }
        } else {
          // Desktop fallback to original if available
          if (typeof $.app !== 'undefined' && $.app && $.app.menu) {
            $.app.menu.toggle();
          }
        }

        return false;
      }, true); // Use capture phase
    });

    // Also check after page load
    setTimeout(function () {
      var newToggles = document.querySelectorAll('.menu-toggle, .modern-nav-toggle');
      console.log('Found ' + newToggles.length + ' menu toggles');

      newToggles.forEach(function (toggle, index) {
        console.log('Toggle ' + index + ':', toggle);
      });
    }, 1000);

  });

  // Also run on window load as backup
  $(window).on('load', function () {

    console.log('Window loaded');

    // Initialize or reinitialize the menu
    if (typeof $.app !== 'undefined' && $.app.menu) {

      console.log('Initializing menu on window load...');

      // Update scrollbar
      if ($.app.menu.manualScroller) {
        $.app.menu.manualScroller.updateHeight();
        console.log('Scrollbar updated');
      }

      // Handle click on sidenav overlay
      $('.sidenav-overlay').off('click.customOverlay').on('click.customOverlay', function (e) {
        console.log('Overlay clicked');
        var body = document.body;

        // Manual hide for mobile
        if (body.classList.contains('menu-open')) {
          body.classList.remove('menu-open');
          body.classList.add('menu-hide');
          $(this).removeClass('d-block').addClass('d-none');
        }

        // Also call original app menu hide if available
        if (typeof $.app !== 'undefined' && $.app && $.app.menu) {
          $.app.menu.hide();
        }
      });

      // Update scrollbar on window resize
      $(window).off('resize.customResize').on('resize.customResize', function () {
        if ($.app && $.app.menu && $.app.menu.manualScroller) {
          $.app.menu.manualScroller.updateHeight();
        }
      });

      console.log('Menu handlers attached');
    } else {
      console.error('$.app.menu not available on window load!');
    }

  });

})(window);