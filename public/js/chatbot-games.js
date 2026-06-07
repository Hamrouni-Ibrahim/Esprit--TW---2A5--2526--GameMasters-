/**
 * Game Masters Chatbot (G-Bot)
 * Gère l'interface de chat et les réponses automatiques
 */

class GameBot {
    constructor() {
        this.isOpen = false;
        this.messages = [];
        this.hasUnread = false;

        // Base de connaissances
        this.knowledge = {
            'bonjour': "Salut ! Je suis G-Bot. Comment puis-je t'aider aujourd'hui ? 🎮",
            'inscription': "Pour t'inscrire, clique sur le bouton 'Inscription' en haut à droite. C'est gratuit et ça prend 2 minutes !",
            'connexion': "Tu peux te connecter via le bouton 'Connexion'. Si tu as oublié ton mot de passe, utilise le lien 'Mot de passe oublié'.",
            'jeu': "Nous avons une superbe collection de jeux ! Va dans la section 'Jeux' pour les découvrir.",
            'contact': "Tu peux contacter l'équipe admin via l'email : support@gamemasters.com",
            'bug': "Oups ! Si tu as trouvé un bug, merci de le signaler à un administrateur.",
            'prix': "L'inscription est 100% gratuite pour le moment !",
            'default': "Je ne suis pas sûr de comprendre. Tu peux choisir une option ci-dessous ou reformuler."
        };

        this.init();
    }

    init() {
        this.injectHTML();
        this.attachEvents();

        // Message de bienvenue après 2 secondes
        setTimeout(() => {
            if (this.messages.length === 0) {
                this.addBotMessage("Hey ! Besoin d'aide ou envie de découvrir nos jeux ? 🚀");
                this.showOptions(['Comment s\'inscrire ?', 'Voir les jeux', 'Mot de passe oublié']);
                this.notify();
            }
        }, 2000);
    }

    injectHTML() {
        const html = `
            <button id="chatbot-toggle">
                <i class="fas fa-robot"></i>
                <span class="chat-badge" style="display: none">1</span>
            </button>

            <div id="chatbot-window">
                <div class="chat-header">
                    <div class="chat-title">
                        <div class="bot-avatar"><i class="fas fa-robot"></i></div>
                        <div>
                            <div>G-Bot</div>
                            <div style="font-size: 10px; opacity: 0.7; font-weight: normal">En ligne</div>
                        </div>
                    </div>
                    <button class="chat-close"><i class="fas fa-times"></i></button>
                </div>
                
                <div class="chat-body" id="chat-messages">
                    <!-- Messages ici -->
                </div>
                
                <div class="chat-footer">
                    <div class="chat-input-group">
                        <input type="text" id="chat-input" placeholder="Écrivez votre message...">
                        <button id="chat-send"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
        `;

        const container = document.createElement('div');
        container.innerHTML = html;
        document.body.appendChild(container);
    }

    attachEvents() {
        this.toggleBtn = document.getElementById('chatbot-toggle');
        this.window = document.getElementById('chatbot-window');
        this.messagesContainer = document.getElementById('chat-messages');
        this.input = document.getElementById('chat-input');
        this.sendBtn = document.getElementById('chat-send');
        this.badge = document.querySelector('.chat-badge');
        this.closeBtn = document.querySelector('.chat-close');

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

        if (this.isOpen) {
            this.hasUnread = false;
            this.badge.style.display = 'none';
            this.input.focus();
            this.scrollToBottom();

            // Son d'ouverture (si le système audio existe)
            if (window.audioSystem) window.audioSystem.playSound('click');
        }
    }

    notify() {
        if (!this.isOpen) {
            this.hasUnread = true;
            this.badge.style.display = 'flex';
            // Son de notification
            if (window.audioSystem) window.audioSystem.playSound('notification');
        }
    }

