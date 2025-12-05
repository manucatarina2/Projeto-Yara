<?php
// chat.php
require_once 'funcoes.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario']) || !$_SESSION['usuario']) {
    header('Location: index.php');
    exit;
}

$tipo_chat = $_GET['tipo'] ?? 'geral';
$usuario_nome = $_SESSION['usuario']['nome'] ?? 'Cliente';
$usuario_id = $_SESSION['usuario']['id'] ?? 0;

// Definir título e descrição baseado no tipo de chat
$titulo_chat = '';
$descricao_chat = '';

if ($tipo_chat === 'especialista') {
    $titulo_chat = 'Especialista em Joias';
    $descricao_chat = 'Conversando com nosso especialista em joias';
} else {
    $titulo_chat = 'Atendimento Geral';
    $descricao_chat = 'Conversando com nosso embaixador YARA';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - YARA Joias</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Reset e estilos base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background-color: #f8f8f8;
            color: #333;
            line-height: 1.6;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header do chat */
        .chat-header {
            background-color: white;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
        }

        .chat-header-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e91e63, #ff4081);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 500;
        }

        .chat-title h1 {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .chat-title p {
            font-size: 12px;
            color: #666;
        }

        .chat-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #4CAF50;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #4CAF50;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .chat-actions {
            display: flex;
            gap: 15px;
        }

        .chat-action-btn {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 16px;
            transition: color 0.3s;
        }

        .chat-action-btn:hover {
            color: #e91e63;
        }

        /* Container principal do chat */
        .chat-container {
            flex: 1;
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            height: calc(100vh - 70px);
        }

        /* Área de mensagens */
        .chat-messages {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: white;
            border-radius: 8px;
            margin: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .messages-container {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        /* Estilos das mensagens */
        .message {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .user-message {
            background: linear-gradient(135deg, #e91e63, #ff4081);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }

        .bot-message {
            background-color: #f5f5f5;
            color: #333;
            align-self: flex-start;
            border-bottom-left-radius: 4px;
        }

        .message-sender {
            font-size: 12px;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .user-message .message-sender {
            color: rgba(255, 255, 255, 0.8);
            text-align: right;
        }

        .bot-message .message-sender {
            color: #666;
        }

        .message-time {
            font-size: 10px;
            margin-top: 4px;
            opacity: 0.7;
            text-align: right;
        }

        .user-message .message-time {
            text-align: right;
        }

        .bot-message .message-time {
            text-align: left;
        }

        /* Área de input */
        .chat-input-area {
            padding: 20px;
            border-top: 1px solid #eee;
            background-color: white;
        }

        .input-container {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }

        .message-input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid #eee;
            border-radius: 24px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s;
            resize: none;
            max-height: 120px;
            min-height: 44px;
        }

        .message-input:focus {
            border-color: #e91e63;
        }

        .send-button {
            background: linear-gradient(135deg, #e91e63, #ff4081);
            color: white;
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .send-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(233, 30, 99, 0.3);
        }

        .send-button:active {
            transform: translateY(0);
        }

        .input-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .action-btn {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 16px;
            transition: color 0.3s;
            padding: 5px;
        }

        .action-btn:hover {
            color: #e91e63;
        }

        /* Sidebar de informações */
        .chat-sidebar {
            width: 300px;
            background-color: white;
            border-radius: 8px;
            margin: 20px 20px 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .sidebar-section {
            margin-bottom: 25px;
        }

        .sidebar-section h3 {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 12px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }

        .specialist-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 15px 0;
        }

        .specialist-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e91e63, #ff4081);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            margin-bottom: 15px;
        }

        .specialist-name {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .specialist-role {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .specialist-rating {
            color: #FFC107;
            margin-bottom: 10px;
        }

        .specialist-status {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            color: #4CAF50;
        }

        .quick-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .quick-action {
            background: none;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 10px 15px;
            text-align: left;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quick-action:hover {
            border-color: #e91e63;
            color: #e91e63;
        }

        .quick-action i {
            width: 16px;
        }

        .chat-history {
            flex: 1;
            overflow-y: auto;
        }

        .history-item {
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .history-item:hover {
            background-color: #f9f9f9;
        }

        .history-date {
            font-size: 12px;
            color: #666;
            margin-bottom: 4px;
        }

        .history-preview {
            font-size: 13px;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Indicador de digitação */
        .typing-indicator {
            display: none;
            align-self: flex-start;
            background-color: #f5f5f5;
            padding: 12px 16px;
            border-radius: 18px;
            border-bottom-left-radius: 4px;
            margin-bottom: 15px;
        }

        .typing-dots {
            display: flex;
            gap: 4px;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #999;
            animation: typing 1.4s infinite ease-in-out;
        }

        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }

        @keyframes typing {
            0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
            40% { transform: scale(1); opacity: 1; }
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .chat-container {
                flex-direction: column;
                height: calc(100vh - 70px);
            }

            .chat-sidebar {
                width: 100%;
                margin: 0;
                border-radius: 0;
                display: none; /* Esconder sidebar em mobile por padrão */
            }

            .chat-messages {
                margin: 0;
                border-radius: 0;
            }

            .message {
                max-width: 85%;
            }

            .sidebar-toggle {
                display: block !important;
            }
        }

        @media (min-width: 769px) {
            .sidebar-toggle {
                display: none;
            }
        }

        /* Botão para mostrar/ocultar sidebar em mobile */
        .sidebar-toggle {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 16px;
        }

        /* Mensagens do sistema */
        .system-message {
            align-self: center;
            background-color: rgba(233, 30, 99, 0.1);
            color: #e91e63;
            padding: 8px 16px;
            border-radius: 16px;
            font-size: 12px;
            max-width: 80%;
            text-align: center;
        }

        /* Links nas mensagens */
        .message-link {
            color: #e91e63;
            text-decoration: none;
            font-weight: 500;
        }

        .message-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Header do Chat -->
    <div class="chat-header">
        <div class="chat-header-info">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="chat-avatar">
                <i class="fas fa-gem"></i>
            </div>
            <div class="chat-title">
                <h1>Administrador YARA</h1>
                <p><?php echo $titulo_chat; ?></p>
            </div>
        </div>
        <div class="chat-status">
            <div class="status-dot"></div>
            <span>Online</span>
        </div>
        <div class="chat-actions">
            <button class="chat-action-btn" title="Informações">
                <i class="fas fa-info-circle"></i>
            </button>
            <button class="chat-action-btn" onclick="window.close() || window.history.back()" title="Fechar">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Container Principal -->
    <div class="chat-container">
        <!-- Sidebar de Informações -->
        <div class="chat-sidebar" id="chatSidebar">
            <div class="sidebar-section">
                <div class="specialist-info">
                    <div class="specialist-avatar">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="specialist-name">
                        Administrador YARA
                    </div>
                    <div class="specialist-role">
                        <?php echo $tipo_chat === 'especialista' ? 'Especialista em Joias' : 'Atendimento ao Cliente'; ?>
                    </div>
                    <div class="specialist-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <div class="specialist-status">
                        <div class="status-dot"></div>
                        <span>Disponível</span>
                    </div>
                </div>
            </div>

            <div class="sidebar-section">
                <h3>Ações Rápidas</h3>
                <div class="quick-actions">
                    <button class="quick-action" onclick="suggestMessage('Gostaria de informações sobre personalização de joias.')">
                        <i class="fas fa-gem"></i>
                        <span>Personalização</span>
                    </button>
                    <button class="quick-action" onclick="suggestMessage('Qual o prazo de entrega?')">
                        <i class="fas fa-shipping-fast"></i>
                        <span>Prazo de Entrega</span>
                    </button>
                    <button class="quick-action" onclick="suggestMessage('Preciso de ajuda para escolher um presente.')">
                        <i class="fas fa-gift"></i>
                        <span>Sugestão de Presente</span>
                    </button>
                    <button class="quick-action" onclick="suggestMessage('Como funciona a garantia das joias?')">
                        <i class="fas fa-shield-alt"></i>
                        <span>Garantia</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- Área de Mensagens -->
        <div class="chat-messages">
            <div class="messages-container" id="messagesContainer">
                <!-- Mensagem de boas-vindas -->
                <div class="message bot-message">
                    <div class="message-sender">Administrador YARA</div>
                    Olá, <?php echo $usuario_nome; ?>! Sou seu administrador YARA. Como posso ajudá-lo hoje?
                    <div class="message-time"><?php echo date('H:i'); ?></div>
                </div>

                <!-- Mensagem do sistema -->
                <div class="system-message">
                    <?php echo $descricao_chat; ?>
                </div>
            </div>

            <!-- Indicador de digitação -->
            <div class="typing-indicator" id="typingIndicator">
                <div class="typing-dots">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>

            <!-- Área de Input -->
            <div class="chat-input-area">
                <div class="input-container">
                    <textarea
                        class="message-input"
                        id="messageInput"
                        placeholder="Digite sua mensagem..."
                        rows="1"
                    ></textarea>
                    <button class="send-button" onclick="enviarMensagem()">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <div class="input-actions">
                    <button class="action-btn" title="Limpar conversa" onclick="limparConversa()">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Elementos da página
        const messageInput = document.getElementById('messageInput');
        const messagesContainer = document.getElementById('messagesContainer');
        const typingIndicator = document.getElementById('typingIndicator');
        const sidebar = document.getElementById('chatSidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');

        // Sistema de respostas específicas baseado nas perguntas
        const respostasEspecificas = {
            // Personalização
            'personalização': "Depois de acessar nossa página inicial, você pode ir em 'Personalize Já' e explorar as combinações possíveis para criar joias únicas, feitas com carinho e originalidade.",
           
            // Prazo de entrega
            'prazo de entrega': "O prazo varia conforme o serviço escolhido — compra online, atendimento em boutique, consultoria, personalização ou manutenção. Navegue pelo site para verificar as opções ou visite nosso estabelecimento para mais informações.",
           
            // Presente
            'presente': "Você pode filtrar pelos gostos e preferências de quem vai presentear, além de ajustar por valor e relevância. Experimente também nossa personalização e outras formas especiais de presentear.",
           
            // Garantia
            'garantia': "Oferecemos garantia para defeitos de fabricação, conforme política da marca. Para detalhes, consulte nossa página de garantia ou fale conosco para orientações personalizadas.",
           
            // Respostas gerais para outras perguntas
            'default': [
                "Entendi sua dúvida. Posso ajudá-lo com informações sobre nossos produtos e serviços.",
                "Obrigado pelo seu contato! Como posso auxiliá-lo hoje?",
                "Estou aqui para ajudar. Pode me contar mais sobre o que precisa?",
                "Temos várias opções que podem atender ao que você procura. Pode me dar mais detalhes?",
                "Posso orientá-lo sobre nossos serviços. O que gostaria de saber?"
            ]
        };

        // Palavras-chave para identificar as perguntas
        const palavrasChave = {
            'personalização': ['personalização', 'personalizar', 'customizar', 'personalize já', 'joia única'],
            'prazo de entrega': ['prazo', 'entrega', 'tempo de entrega', 'quando chega', 'demora'],
            'presente': ['presente', 'presentear', 'sugestão presente', 'ajuda presente', 'presente ideal'],
            'garantia': ['garantia', 'defeito', 'troca', 'devolução', 'política garantia']
        };

        // Função para identificar a pergunta e retornar a resposta adequada
        function identificarResposta(mensagem) {
            const mensagemLower = mensagem.toLowerCase();
           
            // Verificar cada categoria de palavras-chave
            for (const [categoria, palavras] of Object.entries(palavrasChave)) {
                for (const palavra of palavras) {
                    if (mensagemLower.includes(palavra)) {
                        return respostasEspecificas[categoria];
                    }
                }
            }
           
            // Se não encontrou correspondência, retorna resposta padrão
            const respostasDefault = respostasEspecificas.default;
            return respostasDefault[Math.floor(Math.random() * respostasDefault.length)];
        }

        // Função para enviar mensagem
        function enviarMensagem() {
            const message = messageInput.value.trim();
           
            if (message) {
                // Adicionar mensagem do usuário
                addMessage(message, 'user');
               
                // Limpar input
                messageInput.value = '';
                adjustTextareaHeight();
               
                // Mostrar indicador de digitação
                showTypingIndicator();
               
                // Simular resposta após um delay
                setTimeout(() => {
                    hideTypingIndicator();
                    const resposta = identificarResposta(message);
                    addMessage(resposta, 'bot');
                }, 1500 + Math.random() * 1000); // Delay entre 1.5 e 2.5 segundos
            }
        }

        // Função para adicionar mensagem ao chat
        function addMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}-message`;
           
            const time = new Date().toLocaleTimeString('pt-BR', {
                hour: '2-digit',
                minute: '2-digit'
            });
           
            const senderName = sender === 'user' ? 'Você' : 'Administrador YARA';
           
            // Processar links nas mensagens
            let processedText = text;
           
            // Adicionar link para "Personalize Já"
            if (text.includes("'Personalize Já'")) {
                processedText = text.replace("'Personalize Já'", "<a href='personalize.php' class='message-link'>Personalize Já</a>");
            }
           
            // Adicionar link para página de garantia
            if (text.includes("página de garantia")) {
                processedText = text.replace("página de garantia", "<a href='servicos.php' class='message-link'>página de garantia</a>");
            }
           
            messageDiv.innerHTML = `
                <div class="message-sender">${senderName}</div>
                ${processedText}
                <div class="message-time">${time}</div>
            `;
           
            messagesContainer.appendChild(messageDiv);
            scrollToBottom();
        }

        // Função para mostrar indicador de digitação
        function showTypingIndicator() {
            typingIndicator.style.display = 'block';
            scrollToBottom();
        }

        // Função para esconder indicador de digitação
        function hideTypingIndicator() {
            typingIndicator.style.display = 'none';
        }

        // Função para rolar para o final do chat
        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Função para ajustar altura do textarea
        function adjustTextareaHeight() {
            messageInput.style.height = 'auto';
            messageInput.style.height = (messageInput.scrollHeight) + 'px';
        }

        // Função para sugerir mensagem (ações rápidas)
        function suggestMessage(message) {
            messageInput.value = message;
            messageInput.focus();
            adjustTextareaHeight();
        }

        // Função para limpar conversa
        function limparConversa() {
            if (confirm('Tem certeza que deseja limpar esta conversa?')) {
                // Manter apenas a mensagem de boas-vindas
                const welcomeMessage = messagesContainer.querySelector('.bot-message');
                const systemMessage = messagesContainer.querySelector('.system-message');
               
                messagesContainer.innerHTML = '';
               
                if (welcomeMessage) messagesContainer.appendChild(welcomeMessage);
                if (systemMessage) messagesContainer.appendChild(systemMessage);
               
                scrollToBottom();
            }
        }

        // Event Listeners
        messageInput.addEventListener('input', adjustTextareaHeight);
       
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                enviarMensagem();
            }
        });

        // Toggle sidebar em mobile
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.style.display = sidebar.style.display === 'none' ? 'flex' : 'none';
            });
        }

        // Ajustar altura inicial do textarea
        adjustTextareaHeight();
        scrollToBottom();

        // Simular mensagem de boas-vindas adicional para especialista
        <?php if ($tipo_chat === 'especialista'): ?>
        setTimeout(() => {
            addMessage("Como especialista em joias, estou aqui para ajudar com qualquer dúvida sobre nossos produtos, materiais, manutenção ou sugestões personalizadas. Posso orientar sobre escolha de pedras, metais e designs que melhor se adequem ao seu estilo.", 'bot');
        }, 1000);
        <?php endif; ?>
    </script>
</body>
</html>