# Next - Open Supports API

Este repositório contempla o código fonte da API da aplicação "Open Supports" usado no site da Next.

---

## Tópicos

- [Requisitos](#requisitos)
- [Tecnologias utilizadas](#tecnologias-utilizadas)
- [Instalação](#instalação)
    - [Docker](docker)
    - [Debian (e derivados, ex: ubuntu, linux mint, etc...)](#debian-e-derivados-ex-ubuntu-linux-mint-etc)
    - [Servidor com PHP](#servidor-com-php)
    - [LAMP/XAMP](#lampxamp)
- [Deploy](#deploy)
- [Referências](#referências)

---

## Requisitos
- Composer
- Docker (opcional)
- Apache2 (opcional, caso use o docker)
- PHP 5.6+ (opcional, caso use o docker)
- MySQL 4.1+ (opcional, caso use o docker)

## Tecnologias utilizadas
- OpenSupports 4.7.0 `(clonado do master, último commit 25/06/2020 15:25)`
- SlimPHP 2.x (2.6.3)
- PhpMailer 5.2.x
- Respect Validation 1.1.x
- Entre outras...

## Instalação

### Docker

A instalação pode ser feita com docker, para isso basta seguir [estas instruções](https://github.com/opensupports/opensupports#getting-up-and-running-back-end-server-folder).

---

### Servidor com PHP

Caso prefira, pode usar o comando `php -S` no arquivo `index.php`, para isso basta usar:

`php -S localhost:8080 index.php`

---

Caso prefira utilizar a instalação manual com apache2 siga os passos abaixo:

### Debian (e derivados, ex: ubuntu, linux mint, etc...)

sudo cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/api.sup.next.dev.com.conf

- Edite o arquivo `api.sup.next.dev.com.conf` inserindo esse conteúdo nele:

```apacheconfig
<VirtualHost *:80>
	ServerName api.sup.next.dev.com
	ServerAdmin dev@nexttecnologiadainformacao.com.br
	DocumentRoot /home/seuUser/caminho/ate/opensupports-api

	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined

	<Directory /home/seuUser/caminho/ate/opensupports-api>
        	Options Indexes FollowSymLinks MultiViews
	        AllowOverride All
        	Order allow,deny
	        allow from all
		Require all granted
	</Directory>
</VirtualHost>
```

- Agora adicione no final do arquivo `/etc/hosts` a seguinte linha:

`127.0.0.1 api.sup.next.dev.com`

Essa linha indica que o ServerName `api.sup.next.dev.com` é um DNS que aponta para o seu próprio IP.

- Agora é necessário habilitar o site:

`sudo a2ensite api.sup.next.dev.com`

ou

`sudo ln -S /etc/apache2/sites-available/api.sup.next.dev.com /etc/apache2/sites-enabled/`

- Por fim, reiniciar o apache:

`sudo systemctl restart apache2`

---

### LAMP/XAMP

Caso utilize o LAMP ou XAMP seguir os procedimentos padrões para adicionar o site no diretório HTML e usar o apache2 dentro deles para fazer o apontamento do DNS/ServerName conforme demonstrado na instalação do Debian.

> NOTA PARA DEVS: Necessário complementar a instalação do XAMP/LAMP

---

**Observações:**

Caso opte por não utilizar o docker, também deve copiar e editar o arquivo `config.php.example`, a sua cópia deve se chamar `config.php`. Nesse arquivo é configurado o acesso ao banco de dados.

## Documentação da API

É possível gerar a documentação da API através de um comando, para isso siga os passos abaixo:

- npm install -g apidoc
- apidoc -i models/ -i data/ -i libs/ -i controllers/ -o apidoc/

Basta abrir o arquivo `.html` dentro de `apidoc` para ter a documentação dos endpoints desta API.

---

## Deploy

Para realizar o deploy basta se certificar de que o arquivo `config.php` está configurado corretamente para a base de dados em produção, e então subir o diretório `opensupports-api` inteiro para o seu respectivo diretório em produção, renomeando-o para `api`. A sua estrutura deve ser como essa:

- [...]/suporte
    - api `anteriormente 'opensupports-api'`
    - images `contido no repositório do Client`
    - bundle.js `contido no repositório do Client`
    - index.php `contido no repositório do Client`
    - .htaccess `contido no repositório do Client`

## Referências

- [OpenSupports Repositório](https://github.com/opensupports/opensupports)
- [Client/frontend (modificado pela Next)](https://github.com/Next-Tecnologia/opensupports-site)

