// Make sure FAB is defined
window.FAB = window.FAB || {};

function runCodeEditor(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runCodeEditor(F);
        }, 10);
        return;
    }

    F.controller.make('CodeEditor', {
        init: function() {
            this.setUpCodeMirror();
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
            window.CodeMirror.fromTextArea(this.$el.get(0), {
                lineNumbers: true,
                indentUnit: 4,
                lineWrapping: true,
                mode: 'text/x-sh',
                indentWithTabs: false
            });
        }
    });
}

runCodeEditor(window.FAB);
