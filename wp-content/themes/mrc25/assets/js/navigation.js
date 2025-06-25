/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function() {
    const siteNavigation = document.getElementById( 'site-navigation' );

    // Return early if the navigation doesn't exist.
    if ( ! siteNavigation ) {
        return;
    }

    const button = siteNavigation.getElementsByTagName( 'button' )[ 0 ];

    // Return early if the button doesn't exist.
    if ( 'undefined' === typeof button ) {
        return;
    }

    const menu = siteNavigation.getElementsByTagName( 'ul' )[ 0 ];

    // Hide menu toggle button if menu is empty and return early.
    if ( 'undefined' === typeof menu ) {
        button.style.display = 'none';
        return;
    }

    if ( ! menu.classList.contains( 'nav-menu' ) ) {
        menu.classList.add( 'nav-menu' );
    }

    // Toggle the .toggled class and the aria-expanded value each time the button is clicked.
    button.addEventListener( 'click', function() {
        siteNavigation.classList.toggle( 'toggled' );

        if ( button.getAttribute( 'aria-expanded' ) === 'true' ) {
            button.setAttribute( 'aria-expanded', 'false' );
        } else {
            button.setAttribute( 'aria-expanded', 'true' );
        }
    } );

    // Remove the .toggled class and set aria-expanded to false when the user clicks outside the navigation.
    document.addEventListener( 'click', function( event ) {
        const isClickInside = siteNavigation.contains( event.target );

        if ( ! isClickInside ) {
            siteNavigation.classList.remove( 'toggled' );
            button.setAttribute( 'aria-expanded', 'false' );
        }
    } );

    // Get all the link elements within the menu.
    const links = menu.getElementsByTagName( 'a' );

    // Get all the link elements with children within the menu.
    const linksWithChildren = menu.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

    // Toggle focus each time a menu link is focused or blurred.
    for ( const link of links ) {
        link.addEventListener( 'focus', toggleFocus, true );
        link.addEventListener( 'blur', toggleFocus, true );
    }

    // Toggle focus each time a menu link with children receive a touch event.
    for ( const link of linksWithChildren ) {
        link.addEventListener( 'touchstart', toggleFocus, false );
    }

    /**
     * Sets or removes .focus class on an element.
     */
    function toggleFocus() {
        if ( event.type === 'focus' || event.type === 'blur' ) {
            let self = this;
            // Move up through the ancestors of the current link until we hit .nav-menu.
            while ( ! self.classList.contains( 'nav-menu' ) ) {
                // On li elements toggle the class .focus.
                if ( 'li' === self.tagName.toLowerCase() ) {
                    self.classList.toggle( 'focus' );
                }
                self = self.parentNode;
            }
        }

        if ( event.type === 'touchstart' ) {
            const menuItem = this.parentNode;
            event.preventDefault();
            for ( const link of menuItem.parentNode.children ) {
                if ( menuItem !== link ) {
                    link.classList.remove( 'focus' );
                }
            }
            menuItem.classList.toggle( 'focus' );
        }
    }
} )();

document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const overlay = document.getElementById('mobile-menu-overlay');
    const focusableSelectors = 'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])';
    let lastFocusedElement = null;

    function openMenu() {
        mobileMenu.classList.add('open');
        overlay.classList.add('open');
        menuToggle.setAttribute('aria-expanded', 'true');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        // Focus primo link
        const firstLink = mobileMenu.querySelector(focusableSelectors);
        if (firstLink) firstLink.focus();
    }

    function closeMenu() {
        mobileMenu.classList.remove('open');
        overlay.classList.remove('open');
        menuToggle.setAttribute('aria-expanded', 'false');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (lastFocusedElement) lastFocusedElement.focus();
    }

    menuToggle.addEventListener('click', function (e) {
        e.stopPropagation();
        if (mobileMenu.classList.contains('open')) {
            closeMenu();
        } else {
            lastFocusedElement = document.activeElement;
            openMenu();
        }
    });

    overlay.addEventListener('click', function () {
        closeMenu();
    });

    // Chiudi menu cliccando su una voce
    mobileMenu.addEventListener('click', function (e) {
        if (e.target.tagName === 'A') {
            closeMenu();
        }
    });

    // ESC per chiudere
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && mobileMenu.classList.contains('open')) {
            closeMenu();
        }
    });

    // Gestione focus trap
    mobileMenu.addEventListener('keydown', function (e) {
        if (!mobileMenu.classList.contains('open')) return;
        if (e.key !== 'Tab') return;
        const focusableEls = mobileMenu.querySelectorAll(focusableSelectors);
        if (!focusableEls.length) return;
        const firstEl = focusableEls[0];
        const lastEl = focusableEls[focusableEls.length - 1];
        if (e.shiftKey) {
            if (document.activeElement === firstEl) {
                e.preventDefault();
                lastEl.focus();
            }
        } else {
            if (document.activeElement === lastEl) {
                e.preventDefault();
                firstEl.focus();
            }
        }
    });
}); 