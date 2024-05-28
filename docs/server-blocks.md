## 3. NGINX server blocks

api
```sh
sudo nano /etc/nginx/sites-available/api.selonatriklika.rs
#copy from here
server {
    #listen 80;
    #listen [::]:80;
    server_name api.selonatriklika.rs www.api.selonatriklika.rs;
    root /var/www/eco-tourism-api/public;
#    add_header X-XSS-Protection "1; mode=block";
#    add_header X-Content-Type-Options "nosniff";
    #oldr browsers
#    add_header X-Frame-Options "ALLOW-FROM selonatriklika.rs www.selonatriklika.rs https://selonatriklika.rs";
    #new browsers
#    add_header Content-Security-Policy "frame-ancestors selonatriklika.rs www.selonatriklika.rs https://selonatriklika.rs";

    client_body_timeout 10s;
    client_header_timeout 10s;
    client_max_body_size 256M;
    index index.html index.php;
    charset utf-8;
    server_tokens off;
    location / {
        #proxy_pass  http://127.0.0.1:8001;
        try_files   $uri     $uri/  /index.php?$query_string;
    }
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    error_page 404 /index.php;
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
#end 
sudo ln -s /etc/nginx/sites-available/api.selonatriklika.rs /etc/nginx/sites-enabled/
```

app
```sh
sudo nano /etc/nginx/sites-available/selonatriklika.rs
#copy from here
server {    
    server_name selonatriklika.rs www.selonatriklika.rs;
    #root /var/www/coming-soon;
    root /var/www/selonatriklika.rs/current/dist;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";
    client_body_timeout 10s;
    client_header_timeout 10s;
    client_max_body_size 256M;
    index index.html index.php;
    charset utf-8;
    server_tokens off;
    location / {
        try_files   $uri     $uri/  /index.html;
    }
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    error_page 404 /index.php;
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
#end 
sudo ln -s /etc/nginx/sites-available/selonatriklika.rs /etc/nginx/sites-enabled/
```

reload nginx
```sh
sudo systemctl reload nginx
```