angular.module('undine.dashboard')
    .factory('$xhrFactory', function () {
        return function createXhr() {
            var deferred = DeferredStack.pop();
            var xhr = new XMLHttpRequest();

            if (!deferred) {
                // The stack is empty; this call is not "progressable".
                return xhr;
            }

            var read = 0;
            var response;

            var buffer = '';
            var delimiterIndex = -1;
            var progress;
            var rawProgress;
            var data;

            xhr.onreadystatechange = function () {
                response = ('response' in xhr) ? xhr.response : xhr.responseText;
                if (xhr.readyState === 3) {
                    data = response.substring(read);
                    buffer += data;
                    while ((delimiterIndex = buffer.indexOf("\n")) !== -1) {
                        rawProgress = buffer.substring(0, delimiterIndex + 1);
                        buffer = buffer.substring(delimiterIndex + 1);
                        progress = JSON.parse(rawProgress);

                        deferred.notify(progress);
                    }
                }
                read = response.length;
            };

            return xhr;
        };
    });
