var page = require('webpage').create(),
    system = require('system');

var config = {
    width: 1600,
    height: 889,
    format: 'png',
    quality: 100,
    // How many milliseconds to wait after the page has finished loading.
    delay: 1500,
    resourceTimeout: 60000,
    timeout: 300000,
    httpUsername: '',
    httpPassword: ''
};

if (system.args.length < 3) {
    console.log('Usage:', system.args[0] + ' URL OUTPUT_FILE' + Object.keys(config).reduce(function (carry, key) {
            return carry + ' --' + key + '=' + config[key];
        }, ''));
    phantom.exit(1);
}

config.url = system.args[1];
config.output = system.args[2];

system.args.slice(3).forEach(function (v) {
    var match = /^--([^=]+)=(.*)$/.exec(v);
    if (match === null) {
        console.log('Unknown positional parameter:', v);
        phantom.exit(1);
    }
    if (Object.keys(config).indexOf(match[1]) === -1) {
        console.log('Unknown config option:', match[1]);
        phantom.exit(1);
    }
    config[match[1]] = match[2];
});

if (!config.url || !config.output) {
    console.log('Both URL and output filename must be provided.');
    phantom.exit(1);
}

if (config.httpUsername && config.httpPassword) {
    page.settings.userName = config.httpUsername;
    page.settings.password = config.httpPassword;
}

page.viewportSize = {height: config.height, width: config.width};
page.clipRect = {top: 0, left: 0, width: config.width, height: config.height};

page.customHeaders = {
    // Send a "Do Not Track" header.
    "DNT": "1"
};

page.settings.userAgent = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.86 Safari/537.36';
page.settings.resourceTimeout = 60000;

setTimeout(function () {
    console.log('Capturing process timed out.');
    phantom.exit(3);
}, config.timeout);

page.onResourceRequested = function (resource, request) {
    // Block access to [www.]google-analytics.com hostname.
    if (resource.url.match(/^https?:\/\/(www\.)?google-analytics\.com($|\/)/)) {
        console.log('Skipped analytics:', resource.url);
        request.abort();
    }
};

page.onResourceReceived = function (resource) {
    console.log('Loaded:', resource.url);
};

page.onResourceError = function (resource) {
    console.log('Unable to load resource"' + resource.url + '". Error (' + resource.errorCode + '): ' + resource.errorString);
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
            console.log('Page captured.');
            phantom.exit();
        } catch (e) {
            console.log(e);
            phantom.exit(2);
        }
    }, config.delay);
});
