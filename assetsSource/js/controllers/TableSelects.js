// Make sure FAB is defined
window.FAB = window.FAB || {};

function runTableSelects(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runTableSelects(F);
        }, 10);
        return;
    }

    F.controller.make('TableSelects', {
        init: function() {
            var self = this;

            F.GlobalModel.onChange('filterHasRun', function() {
                self.setAllTo(false);
            });
        },

        events: {
            'click .JS-TableSelects__All': function(e) {
                var self = this;
                var $target = $(e.currentTarget);

                self.setAllTo($target.get(0).checked);
            },
            'change .JS-TableSelects__ItemSelector': function(e) {
                this.rowSelectResponder($(e.currentTarget));
            },
            'click .JS-TableSelects__SelectRow': function(e) {
                if ($(e.target).hasClass('JS-TableSelects__SelectEscape')) {
                    return;
                }

                this.rowClickResponder($(e.currentTarget));
            }
        },

        setAllTo: function(checked) {
            var self = this;

            self.$el.find('.JS-TableSelects__ItemSelector')
                .prop('checked', checked)
                .trigger('change');

            self.$el.find('.JS-TableSelects__All').prop('checked', checked);
        },

        rowSelectResponder: function($checkbox) {
            var isChecked = $checkbox.get(0).checked;
            var $row = $checkbox.closest('.JS-TableSelects__SelectRow');
            var selectedClass = $row.data('selectedClass');

            if (! selectedClass) {
                return;
            }

            if (isChecked) {
                $row.addClass(selectedClass);
                return;
            }

            $row.removeClass(selectedClass);
        },

        rowClickResponder: function($row) {
            var $checkbox = $row.find('.JS-TableSelects__ItemSelector');
            var isChecked = $checkbox.get(0).checked;

            $checkbox.prop('checked', ! isChecked).trigger('change');
        }
    });
}

runTableSelects(window.FAB);
