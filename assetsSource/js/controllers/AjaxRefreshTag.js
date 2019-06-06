// Make sure FAB is defined
window.FAB = window.FAB || {};

function runAjaxRefreshTag(F) {
    'use strict';

    if (! window.jQuery || ! F.controller || ! F.model) {
        setTimeout(function() {
            runAjaxRefreshTag(F);
        }, 10);
        return;
    }

    F.controller.make('AjaxRefreshTag', {
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
                cache: false,
                success: function(html) {
                    var $html = $(html);
                    var $el = $html.find('.' + self.$el.data('ajaxUniqueClass'));
                    var refreshUrl = $el.data('ajaxRefreshUrl');
                    var classes = $el.attr('class');

                    self.$el.html($el.html());

                    if (classes) {
                        self.$el.attr('class', '');

                        $el.attr('class').split(' ').forEach(function(classString) {
                            self.$el.addClass(classString);
                        });
                    }

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

runAjaxRefreshTag(window.FAB);
