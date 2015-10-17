angular.module('undine.dashboard')
    .filter('stripProtocol', function (): Function {
        return function (url: string): string {
            if (url) {
                return url.replace(/^http(s)?:\/\/(www\.)?/, '').replace(/\/$/, '');
            } else {
                return '';
            }
        };
    });
