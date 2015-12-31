var page = require('webpage').create(),
    system = require('system');

var defaults = {
    width: 1600,
    height: 889,
    format: 'png',
    quality: 100,
    // How many milliseconds to wait after the page has finished loading.
    delay: 1500
};

try {
    var config = JSON.parse(system.args[1]) || {};
} catch (e) {
    console.log('Could not parse JSON configuration:', e);
    console.log('You must pass in JSON configuration as the argument (eg. \'{"url":"http://example.com","output":"snapshot.png"}\').');
    phantom.exit(1);
}

if (!config.url || !config.output) {
    console.log('Both URL and output filename must be provided.');
    phantom.exit(1);
}

if (config.httpUsername && config.httpPassword) {
    page.settings.userName = config.httpUsername;
    page.settings.password = config.httpPassword;
}

if (!config.width) {
    config.width = defaults.width;
}
if (!config.height) {
    config.height = defaults.height;
}
if (!config.format) {
    config.format = defaults.format;
}
if (!config.quality) {
    config.quality = defaults.quality;
}
if (!config.delay) {
    config.delay = defaults.delay;
}

page.viewportSize = {height: config.height, width: config.width};
page.clipRect = {top: 0, left: 0, width: config.width, height: config.height};

page.customHeaders = {
    // Send a "Do Not Track" header.
    "DNT": "1"
};

page.onResourceError = function (resourceError) {
    console.log('Unable to load resource"' + resourceError.url + '". Error (' + resourceError.errorCode + '): ' + resourceError.errorString);
};

page.onResourceRequested = function (requestData, request) {
    // Block access to [www.]google-analytics.com hostname.
    if (requestData['url'].match(/^https?:\/\/(www\.)?google-analytics\.com($|\/)/)) {
        request.abort();
    }
};

page.open(config.url, function (status) {
    // status is unreliable!
    // If a resource fails downloading (even though it does not affect the page at all), status is empty (not even an error)
    window.setTimeout(function () {
        try {
            page.evaluate(function (config) {
                // Handle transparent background by setting it to white
                var backgroundColor = window.getComputedStyle(document.body).backgroundColor;
                var matches = backgroundColor.match(/rgba\(\d+, \d+, \d+, (\d+)\)$/);
                if (matches !== null && matches[1] === "0") {
                    document.body.style.backgroundColor = "white";
                }

                // Workaround for websites having `height: 100%`
                var computedHeight = window.getComputedStyle(document.body).height;
                if (computedHeight === config.height + "px") {
                    // Computed height is equal to the viewport height when height is 100%
                    document.body.style.width = config.width + "px";
                    document.body.style.height = config.height + "px";
                }
            }, config);
            page.render(config.output, {quality: config.quality, format: config.format});
            phantom.exit();
        } catch (e) {
            console.log(e);
            phantom.exit(2);
        }
    }, config.delay);
});
