/**
 * Event Chatbot (E-Bot)
 * Gère l'interface de chat et les réponses automatiques pour les événements
 */

class EventBot {
    constructor() {
        this.isOpen = false;
        this.messages = [];
        this.hasUnread = false;

        // Base de connaissances
        this.knowledge = {
            'bonjour': "Salut ! Je suis E-Bot, votre assistant pour les événements. Comment puis-je t'aider ? 📅",
            'participation': "Vous pouvez participer à nos événements en cliquant sur le bouton \"Participer\" sur la page de l'événement !",
            'événement': "Nous avons plusieurs événements à venir ! Demandez-moi la liste ou posez-moi des questions sur un événement spécifique.",
            'contact': "Tu peux contacter l'équipe admin via l'email : support@gamemasters.com",
            'default': "Je ne suis pas sûr de comprendre. Tu peux reformuler ou me demander la liste des événements."
        };

        this.init();
    }

    init() {
        this.injectHTML();
        this.attachEvents();

        // Message de bienvenue après 2 secondes
        setTimeout(() => {
            if (this.messages.length === 0) {
                this.addBotMessage("Hey ! Besoin d'aide sur nos événements ? Je peux vous donner des infos sur les dates, descriptions et participations ! 📅");
                this.showOptions(['Liste des événements', 'Quand commence...', 'Comment participer']);
                this.notify();
            }
        }, 2000);
    }

    injectHTML() {
        const html = `
            <button id="event-chatbot-toggle">
                <i class="fas fa-robot"></i>
                <span class="chat-badge" style="display: none">1</span>
            </button>

            <div id="event-chatbot-window">
                <div class="chat-header">
                    <div class="chat-title">
                        <div class="bot-avatar"><i class="fas fa-robot"></i></div>
                        <div>
                            <div>E-Bot</div>
                            <div style="font-size: 10px; opacity: 0.7; font-weight: normal">En ligne</div>
                        </div>
                    </div>
                    <button class="chat-close"><i class="fas fa-times"></i></button>
                </div>
                
                <div class="chat-body" id="event-chat-messages">
                    <!-- Messages ici -->
                </div>
                
                <div class="chat-footer">
                    <div class="chat-input-group">
                        <input type="text" id="event-chat-input" placeholder="Écrivez votre message...">
                        <button id="event-chat-send"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
        `;

        const container = document.createElement('div');
        container.innerHTML = html;
        document.body.appendChild(container);
    }

    attachEvents() {
        this.toggleBtn = document.getElementById('event-chatbot-toggle');
        this.window = document.getElementById('event-chatbot-window');
        this.messagesContainer = document.getElementById('event-chat-messages');
        this.input = document.getElementById('event-chat-input');
        this.sendBtn = document.getElementById('event-chat-send');
        this.badge = document.querySelector('#event-chatbot-toggle .chat-badge');
        this.closeBtn = this.window.querySelector('.chat-close');

        // Ouvrir/Fermer
        this.toggleBtn.addEventListener('click', () => this.toggle());
        this.closeBtn.addEventListener('click', () => this.toggle());

        // Envoyer message
        this.sendBtn.addEventListener('click', () => this.handleUserMessage());
        this.input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.handleUserMessage();
        });
    }

    toggle() {
        this.isOpen = !this.isOpen;
        this.window.classList.toggle('active', this.isOpen);
        this.toggleBtn.classList.toggle('active', this.isOpen);
        
        if (this.isOpen) {
            this.hasUnread = false;
            this.badge.style.display = 'none';
            this.input.focus();
        }
    }

    addUserMessage(text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message user-message';
        messageDiv.innerHTML = `
            <div class="message-content">${this.formatMessage(text)}</div>
            <div class="message-time">${this.getCurrentTime()}</div>
        `;
        this.messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
    }

    addBotMessage(text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message bot-message';
        messageDiv.innerHTML = `
            <div class="message-content">${this.formatMessage(text)}</div>
            <div class="message-time">${this.getCurrentTime()}</div>
        `;
        this.messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
        this.messages.push({ type: 'bot', text });
    }

    formatMessage(text) {
        // Convert markdown-like formatting to HTML
        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        text = text.replace(/\*(.*?)\*/g, '<em>$1</em>');
        text = text.replace(/\n/g, '<br>');
        return text;
    }

    getCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    }

    scrollToBottom() {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }

    notify() {
        if (!this.isOpen) {
            this.hasUnread = true;
            this.badge.style.display = 'block';
            this.badge.textContent = '1';
        }
    }

    showOptions(options) {
        const optionsDiv = document.createElement('div');
        optionsDiv.className = 'message-options';
        optionsDiv.innerHTML = options.map(opt => 
            `<button class="option-btn">${opt}</button>`
        ).join('');
        
        optionsDiv.querySelectorAll('.option-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                this.input.value = btn.textContent;
                this.handleUserMessage();
            });
        });
        
        this.messagesContainer.appendChild(optionsDiv);
        this.scrollToBottom();
    }

    async handleUserMessage() {
        const text = this.input.value.trim();
        if (!text) return;

        this.addUserMessage(text);
        this.input.value = '';
        
        // Remove options
        const options = this.messagesContainer.querySelector('.message-options');
        if (options) options.remove();

        // Show typing indicator
        const typingDiv = this.showTyping();

        try {
            const response = await this.findResponse(text);
            this.removeTyping(typingDiv);
            this.addBotMessage(response);
        } catch (error) {
            console.error('Error handling message:', error);
            this.removeTyping(typingDiv);
            this.addBotMessage("Désolé, une erreur s'est produite. Veuillez réessayer.");
        }
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

    async findResponse(text) {
        // Try to get response from backend API first
        try {
            const response = await fetch('?controller=eventchatbot&action=handleRequest', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: text })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('EventChatbot API response:', data);
            
            if (data.success && data.response) {
                return data.response;
            } else if (data.error) {
                console.error('EventChatbot API error:', data.error);
                throw new Error(data.error);
            }
        } catch (error) {
            console.error('Error fetching chatbot response:', error);
            return "Désolé, je rencontre un problème technique. Veuillez réessayer dans un instant. 🔧";
        }
        
        // Fallback to local knowledge base if API fails completely
        const lowerText = text.toLowerCase();
        
        if (lowerText.includes('participation') || lowerText.includes('participer')) return this.knowledge.participation;
        if (lowerText.includes('événement') || lowerText.includes('event')) return this.knowledge.événement;
        if (lowerText.includes('bonjour') || lowerText.includes('salut') || lowerText.includes('hello')) return this.knowledge.bonjour;
        if (lowerText.includes('contact') || lowerText.includes('email')) return this.knowledge.contact;
        
        return this.knowledge.default;
    }
}

// Initialize chatbot when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.eventBot = new EventBot();
    });
} else {
    window.eventBot = new EventBot();
}




