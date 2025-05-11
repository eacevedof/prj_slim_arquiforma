- composer create-project slim/slim-skeleton [your-app-name]
- start localhost server in 8181:
- `make run-app-wsl`

```
ln -s $${HOME}/php.ini $${PATH_DOM_MYPROMOS}}/arquiforma.es/backend_web/public/php.ini

## php.ini no se porque se elimina solo

# chmod 644 php.ini
error_reporting = E_ALL;
display_errors = Off;
log_errors = On;
error_log = /<htdocs>/mi_logs/php-errors.log;

# para que funcione se necestia .msmtprc en <htdocs>/.msmtprc con la config del smtp
sendmail_path = "/usr/bin/msmtp -t -i"
```