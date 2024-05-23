## php dokumentacija

kopirati `.env.example` u `.env`

instalacija php8.1
```sh
sudo apt install --no-install-recommends php8.1 php8.1-fpm
sudo apt-get install -y php8.1-cli php8.1-common php8.1-mysql php8.1-zip php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml php8.1-bcmath 
```
>8.2 nije testirano u lokalu
```sh
sudo apt install --no-install-recommends php8.2 php8.2-fpm
sudo apt-get install -y php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath 
````

potrebno za pecl
```sh
sudo apt install -y php8.1-dev
```
pecl instalacija
```sh
sudo apt-get install php-pear
```

Potrebne ekstenzije
> ekstenzija `imagick` za rad zahteva dodatne pakete
```sh
sudo apt-get install libmagickwand-dev libmagickcore-dev
sudo pecl install imagick
sudo nano /etc/php/8.1/cli/php.ini
sudo nano /etc/php/8.1/fpm/php.ini
'extension=imagick.so'
sudo service php8.1-fpm reload
```

izmena php verzije
```sh
sudo update-alternatives --config php
```

brisanje svih php verzija
```sh
sudo apt-get purge php*.*
sudo apt-get autoremove
sudo apt-get autoclean
php -v
```

- Primeri .md [dillinger.io](https://dillinger.io/)

## Tech

- [Laravel](www.laravel.com) - HTML enhanced for web apps!

## Plugins

Dillinger is currently extended with the following plugins.
Instructions on how to use them in your own application are linked below.

| Plugin | README |
| ------ | ------ |
| Dropbox | [plugins/dropbox/README.md][PlDb] |
| GitHub | [plugins/github/README.md][PlGh] |
| Google Drive | [plugins/googledrive/README.md][PlGd] |
| OneDrive | [plugins/onedrive/README.md][PlOd] |
| Medium | [plugins/medium/README.md][PlMe] |
| Google Analytics | [plugins/googleanalytics/README.md][PlGa] |
