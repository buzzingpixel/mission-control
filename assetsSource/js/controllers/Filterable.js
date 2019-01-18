// Make sure FAB is defined
window.FAB = window.FAB || {};

function runFilterable(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runFilterable(F);
        }, 10);
        return;
    }

    F.controller.make('Filterable', {
        $itemsContainer: null,
        $activeItems: null,
        $allItems: null,
        $searchInput: null,
        searchTimer: 0,

        init: function() {
            var self = this;

            self.$itemsContainer = self.$el.find(
                '.JS-Filterable__ItemsContainer'
            );

            self.$activeItems = self.$el.find('.JS-Filterable__Items');

            self.$allItems = self.$activeItems.clone();

            self.$searchInput = self.$el.find('.JS-Filterable__FilterInput');

            self.model.onChange('activeSearchString', function() {
                self.searchStringResponder();
            });
        },

        model: {
            activeSearchString: 'string'
        },

        events: {
            'change .JS-Filterable__FilterInput': function(e) {
                this.model.set(
                    'activeSearchString',
                    e.currentTarget.value.toLowerCase()
                );
            },
            'keyup .JS-Filterable__FilterInput': function(e) {
                this.model.set(
                    'activeSearchString',
                    e.currentTarget.value.toLowerCase()
                );
            },
            'keydown .JS-Filterable__FilterInput': function(e) {
                if (e.keyCode === 13) {
                    e.preventDefault();
                }
            }
        },

        searchStringResponder: function() {
            var self = this;

            clearTimeout(self.searchTimer);

            self.searchTimer = setTimeout(function() {
                self.runSearch();
            }, 200);
        },

        runSearch: function() {
            var self = this;
            var searchString = self.model.get('activeSearchString');
            var $filteredEls = self.$allItems.clone();
            var $intermediateEls;

            if (searchString) {
                $intermediateEls = $();

                $filteredEls.each(function() {
                    var $el = $(this);

                    if ($el.text().toLowerCase().indexOf(searchString) < 0) {
                        return;
                    }

                    $intermediateEls = $intermediateEls.add($el);
                });

                $filteredEls = $intermediateEls;
            }

            self.$activeItems.remove();
            self.$activeItems = $filteredEls;
            self.$itemsContainer.append(self.$activeItems);

            F.GlobalModel.set(
                'filterHasRun',
                F.GlobalModel.get('filterHasRun') + 1
            );
        }
    });
}

runFilterable(window.FAB);
