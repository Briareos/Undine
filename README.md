Add this to Nginx configuration for development environment:

    location ~ ^(/app_dev\.php)?(/dashboard/.*\.(css|js|html))$ {
        root /home/vagrant/www/undine;
        try_files $2 =404;
    }

    location ~ ^(/app_dev\.php)?(/image/.*\.(jpg|png))$ {
        root /home/vagrant/www/undine/dashboard;
        try_files $2 =404;
    }
