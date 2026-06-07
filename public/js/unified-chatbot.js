/**
 * Unified Chatbot
 * Gère l'interface de chat unifiée pour tous les sujets (jeux, projets, événements, formations)
 */

class UnifiedChatbot {
    constructor() {
        this.isOpen = false;
        this.messages = [];
        this.hasUnread = false;
        this.apiEndpoint = '?controller=unifiedchatbot&action=handleRequest';

        this.init();
    }

    init() {
        this.injectHTML();
        this.attachEvents();

        // Message de bienvenue après 2 secondes
        setTimeout(() => {
            if (this.messages.length === 0) {
                this.addBotMessage("Bonjour ! 👋 Je suis votre **Assistant IA Universel** ! 🤖\n\nJe peux vous aider avec :\n- 🎮 **Jeux** : Trouver des jeux, catégories, notes\n- 🌍 **Projets** : Informations sur les projets et donations\n- 📅 **Événements** : Dates, participations, descriptions\n- 📚 **Formations** : Informations sur les formations et éducations\n\nQue souhaitez-vous savoir ?");
                this.showOptions(['Liste des jeux', 'Liste des projets', 'Liste des événements', 'Aide']);
                this.notify();
            }
        }, 2000);
    }

    injectHTML() {
        const html = `
            <button id="unified-chatbot-toggle">
                <i class="fas fa-robot"></i>
                <span class="chat-badge" style="display: none">1</span>
            </button>

            <div id="unified-chatbot-window">
                <div class="chat-header">
                    <div class="chat-title">
                        <div class="bot-avatar"><i class="fas fa-robot"></i></div>
                        <div>
                            <div>Assistant IA</div>
                            <div style="font-size: 10px; opacity: 0.7; font-weight: normal">En ligne</div>
                        </div>
                    </div>
                    <button class="chat-close"><i class="fas fa-times"></i></button>
                </div>
                
                <div class="chat-body" id="unified-chat-messages">
                    <!-- Messages ici -->
                </div>
                
                <div class="chat-footer">
                    <div class="chat-input-group">
                        <input type="text" id="unified-chat-input" placeholder="Écrivez votre message...">
                        <button id="unified-chat-send"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
        `;

        const container = document.createElement('div');
        container.innerHTML = html;
        document.body.appendChild(container);
    }

    showTyping() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message bot-message typing';
        typingDiv.innerHTML = `
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        `;
        this.messagesContainer.appendChild(typingDiv);
        this.scrollToBottom();
        return typingDiv;
    }

    removeTyping(typingDiv) {
        if (typingDiv) typingDiv.remove();
    }

    attachEvents() {
        this.toggleBtn = document.getElementById('unified-chatbot-toggle');
        this.window = document.getElementById('unified-chatbot-window');
        this.messagesContainer = document.getElementById('unified-chat-messages');
        this.input = document.getElementById('unified-chat-input');
        this.sendBtn = document.getElementById('unified-chat-send');
        this.badge = document.querySelector('#unified-chatbot-toggle .chat-badge');
        this.closeBtn = this.window.querySelector('.chat-close');

        // Toggle chat
        this.toggleBtn.addEventListener('click', () => {
            this.toggle();
        });

        // Close chat
        this.closeBtn.addEventListener('click', () => {
            this.close();
        });

        // Send message
        this.sendBtn.addEventListener('click', () => {
            this.sendMessage();
        });

        // Send on Enter
        this.input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
    }

    toggle() {
        this.isOpen = !this.isOpen;
        
        if (this.isOpen) {
            this.open();
        } else {
            this.close();
        }
    }

    open() {
        this.window.classList.add('active');
        this.toggleBtn.classList.add('active');
        this.hasUnread = false;
        this.hideBadge();
        this.scrollToBottom();
    }

    close() {
        this.window.classList.remove('active');
        this.toggleBtn.classList.remove('active');
    }

    notify() {
        if (!this.isOpen) {
            this.hasUnread = true;
            this.showBadge();
        }
    }

    showBadge() {
        if (this.badge) {
            this.badge.style.display = 'flex';
        }
    }

    hideBadge() {
        if (this.badge) {
            this.badge.style.display = 'none';
        }
    }

    addUserMessage(text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message user-message';
        messageDiv.innerHTML = `
            <div>${this.formatMessage(text)}</div>
            <div class="message-time">${this.getTime()}</div>
        `;
        this.messagesContainer.appendChild(messageDiv);
        this.messages.push({ type: 'user', text: text, time: new Date() });
        this.scrollToBottom();
    }

    addBotMessage(text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message bot-message';
        messageDiv.innerHTML = `
            <div>${this.formatMessage(text)}</div>
            <div class="message-time">${this.getTime()}</div>
        `;
        this.messagesContainer.appendChild(messageDiv);
        this.messages.push({ type: 'bot', text: text, time: new Date() });
        this.scrollToBottom();
    }

    formatMessage(text) {
        // Convert markdown-like formatting to HTML
        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        text = text.replace(/\*(.*?)\*/g, '<em>$1</em>');
        text = text.replace(/\n/g, '<br>');
        // Convert markdown links to HTML
        text = text.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank">$1</a>');
        return text;
    }

    showOptions(options) {
        const optionsDiv = document.createElement('div');
        optionsDiv.className = 'message-options';
        
        options.forEach(option => {
            const btn = document.createElement('button');
            btn.className = 'option-btn';
            btn.textContent = option;
            btn.addEventListener('click', () => {
                this.input.value = option;
                this.sendMessage();
            });
            optionsDiv.appendChild(btn);
        });
        
        this.messagesContainer.appendChild(optionsDiv);
        this.scrollToBottom();
    }

    async sendMessage() {
        const message = this.input.value.trim();
        
        if (!message) return;
        
        this.addUserMessage(message);
        this.input.value = '';
        
        const typingDiv = this.showTyping();
        
        try {
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: message })
            });
            
            const data = await response.json();
            
            this.removeTyping(typingDiv);
            
            if (data.success) {
                this.addBotMessage(data.response);
            } else {
                this.addBotMessage("Désolé, une erreur s'est produite. Veuillez réessayer. 😔");
            }
        } catch (error) {
            console.error('Chatbot error:', error);
            this.removeTyping(typingDiv);
            this.addBotMessage("Désolé, je ne peux pas me connecter au serveur. Vérifiez votre connexion internet. 🔧");
        }
    }

    scrollToBottom() {
        if (this.messagesContainer) {
            this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
        }
    }

    getTime() {
        const now = new Date();
        return now.getHours().toString().padStart(2, '0') + ':' + 
               now.getMinutes().toString().padStart(2, '0');
    }
}

// Initialize chatbot when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.unifiedChatbot = new UnifiedChatbot();
});

