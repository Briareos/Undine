angular.module('undine.dashboard')
    .filter('stripProtocol', function () {
        return function (url) {
            if (url) {
                return url.replace(/^http(s)?:\/\/(www\.)?/, '').replace(/\/$/, '');
            } else {
                return '';
            }
        };
    });
