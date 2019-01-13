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

    console.log('main');
}

runMain(window.FAB, window);
