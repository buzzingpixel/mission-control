// Make sure FAB is defined
window.FAB = window.FAB || {};

function runMain(F, W) {
    'use strict';

    if (! window.jQuery ||
        ! F.controller ||
        ! F.model
    ) {
        setTimeout(function() {
            runMain(F, W);
        }, 1);
        return;
    }

    var GlobalModelConstructor = F.model.make({
        menuBreakPoint: 'int',
        windowWidth: 'int',
        filterHasRun: 'int',
        selectIsLoading: 'bool',
        selectHasLoaded: 'bool',
        flatPickerIsLoading: 'bool',
        flackPickerHasLoaded: 'bool',
        garnishIsLoading: 'bool',
        garnishHasLoaded: 'bool',
        codeMirrorIsLoading: 'bool',
        codeMirrorHasLoaded: 'bool'
    });

    F.GlobalModel = new GlobalModelConstructor({
        menuBreakPoint: 1000
    });

    F.GlobalModel.set('windowWidth', W.innerWidth);

    $(W).on('resize.GlobalModel', function() {
        F.GlobalModel.set('windowWidth', W.innerWidth);
    });

    F.controller.construct('MobileMenu', {
        el: 'body'
    });

    $('.JS-TableSelects').each(function() {
        F.controller.construct('TableSelects', {
            el: this
        });
    });

    $('.JS-Filterable').each(function() {
        F.controller.construct('Filterable', {
            el: this
        });
    });

    $('.JS-Select').each(function() {
        F.controller.construct('Select', {
            el: this
        });
    });

    $('.JS-FlatPicker').each(function() {
        F.controller.construct('FlatPicker', {
            el: this
        });
    });

    $('.JS-PipelineBuilder').each(function() {
        F.controller.construct('PipelineBuilder', {
            el: this
        });
    });

    $('.JS-CodeEditor').each(function() {
        F.controller.construct('CodeEditor', {
            el: this
        });
    });

    $('.JS-AjaxRefreshInner').each(function() {
        F.controller.construct('AjaxRefreshInner', {
            el: this
        });
    });

    $('.JS-AjaxRefreshTag').each(function() {
        F.controller.construct('AjaxRefreshTag', {
            el: this
        });
    });
}

runMain(window.FAB, window);
