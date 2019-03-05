/**
 * @see https://flatpickr.js.org/
 */

// Make sure FAB is defined
window.FAB = window.FAB || {};

function runPipelineBuilder(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runPipelineBuilder(F);
        }, 10);
        return;
    }

    F.controller.make('PipelineBuilder', {
        templateUniqueId: null,
        templateHtml: null,
        $itemsContainer: null,
        sorter: null,

        events: {
            'click .JS-PipelineBuilder__AddItem': function() {
                var self = this;
                var template = self.templateHtml
                    .split(self.templateUniqueId)
                    .join(F.uuid.make());
                var $template = $(template);

                self.$itemsContainer.append($template);

                self.sorter.addItems($template);
            },
            'click .JS-PipelineBuilder__AreaRemove': function(e) {
                var $el = $(e.currentTarget);
                var $item = $el.closest('.JS-PipelineBuilder__Area');

                $item.remove();
            }
        },

        init: function() {
            var self = this;

            self.$itemsContainer = self.$el.find('.JS-PipelineBuilder__Items');

            self.templateHtml = self.$el
                .find('.JS-PipelineBuilder__InputTemplate')
                .html();

            self.templateUniqueId = $(self.templateHtml).data('uniqueId');

            self.setUpSorting();
        },

        setUpSorting: function() {
            var self = this;

            if (! F.GlobalModel.get('garnishIsLoading')) {
                self.loadGarnish();
            }

            if (! F.GlobalModel.get('garnishHasLoaded')) {
                setTimeout(function() {
                    self.setUpSorting();
                }, 10);

                return;
            }

            self.setUpSortingReal();
        },

        loadGarnish: function() {
            F.GlobalModel.set('garnishIsLoading', true);

            F.assets.load({
                root: '/',
                js: [
                    'assets/node_modules/velocity-animate/velocity.min.js',
                    'assets/node_modules/garnishjs/dist/garnish.min.js'
                ],
                success: function() {
                    F.GlobalModel.set('garnishHasLoaded', true);
                }
            });
        },

        setUpSortingReal: function() {
            var self = this;

            self.sorter = new window.Garnish.DragSort({
                container: self.$itemsContainer,
                handle: '.JS-PipelineBuilder__Dragger',
                axis: window.Garnish.Y_AXIS,
                collapseDraggees: true,
                magnetStrength: 4,
                helperLagBase: 1.5,
                helperOpacity: 0.6
            });

            self.sorter.addItems(self.$el.find('.JS-PipelineBuilder__Area'));
        }
    });
}

runPipelineBuilder(window.FAB);
