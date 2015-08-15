Add this to Nginx configuration for development environment:

    location ~ ^(/app_dev\.php)?(/dashboard/.*\.(css|js|jpg|png|html))$ {
        root /home/vagrant/www/undine;
        try_files $2 =404;
    }
