/**
 * Navigation JavaScript
 */
(function() {
  'use strict';

  const DESKTOP_BREAKPOINT = 992;

  function isDesktopView() {
    return window.innerWidth >= DESKTOP_BREAKPOINT;
  }

  /**
   * Dropdown Menu Functionality
   */
  const navigation = document.querySelector('#site-navigation');
  const menuToggle = document.querySelector('.menu-toggle');

  if (!navigation) {
    return;
  }

  const menuItems = navigation.querySelectorAll('.menu-item-has-children');

  function closeSubmenu(item) {
    const button = item.querySelector('.submenu-toggle');
    item.classList.remove('toggled', 'menu-open');
    if (button) {
      button.setAttribute('aria-expanded', 'false');
    }
  }

  function openSubmenu(item) {
    const button = item.querySelector('.submenu-toggle');
    item.classList.add('toggled', 'menu-open');
    if (button) {
      button.setAttribute('aria-expanded', 'true');
    }
  }

  function closeAllSubmenus(exceptItem) {
    menuItems.forEach(function(item) {
      if (exceptItem && item === exceptItem) {
        return;
      }
      closeSubmenu(item);
    });
  }

  menuItems.forEach(function(item) {
    const link = item.querySelector('a');
    const submenu = item.querySelector('.sub-menu');

    if (link && submenu) {
      // Create dropdown toggle button
      let button = item.querySelector('.submenu-toggle');
      if (!button) {
        button = document.createElement('button');
        button.className = 'submenu-toggle';
        button.setAttribute('aria-expanded', 'false');
        button.setAttribute('aria-label', 'Toggle submenu');
        button.innerHTML = '<span class="submenu-icon" aria-hidden="true"></span>';
        link.parentNode.insertBefore(button, link.nextSibling);
      }

      function toggleSubmenu(forceOpen) {
        const isOpen = item.classList.contains('menu-open') || item.classList.contains('toggled');
        const shouldOpen = typeof forceOpen === 'boolean' ? forceOpen : !isOpen;

        if (shouldOpen) {
          closeAllSubmenus(item);
          openSubmenu(item);
        } else {
          closeSubmenu(item);
        }
      }

      button.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleSubmenu();
      });

      link.addEventListener('click', function(e) {
        if (!isDesktopView()) {
          return;
        }

        const href = link.getAttribute('href') || '';
        const isOpen = item.classList.contains('menu-open');

        if (!isOpen) {
          e.preventDefault();
          toggleSubmenu(true);
          return;
        }

        if (href === '#' || href === '') {
          e.preventDefault();
          toggleSubmenu(false);
        }
      });
    }
  });

  /**
   * Close menu when clicking outside
   */
  document.addEventListener('click', function(event) {
    if (!navigation.contains(event.target) && (!menuToggle || !menuToggle.contains(event.target))) {
      navigation.classList.remove('toggled');
      if (menuToggle) {
        menuToggle.setAttribute('aria-expanded', 'false');
      }
      closeAllSubmenus();
    }
  });

  /**
   * Close menu on Escape key
   */
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
      if (navigation.classList.contains('toggled')) {
        navigation.classList.remove('toggled');
      }

      closeAllSubmenus();

      if (menuToggle) {
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.focus();
      }
    }
  });

})();
