<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require __DIR__ . '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$smtp_host = $_ENV['SMTP_HOST'];
$smtp_user = $_ENV['SMTP_USER'];
$smtp_pass = $_ENV['SMTP_PASS'];
$smtp_port = $_ENV['SMTP_PORT'];
$smtp_secure = $_ENV['SMTP_SECURE'];

$email_from = $_ENV['EMAIL_FROM'];
$email_from_name = $_ENV['EMAIL_FROM_NAME'];


// Destinatários
$destinatarios = array(
    'gucorreia2901@gmail.com'
);


$errors = array();


$origem = isset($_POST['origem']) ? $_POST['origem'] : 'contact_form';

//validar inputs com base na origem
if ($origem === 'hero_form') {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);
    $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $valor = filter_input(INPUT_POST, 'valor', FILTER_SANITIZE_STRING);
    $processo = filter_input(INPUT_POST, 'processo', FILTER_SANITIZE_STRING);
    $assunto = "Novo Contato - Formulário Principal";
    $mensagem = "Contato via formulário principal do site.";
} else {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $assunto = filter_input(INPUT_POST, 'assunto', FILTER_SANITIZE_STRING);
    $mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_STRING);
    $estado = isset($_POST['estado']) ? filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING) : '';
    $valor = isset($_POST['valor']) ? filter_input(INPUT_POST, 'valor', FILTER_SANITIZE_STRING) : '';
    $processo = isset($_POST['processo']) ? filter_input(INPUT_POST, 'processo', FILTER_SANITIZE_STRING) : '';
}

// Validações obrigatórias
if (empty($nome)) {
    $errors[] = 'Nome é obrigatório.';
}

if (empty($telefone)) {
    $errors[] = 'Telefone é obrigatório.';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'E-mail inválido.';
}

// Validações específicas por formulário
if ($origem === 'contact_form') {
    if (empty($assunto)) {
        $errors[] = 'Assunto é obrigatório.';
    }
    if (empty($mensagem)) {
        $errors[] = 'Mensagem é obrigatória.';
    }
}

// Se houver erros, retornar
if (!empty($errors)) {
    echo json_encode(array(
        'success' => false,
        'message' => implode(' ', $errors)
    ));
    exit;
}

try {
    // Criar instância do PHPMailer
    $mail = new PHPMailer(true);
    
    // Configurações do servidor SMTP
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_user;
    $mail->Password = $smtp_pass;
    $mail->SMTPSecure = $smtp_secure;
    $mail->Port = $smtp_port;
    $mail->CharSet = 'UTF-8';
    
    // Remetente
    $mail->setFrom($email_from, $email_from_name);
    $mail->addReplyTo($email, $nome);
    
    // for each nos destinarios pq a tarefa pede para enviar para mais de 1
    foreach ($destinatarios as $destinatario) {
        $mail->addAddress($destinatario);
    }
    
    $email_subject = $origem === 'hero_form' ? "Novo Lead - Formulário Principal" : "Contato do Site: " . $assunto;
    $mail->Subject = $email_subject;
    
    // Corpo do e-mail
    $body = "
    <html>
    <head>
        <title>$email_subject</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%); color: white; padding: 20px; text-align: center; }
            .content { background: #f8f9fa; padding: 20px; border-radius: 0 0 10px 10px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #1a365d; }
            .value { color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Novo Contato do Site</h2>
                <p>Formulário: " . ($origem === 'hero_form' ? 'Principal' : 'Contato') . "</p>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>Nome:</div>
                    <div class='value'>$nome</div>
                </div>
                
                <div class='field'>
                    <div class='label'>Telefone:</div>
                    <div class='value'>$telefone</div>
                </div>
                
                <div class='field'>
                    <div class='label'>E-mail:</div>
                    <div class='value'>$email</div>
                </div>";
                
    if (!empty($estado)) {
        $body .= "
                <div class='field'>
                    <div class='label'>Estado/UF:</div>
                    <div class='value'>$estado</div>
                </div>";
    }
    
    if (!empty($valor)) {
        $body .= "
                <div class='field'>
                    <div class='label'>Valor do precatório:</div>
                    <div class='value'>$valor</div>
                </div>";
    }
    
    if (!empty($processo)) {
        $body .= "
                <div class='field'>
                    <div class='label'>Número do processo:</div>
                    <div class='value'>$processo</div>
                </div>";
    }
    
    if ($origem === 'contact_form') {
        $body .= "
                <div class='field'>
                    <div class='label'>Assunto:</div>
                    <div class='value'>$assunto</div>
                </div>
                
                <div class='field'>
                    <div class='label'>Mensagem:</div>
                    <div class='value'>$mensagem</div>
                </div>";
    }
    
    $body .= "
                <div class='field'>
                    <div class='label'>Data/Hora:</div>
                    <div class='value'>" . date('d/m/Y H:i:s') . "</div>
                </div>
                
                <div class='field'>
                    <div class='label'>IP do remetente:</div>
                    <div class='value'>" . $_SERVER['REMOTE_ADDR'] . "</div>
                </div>
            </div>
        </div>
    </body>
    </html>";

    $mail->isHTML(true);
    $mail->Body = $body;
    
    // Enviar e-mail
    if ($mail->send()) {
        echo json_encode(array(
            'success' => true,
            'message' => 'Mensagem enviada com sucesso! Entraremos em contato em breve.'
        ));
    } else {
        throw new Exception('Erro ao enviar e-mail: ' . $mail->ErrorInfo);
    }
    
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => 'Erro ao enviar e-mail: ' . $e->getMessage()
    ));
}
?>