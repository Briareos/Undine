//angular.module('undine.dashboard')
//    .directive('gravatarSrc', function (): ng.IDirective {
//        return {
//            restrict: 'A',
//            link: function (scope: ng.IScope, $element: ng.IAugmentedJQuery, attrs: ng.IAttributes): void {
//                /* tslint:disable:no-string-literal */
//                let emailMd5: string = scope.$eval(attrs['gravatarSrc']);
//                let size: number = attrs['gravatarSize'] ? attrs['gravatarSize'] : 120;
//                let rating: string = attrs['gravatarRating'] ? attrs['gravatarRating'] : 'g';
//                let style: string = attrs['gravatarStyle'] ? attrs['gravatarStyle'] : 'retro';
//
//                let url: string = '//gravatar.com/avatar/' + emailMd5 + '?size=' + size + '&rating=' + rating + '&style=' + style;
//                $element.attr('src', url);
//                /* tslint:enable */
//            }
//        };
//    });