    async handleUserMessage() {
        const text = this.input.value.trim();
        if (!text) return;

        // Ajouter message utilisateur
        this.addUserMessage(text);
        this.input.value = '';

        // Simuler réflexion et répondre
        this.showTyping();

        try {
            const response = await this.findResponse(text);
            this.removeTyping();
            this.addBotMessage(response);
        } catch (error) {
            console.error('Error handling message:', error);
            this.removeTyping();
            this.addBotMessage("Désolé, une erreur s'est produite. Veuillez réessayer.");
        }
    }

    async findResponse(text) {
        // Try to get response from backend API first
        try {
            const response = await fetch('?controller=gamechatbot&action=handleRequest', {
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
            console.log('Chatbot API response:', data); // Debug log
            
            if (data.success && data.response) {
                return data.response;
            } else if (data.error) {
                console.error('Chatbot API error:', data.error);
                throw new Error(data.error);
            }
        } catch (error) {
            console.error('Error fetching chatbot response:', error);
            // Don't fall back immediately - show error message
            return "Désolé, je rencontre un problème technique. Veuillez réessayer dans un instant. 🔧";
        }
        
        // Fallback to local knowledge base if API fails completely
        const lowerText = text.toLowerCase();

        if (lowerText.includes('insc') || lowerText.includes('créer') || lowerText.includes('compte')) return this.knowledge.inscription;
        if (lowerText.includes('connect') || lowerText.includes('login')) return this.knowledge.connexion;
        if (lowerText.includes('mot de passe') || lowerText.includes('oubli') || lowerText.includes('perdu')) return this.knowledge.connexion;
        if (lowerText.includes('jeu') || lowerText.includes('jouer')) return this.knowledge.jeu;
        if (lowerText.includes('bonjour') || lowerText.includes('salut') || lowerText.includes('hello')) return this.knowledge.bonjour;
        if (lowerText.includes('contact') || lowerText.includes('email') || lowerText.includes('admin')) return this.knowledge.contact;
        if (lowerText.includes('bug') || lowerText.includes('probleme') || lowerText.includes('erreur')) return this.knowledge.bug;

        return this.knowledge.default;
    }

    addUserMessage(text) {
        const div = document.createElement('div');
        div.className = 'message user';
        div.textContent = text;
        this.messagesContainer.appendChild(div);
        this.scrollToBottom();
        if (window.audioSystem) window.audioSystem.playSound('click');
    }

    parseMessage(message) {
        // 1. Convert newlines to <br>
        let parsed = message.replace(/\n/g, '<br>');

        // 2. Convert **text** to <strong>text</strong>
        parsed = parsed.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

        // 3. Convert [text](url) to <a href="url">text</a>
        parsed = parsed.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" style="color: #00d1ff; text-decoration: underline;">$1</a>');

        return parsed;
    }

    addBotMessage(text) {
        const div = document.createElement('div');
        div.className = 'message bot';
        div.innerHTML = this.parseMessage(text); // Parse markdown
        this.messagesContainer.appendChild(div);
        this.scrollToBottom();

        // Son de réception
        if (window.audioSystem && this.isOpen) window.audioSystem.playSound('hover');
        else if (!this.isOpen) this.notify();
    }

    showOptions(options) {
        const div = document.createElement('div');
        div.className = 'chat-options';

        options.forEach(opt => {
            const btn = document.createElement('button');
            btn.className = 'chat-option-btn';
            btn.textContent = opt;
            btn.onclick = () => {
                this.input.value = opt;
                this.handleUserMessage();
            };
            div.appendChild(btn);
        });

        this.messagesContainer.appendChild(div);
        this.scrollToBottom();
    }

    showTyping() {
        const div = document.createElement('div');
        div.className = 'typing-indicator';
        div.id = 'typing-indicator';
        div.innerHTML = `
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        `;
        this.messagesContainer.appendChild(div);
        this.scrollToBottom();
    }

    removeTyping() {
        const el = document.getElementById('typing-indicator');
        if (el) el.remove();
    }

    scrollToBottom() {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }
}

// Initialiser le chatbot
document.addEventListener('DOMContentLoaded', () => {
    // Petit délai pour laisser le temps au reste de charger
    setTimeout(() => {
        window.gameBot = new GameBot();
    }, 500);
});
