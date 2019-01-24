/**
 * @see https://flatpickr.js.org/
 */

// Make sure FAB is defined
window.FAB = window.FAB || {};

function runFlatPicker(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runFlatPicker(F);
        }, 10);
        return;
    }

    F.controller.make('FlatPicker', {
        init: function() {
            var self = this;

            if (! F.GlobalModel.get('flatPickerIsLoading')) {
                self.load();
            }

            if (! F.GlobalModel.get('flackPickerHasLoaded')) {
                setTimeout(function() {
                    self.init();
                }, 10);

                return;
            }

            self.initReal();
        },

        load: function() {
            F.GlobalModel.set('flatPickerIsLoading', true);

            F.assets.load({
                root: 'https://cdn.jsdelivr.net',
                css: '/npm/flatpickr/dist/flatpickr.min.css'
            });

            F.assets.load({
                root: 'https://cdn.jsdelivr.net',
                js: '/npm/flatpickr',
                success: function() {
                    F.GlobalModel.set('flackPickerHasLoaded', true);
                }
            });
        },

        initReal: function() {
            var self = this;

            window.flatpickr(self.el, {
                enableTime: false,
                dateFormat: 'Y-m-d'
            });
        }
    });
}

runFlatPicker(window.FAB);
