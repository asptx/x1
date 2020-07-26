Установка zTDS на сервере с Nginx+PHP-FPM
CentOS 7 minimal

Nginx
# yum install epel-release
# yum install nginx
# systemctl start nginx
# firewall-cmd --permanent --zone=public --add-service=http
# firewall-cmd --permanent --zone=public --add-service=https
# firewall-cmd --reload
# systemctl enable nginx
Открываем в браузере http://ip/ (должна быть стандартная заглушка nginx)
Заливаем новый конфиг в /etc/nginx/nginx.conf (tds.com - домен вашей ТДС)
# service nginx restart

php-fpm
# yum install -y php-fpm php-cli php-gd php-ldap php-odbc php-pdo php-pecl-memcache php-pear php-xml php-xmlrpc php-mbstring php-snmp php-soap
# systemctl enable php-fpm.service
# systemctl start php-fpm.service

Создаем папки
# mkdir -m 777 /var/lib/php/session
# mkdir -m 777 /var/www/html/tds.com

Заливаем файлы ТДС в /var/www/html/tds.com
Устанавливаем права 777 на файлы в папке database