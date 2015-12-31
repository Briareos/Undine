### Development setup

This development setup guide is geared toward the [Legend](https://github.com/Briareos/Legend) vagrant machine.

#### Add Server

- Go to: `Settings > Build, Execution, Deployment > Deployment > Add`
- Mark the machine as "Default"
- Configure new Deploymet Server:
    - Name: "Undine"
    - Method: FTP (insecure and fastest)
    - Connection:
        - FTP host: `localhost`
        - Port: `21`
    	- Root Path: `/home/vagrant/www/undine`
    	- Username: `vagrant`
    	- Password: `vagrant`
    	- Save password: `true`
    	- Check "passive mode"
    	- Web Server Root URL: http://undine.dev.localhost (for valid SSL certificates)
    - Mappings:
    	- Deployment path: /
    - Excluded paths:
    	- Add deployment path:
    	```
    	/var/logs
    	/var/cache/prod
    	/var/cache/test
    	/var/cache/dev/annotations
    	/var/cache/dev/doctrine
    	/var/cache/dev/profiler
    	/var/cache/dev/twig
    	/vendor
    	/node_modules
    	```

Add this to Nginx configuration for development environment:

    location ~ ^/tmp/(css|js|semantic|themes|font)/ {
        root /home/vagrant/www/undine/var;
    }

    location ~ ^/tmp/image/(.*\.(jpg|png))$ {
        root /home/vagrant/www/undine;
        try_files /frontend/image/$1 =404;
    }

    location ~ ^/node_modules/ {
        root /home/vagrant/www/undine;
    }

To bootstrap the application, run `composer install --no-scripts --no-autoloader --ignore-platform-reqs` on the host machine.
That should be all the host should run in order to provide smooth development experience.

On the guest you have to run `composer install`, which will, along with the standard tasks, run `npm install`
(`/node_modules`) and `gulp build-dev`/`gulp build` (`/var/tmp`, `/web/(css|js|image)`), which will prepare the twig part of the project for
the (automatically run) `cache:warmup` command.

>Note: If you get a 500 error on the `/dashboard` endpoint in production mode, do `tail var/logs/prod.log`; if the
logs say anything about gulp manifest files, you have to run `gulp build-dev` manually.

Every time you want to work on the dashboard, you have to run `gulp` from the guest machine. If you have put the Nginx
configuration mentioned above, everything should work smoothly.

Currently, it's too early for migrations. Just run `php bin/console doctrine:schema:update --force` when you want to
update the schema.

To load the fixtures run `php bin/console doctrine:fixtures:load`.

