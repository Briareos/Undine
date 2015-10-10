//angular.module('undine.dashboard', [])
//    .factory('$xhrFactory', function () {
//        return function createXhr(method:string, url:string) {
//            var xhr = new XMLHttpRequest();
//            var read = 0;
//            var response;
//
//            var buffer = '';
//            var delimiterIndex = -1;
//            var progress;
//            var rawProgress;
//            var data;
//
//            xhr.onreadystatechange = function () {
//                response = ('response' in xhr) ? xhr.response : xhr.responseText;
//                if (xhr.readyState === 3) {
//                    data = response.substring(read);
//
//                    buffer += data;
//                    while ((delimiterIndex = buffer.indexOf("\n")) !== -1) {
//                        rawProgress = buffer.substring(0, delimiterIndex + 1);
//                        buffer = buffer.substring(delimiterIndex + 1);
//                        progress = JSON.parse(rawProgress);
//
//                        deferred.notify(progress);
//                    }
//                }
//                read = response.length;
//            };
//
//            return xhr;
//        };
//    });
