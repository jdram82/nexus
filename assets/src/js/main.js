// Import SCSS
import '../scss/main.scss';

/**
 * Main JavaScript File
 */
(function() {
  'use strict';

  /**
   * Mobile Menu Toggle
   */
  const menuToggle = document.querySelector('.menu-toggle');
  const navigation = document.querySelector('#site-navigation');

  if (menuToggle && navigation) {
    menuToggle.addEventListener('click', function() {
      const expanded = this.getAttribute('aria-expanded') === 'true' || false;
      this.setAttribute('aria-expanded', !expanded);
      navigation.classList.toggle('toggled');
    });

    const resetMenuForDesktop = function() {
      if (window.innerWidth >= 992) {
        navigation.classList.remove('toggled');
        menuToggle.setAttribute('aria-expanded', 'false');
      }
    };

    resetMenuForDesktop();
    window.addEventListener('resize', resetMenuForDesktop);
  }

  /**
   * Smooth Scroll for Anchor Links
   */
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (targetId === '#') return;

      const target = document.querySelector(targetId);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });

  /**
   * Sticky Header on Scroll
   */
  let lastScroll = 0;
  const header = document.querySelector('#masthead');

  window.addEventListener('scroll', function() {
    const currentScroll = window.pageYOffset;

    if (currentScroll > 100) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }

    lastScroll = currentScroll;
  });

  /**
   * Back to Top Button
   */
  const backToTop = document.createElement('button');
  backToTop.className = 'back-to-top';
  backToTop.innerHTML = '↑';
  backToTop.setAttribute('aria-label', 'Back to top');
  document.body.appendChild(backToTop);

  window.addEventListener('scroll', function() {
    if (window.pageYOffset > 300) {
      backToTop.classList.add('visible');
    } else {
      backToTop.classList.remove('visible');
    }
  });

  backToTop.addEventListener('click', function() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

})();
