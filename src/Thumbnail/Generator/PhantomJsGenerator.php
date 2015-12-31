<?php

namespace Undine\Thumbnail\Generator;

use Undine\Thumbnail\CaptureConfiguration;
use Undine\Thumbnail\ThumbnailConfiguration;

class PhantomJsGenerator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(CaptureConfiguration $configuration)
    {
        $options = [
            'height' => $configuration->getCaptureHeight(),
            'width'  => $configuration->getCaptureWidth(),
        ];

        $js = <<<JS
var page = require('webpage').create(),
    system = require('system');

var size = {
    width: 1600,
    height: 889
};

if (system.args.length < 3 || system.args.length > 6) {
    console.log('Usage: phantomjs-screenshot URL filename [width x height] [httpUsername] [httpPassword]');
    console.log(' size is specified in pixels (ex. 1600x889)');
    phantom.exit(1);
} else {
    address = system.args[1];
    output = system.args[2];
    if (system.args[3]) {
        var passedSize = system.args[3].split('x');
        size.width = parseInt(passedSize[0], 10);
        size.height = parseInt(passedSize[1], 10);
    }

    if (system.args[4] && system.args[5]) {
        page.settings.userName = system.args[4];
        page.settings.password = system.args[5];
    }

    page.viewportSize = size;
    page.clipRect = {top: 0, left: 0, width: size.width, height: size.height};

    page.customHeaders = {
        // Send a "Do Not Track" header.
        "DNT": "1"
    };

    page.onResourceError = function (resourceError) {
        console.log('Unable to load resource ' + resourceError.url + '. Error (' + resourceError.errorCode + '): ' + resourceError.errorString);
    };

    page.onResourceRequested = function (requestData, request) {
        // Block access to [www.]google-analytics.com hostname.
        if (requestData['url'].match(/^https?:\/\/(www\.)?google-analytics\.com($|\/)/)) {
            request.abort();
        }
    };

    page.open(address, function (status) {
        // status is unreliable!
        // If a resource fails downloading (even though it does not affect the page at all), status is empty (not even an error)
        window.setTimeout(function () {
            try {
                page.evaluate(function (size) {
                    // Handle transparent background by setting it to white
                    var backgroundColor = window.getComputedStyle(document.body).backgroundColor;
                    var matches = backgroundColor.match(/rgba\(\d+, \d+, \d+, (\d+)\)$/);
                    if (matches !== null && matches[1] === "0") {
                        document.body.style.backgroundColor = "white";
                    }

                    // Workaround for websites having `height: 100%`
                    var computedHeight = window.getComputedStyle(document.body).height;
                    if (computedHeight === size.height + "px") {
                        // Computed height is equal to the viewport height when height is 100%
                        document.body.style.width = size.width + "px";
                        document.body.style.height = size.height + "px";
                    }
                }, size);
                page.render(output, {quality: 90});
                phantom.exit();
            } catch (e) {
                console.log(e);
                phantom.exit(2);
            }
        }, 1500);
    });
}
JS;

    }
}
