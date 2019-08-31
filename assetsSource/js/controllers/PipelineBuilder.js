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
                var codeEditor = $template.find('.JS-PipelineBuilder__CodeEditor').get(0);
                var select = $template.find('.JS-PipelineBuilder__ServerSelect').get(0);

                self.$itemsContainer.append($template);

                self.sorter.addItems($template);

                if (codeEditor) {
                    self.elToCodeMirror(codeEditor);
                }

                if (select) {
                    F.controller.construct('Select', {
                        el: select
                    });
                }
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

            self.setUpCodeMirror();

            self.$el.find('.JS-PipelineBuilder__ServerSelect').each(function() {
                F.controller.construct('Select', {
                    el: this
                });
            });
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
            var $existingItems = self.$el.find('.JS-PipelineBuilder__Area');

            self.sorter = new window.Garnish.DragSort({
                container: self.$itemsContainer,
                handle: '.JS-PipelineBuilder__Dragger',
                axis: window.Garnish.Y_AXIS,
                collapseDraggees: false,
                magnetStrength: 4,
                helperLagBase: 1.5,
                helperOpacity: 0.6
            });

            $existingItems.detach();

            $existingItems.each(function() {
                var $item = $(this);

                self.$itemsContainer.append($item);

                self.sorter.addItems($item);
            });
        },

        setUpCodeMirror: function() {
            var self = this;

            if (! F.GlobalModel.get('codeMirrorIsLoading')) {
                self.loadCodeMirror();
            }

            if (! F.GlobalModel.get('codeMirrorHasLoaded')) {
                setTimeout(function() {
                    self.setUpCodeMirror();
                }, 10);

                return;
            }

            self.setUpCodeMirrorReal();
        },

        loadCodeMirror: function() {
            F.GlobalModel.set('codeMirrorIsLoading', true);

            F.assets.load({
                root: 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.44.0/',
                css: 'codemirror.min.css'
            });

            F.assets.load({
                root: 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.44.0/',
                js: [
                    'codemirror.min.js',
                    'mode/shell/shell.min.js'
                ],
                success: function() {
                    F.GlobalModel.set('codeMirrorHasLoaded', true);
                }
            });
        },

        setUpCodeMirrorReal: function() {
            var self = this;

            self.$el.find('.JS-PipelineBuilder__CodeEditor').each(function() {
                self.elToCodeMirror(this);
            });
        },

        elToCodeMirror: function(el) {
            window.CodeMirror.fromTextArea(el, {
                lineNumbers: true,
                indentUnit: 4,
                lineWrapping: true,
                mode: 'text/x-sh',
                indentWithTabs: false
            });
        }
    });
}

runPipelineBuilder(window.FAB);
