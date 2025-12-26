/**
 * Navigation JavaScript
 */
(function() {
  'use strict';

  /**
   * Dropdown Menu Functionality
   */
  const menuItems = document.querySelectorAll('.menu-item-has-children');

  menuItems.forEach(function(item) {
    const link = item.querySelector('a');
    const submenu = item.querySelector('.sub-menu');

    if (link && submenu) {
      // Create dropdown toggle button
      const button = document.createElement('button');
      button.className = 'submenu-toggle';
      button.setAttribute('aria-expanded', 'false');
      button.setAttribute('aria-label', 'Toggle submenu');
      button.innerHTML = '<span class="submenu-icon">+</span>';

      link.parentNode.insertBefore(button, link.nextSibling);

      button.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const expanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', !expanded);
        item.classList.toggle('toggled');

        // Update icon
        const icon = this.querySelector('.submenu-icon');
        icon.textContent = expanded ? '+' : '−';
      });
    }
  });

  /**
   * Close menu when clicking outside
   */
  document.addEventListener('click', function(event) {
    const navigation = document.querySelector('#site-navigation');
    const menuToggle = document.querySelector('.menu-toggle');

    if (!navigation || !menuToggle) return;

    if (!navigation.contains(event.target) && !menuToggle.contains(event.target)) {
      navigation.classList.remove('toggled');
      menuToggle.setAttribute('aria-expanded', 'false');
    }
  });

  /**
   * Close menu on Escape key
   */
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
      const navigation = document.querySelector('#site-navigation');
      const menuToggle = document.querySelector('.menu-toggle');

      if (navigation && navigation.classList.contains('toggled')) {
        navigation.classList.remove('toggled');
        if (menuToggle) {
          menuToggle.setAttribute('aria-expanded', 'false');
          menuToggle.focus();
        }
      }
    }
  });

})();
