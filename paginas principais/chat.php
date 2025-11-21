<?php
// chat.php
require_once 'funcoes.php';

if (!$_SESSION['usuario']) {
    header('Location: login.php');
    exit;
}

$tipo_chat = $_GET['tipo'] ?? 'geral';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - YARA Joias</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #ffe7f6;
        }
        .chat-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .chat-header {
            background: #fe7db9;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .chat-messages {
            height: 400px;
            overflow-y: auto;
            padding: 20px;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
        }
        .user-message {
            background: #fe7db9;
            color: white;
            margin-left: auto;
        }
        .bot-message {
            background: #f0f0f0;
            color: #333;
        }
        .chat-input {
            padding: 20px;
            border-top: 1px solid #ddd;
            display: flex;
            gap: 10px;
        }
        .chat-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .chat-input button {
            background: #fe7db9;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <h2>Chat com Embaixador YARA</h2>
            <p><?php echo $tipo_chat === 'especialista' ? 'Especialista em Joias' : 'Atendimento Geral'; ?></p>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="message bot-message">
                Olá! Sou o embaixador YARA. Como posso ajudá-lo hoje?
            </div>
        </div>
        <div class="chat-input">
            <input type="text" id="messageInput" placeholder="Digite sua mensagem...">
            <button onclick="enviarMensagem()">Enviar</button>
        </div>
    </div>

    <script>
        function enviarMensagem() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            
            if (message) {
                const messages = document.getElementById('chatMessages');
                
                // Mensagem do usuário
                const userMessage = document.createElement('div');
                userMessage.className = 'message user-message';
                userMessage.textContent = message;
                messages.appendChild(userMessage);
                
                // Limpar input
                input.value = '';
                
                // Scroll para baixo
                messages.scrollTop = messages.scrollHeight;
                
                // Resposta automática (simulação)
                setTimeout(() => {
                    const botMessage = document.createElement('div');
                    botMessage.className = 'message bot-message';
                    botMessage.textContent = 'Obrigado pela sua mensagem! Nossa equipe entrará em contato em breve.';
                    messages.appendChild(botMessage);
                    messages.scrollTop = messages.scrollHeight;
                }, 1000);
            }
        }

        // Enviar mensagem com Enter
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                enviarMensagem();
            }
        });
    </script>
</body>
</html>