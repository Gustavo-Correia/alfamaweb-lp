AlfamaWeb – Landing Page

Landing Page desenvolvida com formulário de contato e envio de e-mails via PHP.

Requisitos

PHP >= 7.4

Servidor local (PHP built-in, XAMPP, WAMP ou similar)

Como executar

Clone o repositório:

git clone https://github.com/Gustavo-Correia/alfamaweb-lp.git


Crie o arquivo .env na raiz do projeto com base em .env.example e configure os dados SMTP.
utilize o email do gmail e crie uma senha do app
para pegar a senha do app, vai em gerenciar sua conta do gmail e pesquise senhas de app depois e so gerar

voce também pode, colocar a sua senha do gmail padrão de login mas não é recomendado
Inicie o servidor local:

php -S localhost:8000


Acesse:

http://localhost:8000

Envio de e-mails

O formulário envia os dados para /mail/mail.php, utilizando SMTP com PHPMailer.

Dependências

O diretório vendor/ foi commitado intencionalmente para facilitar a execução do projeto, evitando a necessidade de rodar composer install.
