angular.module('undine.dashboard')
    .directive('gravatarSrc', function () {
        return {
            restrict: 'A',
            link: function (scope:ng.IScope, $element:ng.IAugmentedJQuery, attrs:ng.IAttributes) {
                var emailMd5 = scope.$eval(attrs['gravatarSrc']);
                var size = attrs['gravatarSize'] ? attrs['gravatarSize'] : 120;
                var rating = attrs['gravatarRating'] ? attrs['gravatarRating'] : 'g';
                var style = attrs['gravatarStyle'] ? attrs['gravatarStyle'] : 'retro';

                var url = '//gravatar.com/avatar/' + emailMd5 + '?size=' + size + '&rating=' + rating + '&style=' + style;
                $element.attr('src', url);
            }
        }
    });