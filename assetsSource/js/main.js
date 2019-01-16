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
        windowWidth: 'int'
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
}

runMain(window.FAB, window);
