## 4. SSL 

create ssl certificate
>opbratiti paznju na slove greske kada se unosi naziv domena
```sh
sudo apt install certbot python3-certbot-nginx
#u dns manager, dodati A record za poddomen.domen.net i www.poddomen.domen.net
sudo certbot --nginx -d selonatriklika.rs -d www.selonatriklika.rs
sudo certbot --nginx -d api.selonatriklika.rs -d www.api.selonatriklika.rs

```
