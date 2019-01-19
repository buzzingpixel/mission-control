/**
 * @see https://github.com/jshjohnson/Choices
 * @see https://joshuajohnson.co.uk/Choices/
 */

// Make sure FAB is defined
window.FAB = window.FAB || {};

function runSelect(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runSelect(F);
        }, 10);
        return;
    }

    F.controller.make('Select', {
        init: function() {
            var self = this;

            if (! F.GlobalModel.get('selectIsLoading')) {
                self.loadSelect();
            }

            if (! F.GlobalModel.get('selectHasLoaded')) {
                setTimeout(function() {
                    self.init();
                }, 10);

                return;
            }

            self.initReal();
        },

        loadSelect: function() {
            F.GlobalModel.set('selectIsLoading', true);

            F.assets.load({
                root: '/',
                js: 'assets/node_modules/choices.js/public/assets/scripts/choices.min.js',
                css: 'assets/node_modules/choices.js/public/assets/styles/choices.min.css',
                success: function() {
                    F.GlobalModel.set('selectHasLoaded', true);
                }
            });
        },

        initReal: function() {
            var self = this;

            console.log(self.el);

            new window.Choices(self.el, {
                searchResultLimit: 8,
                shouldSort: false
            });
        }
    });
}

runSelect(window.FAB);
