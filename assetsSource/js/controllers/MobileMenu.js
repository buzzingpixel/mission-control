// Make sure FAB is defined
window.FAB = window.FAB || {};

function runMobileMenu(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runMobileMenu(F);
        }, 10);
        return;
    }

    F.controller.make('MobileMenu', {
        model: {
            isActive: 'bool'
        },

        isMobile: true,

        events: {
            'click .JS-MobileMenuActivator': function(e) {
                var self = this;

                e.preventDefault();

                self.model.set('isActive', ! self.model.get('isActive'));
            }
        },

        init: function() {
            var self = this;

            self.model.onChange('isActive', function() {
                self.menuResponder();
            });

            self.resizeResponder();

            F.GlobalModel.onChange('windowWidth', function(windowWidth) {
                self.resizeResponder(windowWidth);
            });
        },

        resizeResponder: function(windowWidth) {
            var self = this;
            var modeIsMobile = self.isMobile;
            var windowIsDesktop = windowWidth >= F.GlobalModel.get(
                'menuBreakPoint'
            );

            if (windowIsDesktop && modeIsMobile) {
                self.model.set('isActive', false);
                self.deactivateMenu();
            }

            self.isMobile = ! windowIsDesktop;
        },

        menuResponder: function() {
            var self = this;
            var state = self.model.get('isActive');
            var desktopBreakPoint = F.GlobalModel.get('menuBreakPoint');

            if (window.innerWidth >= desktopBreakPoint) {
                if (self.model.get('isActive')) {
                    self.deactivateMenu();
                }

                return;
            }

            if (state) {
                self.activateMenu();
                return;
            }

            self.deactivateMenu();
        },

        activateMenu: function() {
            var self = this;

            $('.JS-MobileMenuActivator').each(function() {
                var $el = $(this);
                var activeClass = $el.data('activeClass');

                if (! activeClass) {
                    return;
                }

                $el.addClass(activeClass);
            });

            $('.JS-MobileNav').each(function() {
                var $el = $(this);
                var preActiveClass = $el.data('preActiveClass');
                var activeClass = $el.data('activeClass');

                $el.addClass(preActiveClass);

                setTimeout(function() {
                    $el.addClass(activeClass);
                }, 20);
            });

            $('body').on('keyup.MobileMenu', function(e) {
                if (e.keyCode !== 27) {
                    return;
                }

                self.model.set('isActive', false);
            });
        },

        deactivateMenu: function() {
            $('.JS-MobileMenuActivator').each(function() {
                var $el = $(this);
                var activeClass = $el.data('activeClass');

                if (! activeClass) {
                    return;
                }

                $el.removeClass(activeClass);
            });

            $('.JS-MobileNav').each(function() {
                var $el = $(this);
                var preActiveClass = $el.data('preActiveClass');
                var activeClass = $el.data('activeClass');

                $el.removeClass(activeClass);

                setTimeout(function() {
                    $el.removeClass(preActiveClass);
                }, 200);
            });

            $('body').off('keyup.MobileMenu');
        }
    });
}

runMobileMenu(window.FAB);
