// Make sure FAB is defined
window.FAB = window.FAB || {};

function runAjaxRefreshInner(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runAjaxRefreshInner(F);
        }, 10);
        return;
    }

    F.controller.make('AjaxRefreshInner', {
        runsSinceNoUrl: 0,

        init: function() {
            var self = this;
            var url = self.$el.data('ajaxRefreshUrl');

            if (! url) {
                return;
            }

            setTimeout(function() {
                self.runRefresh(url);
            }, 1000);
        },

        runRefresh: function(url) {
            var self = this;

            $.ajax({
                url: url,
                success: function(html) {
                    var $html = $(html);
                    var $el = $html.find('.JS-AjaxRefreshInner');
                    var refreshUrl = $el.data('ajaxRefreshUrl');

                    self.$el.html($el.html());

                    if (! $el.data('ajaxRefreshUrl')) {
                        if (self.runsSinceNoUrl > 1) {
                            return;
                        }

                        self.runsSinceNoUrl = self.runsSinceNoUrl + 1;

                        setTimeout(function() {
                            self.runRefresh(url);
                        }, 1000);

                        return;
                    }

                    setTimeout(function() {
                        self.runRefresh(refreshUrl);
                    }, 1000);
                }
            });
        }
    });
}

runAjaxRefreshInner(window.FAB);
