/**
 * YonksTEAM — Navigation Toggle
 * 
 * Simple mobile navigation toggle.
 * Toggles a class on the navigation container when the hamburger button is clicked.
 * Handles focus management and escape key for accessibility.
 */

(function () {
  'use strict';

  const initNavToggle = function () {
    const toggleButton = document.querySelector('.nav-toggle');
    const nav = document.querySelector('.site-header .wp-block-navigation');

    if (!toggleButton || !nav) {
      return;
    }

    const toggleNav = function () {
      const isOpen = nav.classList.toggle('is-open');
      toggleButton.setAttribute('aria-expanded', isOpen);

      if (isOpen) {
        // Focus the first link when menu opens
        const firstLink = nav.querySelector('a');
        if (firstLink) {
          firstLink.focus();
        }
      }
    };

    // Click handler
    toggleButton.addEventListener('click', toggleNav);

    // Escape key closes the menu
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && nav.classList.contains('is-open')) {
        nav.classList.remove('is-open');
        toggleButton.setAttribute('aria-expanded', 'false');
        toggleButton.focus();
      }
    });

    // Close menu when clicking outside
    document.addEventListener('click', function (e) {
      if (
        nav.classList.contains('is-open') &&
        !nav.contains(e.target) &&
        !toggleButton.contains(e.target)
      ) {
        nav.classList.remove('is-open');
        toggleButton.setAttribute('aria-expanded', 'false');
      }
    });

    // Close menu on window resize above breakpoint
    let resizeTimer;
    window.addEventListener('resize', function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () {
        if (window.innerWidth > 768 && nav.classList.contains('is-open')) {
          nav.classList.remove('is-open');
          toggleButton.setAttribute('aria-expanded', 'false');
        }
      }, 250);
    });
  };

  // Run on DOMContentLoaded
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNavToggle);
  } else {
    initNavToggle();
  }
})();