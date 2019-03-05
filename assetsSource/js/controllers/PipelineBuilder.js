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

        events: {
            'click .JS-PipelineBuilder__AddItem': function() {
                var self = this;
                var template = self.templateHtml
                    .split(self.templateUniqueId)
                    .join(F.uuid.make());

                self.$itemsContainer.append(template);
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
        }
    });
}

runPipelineBuilder(window.FAB);
