<style>
    .projects-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 20px;
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
        min-height: calc(100vh - 200px);
    }

    .page-title {
        text-align: center;
        margin-bottom: 40px;
    }

    .page-title h1 {
        font-size: 2.5em;
        background: linear-gradient(135deg, #00ffcc, #00ccff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 10px;
    }

    .filters-section {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
    }

    .filters-row {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        align-items: end;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        color: #00ffcc;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 12px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        color: #fff;
        font-size: 14px;
    }

    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
        margin-top: 30px;
    }

    .project-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        flex-direction: column;
    }

    .project-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 255, 204, 0.2);
        border-color: #00ffcc;
    }

    .project-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #1a1a2e;
    }

    .project-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .project-category {
        display: inline-block;
        background: rgba(0, 255, 204, 0.15);
        color: #00ffcc;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 12px;
        border: 1px solid rgba(0, 255, 204, 0.3);
    }

    .project-title {
        font-size: 22px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 10px;
        line-height: 1.3;
    }

    .project-description {
        color: #b0bec5;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 15px;
        flex: 1;
    }

    .project-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn-action {
        padding: 12px 20px;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        flex: 1;
        min-width: 140px;
        position: relative;
        overflow: hidden;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-donate,
    .btn-view {
        background: linear-gradient(135deg, #00ffcc 0%, #00ccff 50%, #0099ff 100%);
        background-size: 200% 200%;
        color: #0a0a0a;
        box-shadow: 
            0 4px 15px rgba(0, 255, 204, 0.4),
            0 0 0 1px rgba(0, 204, 255, 0.3) inset;
        font-weight: 700;
        animation: gradientShift 3s ease infinite;
    }

    .btn-donate::before,
    .btn-view::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.5s ease;
    }

    .btn-donate:hover::before,
    .btn-view:hover::before {
        left: 100%;
    }

    .btn-donate:hover,
    .btn-view:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 
            0 8px 25px rgba(0, 255, 204, 0.6),
            0 0 0 1px rgba(0, 204, 255, 0.5) inset,
            0 0 30px rgba(0, 255, 204, 0.4);
        background-position: right center;
        color: #0a0a0a;
    }

    .btn-donate:active,
    .btn-view:active {
        transform: translateY(-1px);
    }

    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    /* Search Button */
    .btn-search {
        width: 100%;
        padding: 12px 25px;
        background: linear-gradient(135deg, #00ffcc 0%, #00ccff 50%, #0099ff 100%);
        background-size: 200% 200%;
        color: #0a0a0a;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        box-shadow: 
            0 4px 15px rgba(0, 255, 204, 0.4),
            0 0 0 1px rgba(0, 204, 255, 0.3) inset;
        animation: gradientShift 3s ease infinite;
    }

    .btn-search::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.5s ease;
    }

    .btn-search:hover::before {
        left: 100%;
    }

    .btn-search:hover {
        transform: translateY(-3px);
        box-shadow: 
            0 8px 25px rgba(0, 255, 204, 0.6),
            0 0 0 1px rgba(0, 204, 255, 0.5) inset,
            0 0 30px rgba(0, 255, 204, 0.4);
        background-position: right center;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #a0a0a0;
    }

    .empty-state h3 {
        color: #fff;
        margin-bottom: 10px;
    }

    .btn-ai-summary {
        background: linear-gradient(135deg, #9333ea 0%, #7c3aed 50%, #6d28d9 100%) !important;
        color: #ffffff !important;
        box-shadow: 
            0 4px 15px rgba(147, 51, 234, 0.4),
            0 0 0 1px rgba(124, 58, 237, 0.3) inset;
    }

    .btn-ai-summary:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 
            0 8px 25px rgba(147, 51, 234, 0.6),
            0 0 0 1px rgba(124, 58, 237, 0.5) inset,
            0 0 30px rgba(147, 51, 234, 0.4);
    }

    .ai-summary-container {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .share-container {
        position: relative;
    }

    .share-menu {
        position: absolute;
        bottom: 100%;
        left: 0;
        right: 0;
        background: rgba(26, 26, 46, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 255, 204, 0.3);
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 10px;
        z-index: 1000;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .share-option {
        width: 100%;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        text-align: left;
    }

    .share-option:last-child {
        margin-bottom: 0;
    }

    .share-option:hover {
        background: rgba(0, 255, 204, 0.1);
        border-color: rgba(0, 255, 204, 0.4);
        transform: translateX(5px);
    }

    .share-option span:first-child {
        font-size: 18px;
    }

    .btn-share {
        background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%) !important;
        color: #ffffff !important;
        box-shadow: 
            0 4px 15px rgba(16, 185, 129, 0.4),
            0 0 0 1px rgba(5, 150, 105, 0.3) inset;
    }

    .btn-share:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 
            0 8px 25px rgba(16, 185, 129, 0.6),
            0 0 0 1px rgba(5, 150, 105, 0.5) inset,
            0 0 30px rgba(16, 185, 129, 0.4);
    }
</style>

<script>
function toggleShareMenu(projectId, event) {
    event.stopPropagation();
    const menu = document.getElementById('share-menu-' + projectId);
    const allMenus = document.querySelectorAll('.share-menu');
    
    // Close all other menus
    allMenus.forEach(m => {
        if (m.id !== 'share-menu-' + projectId) {
            m.style.display = 'none';
        }
    });
    
    // Toggle current menu
    if (menu.style.display === 'none' || menu.style.display === '') {
        menu.style.display = 'block';
    } else {
        menu.style.display = 'none';
    }
}

// Close share menus when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.share-container')) {
        document.querySelectorAll('.share-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});

function getProjectUrl(projectId) {
    // Build relative URL like the project card uses
    const relativeUrl = '?action=project_details&id=' + projectId;
    // Return full URL for sharing
    return window.location.origin + window.location.pathname + relativeUrl;
}

function copyProjectLink(projectId, title, event) {
    // Stop event propagation to prevent card click
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }
    
    // Build relative URL (same format as project card)
    const relativeUrl = '?action=project_details&id=' + projectId;
    // Build full URL for copying to clipboard
    const fullUrl = window.location.origin + window.location.pathname + relativeUrl;
    
    // Copy the link to clipboard
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(fullUrl).then(() => {
            showNotification('Lien copié dans le presse-papiers !', 'success');
            // Close the menu after copying
            document.getElementById('share-menu-' + projectId).style.display = 'none';
        }).catch(err => {
            console.error('Erreur lors de la copie:', err);
            fallbackCopyToClipboard(fullUrl, projectId);
        });
    } else {
        fallbackCopyToClipboard(fullUrl, projectId);
    }
}

function fallbackCopyToClipboard(text, projectId) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showNotification('Lien copié dans le presse-papiers !', 'success');
        // Close the menu after copying
        if (projectId) {
            document.getElementById('share-menu-' + projectId).style.display = 'none';
        }
    } catch (err) {
        console.error('Erreur lors de la copie:', err);
        showNotification('Erreur lors de la copie. Veuillez copier manuellement: ' + text, 'error');
    }
    
    document.body.removeChild(textArea);
}

function shareOnFacebook(projectId, title) {
    const url = encodeURIComponent(getProjectUrl(projectId));
    const shareUrl = 'https://www.facebook.com/login.php?next=https%3A%2F%2Fwww.facebook.com%2Fsharer%2Fsharer.php%3Fu%3D' + url;
    window.open(shareUrl, '_blank', 'width=600,height=400');
    document.getElementById('share-menu-' + projectId).style.display = 'none';
}

function shareOnTwitter(projectId, title) {
    const url = encodeURIComponent(getProjectUrl(projectId));
    const text = encodeURIComponent(title);
    const shareUrl = 'https://twitter.com/i/flow/login?redirect_after_login=%2Fintent%2Ftweet%3Furl%3D' + url + '%26text%3D' + text;
    window.open(shareUrl, '_blank', 'width=600,height=400');
    document.getElementById('share-menu-' + projectId).style.display = 'none';
}

function showNotification(message, type) {
    // Remove existing notification if any
    const existing = document.getElementById('share-notification');
    if (existing) {
        existing.remove();
    }
    
    const notification = document.createElement('div');
    notification.id = 'share-notification';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #ef4444, #dc2626)'};
        color: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        z-index: 10000;
        font-weight: 600;
        animation: slideInRight 0.3s ease-out;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Add CSS animations for notification
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

async function generateAISummary(projectId, title, description, button) {
    const summaryContainer = document.getElementById('ai-summary-' + projectId);
    const summaryContent = document.getElementById('ai-summary-content-' + projectId);
    
    // Show container
    summaryContainer.style.display = 'block';
    
    // Disable button
    button.disabled = true;
    button.style.opacity = '0.6';
    button.style.cursor = 'not-allowed';
    
    try {
        // Use a relative URL
        const url = 'index.php?action=generate_project_summary';
        console.log('Requesting URL:', url);
        console.log('Sending data:', { project_id: projectId, title: title, description: description });
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                project_id: projectId,
                title: title,
                description: description
            })
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers.get('content-type'));
        
        if (!response.ok) {
            const text = await response.text();
            console.error('Error response text:', text);
            throw new Error('HTTP error! status: ' + response.status + ', response: ' + text.substring(0, 100));
        }
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Non-JSON response:', text);
            throw new Error('Réponse invalide du serveur. Réponse: ' + text.substring(0, 200));
        }
        
        const text = await response.text();
        console.log('Response text:', text);
        
        let data;
        try {
            data = JSON.parse(text);
            console.log('Response data:', data);
        } catch (e) {
            console.error('JSON parse error:', e);
            console.error('Response text that failed to parse:', text);
            throw new Error('Erreur de parsing JSON: ' + e.message);
        }
        
        if (data.success && data.summary) {
            summaryContent.innerHTML = '<p style="margin: 0;">' + data.summary.replace(/\n/g, '<br>') + '</p>';
        } else {
            const errorMsg = data.error || 'Erreur lors de la génération du résumé';
            summaryContent.innerHTML = '<p style="color: #ff6b6b; margin: 0;">' + errorMsg + '. Veuillez réessayer.</p>';
        }
    } catch (error) {
        console.error('Error generating AI summary:', error);
        summaryContent.innerHTML = '<p style="color: #ff6b6b; margin: 0;">Erreur de connexion: ' + error.message + '. Veuillez réessayer.</p>';
    } finally {
        // Re-enable button
        button.disabled = false;
        button.style.opacity = '1';
        button.style.cursor = 'pointer';
    }
}
</script>

<div class="projects-container">
    <div class="page-title">
        <h1>🌍 Nos Projets Internationaux</h1>
        <p style="color: #a0a0a0;">Découvrez nos projets et initiatives à travers le monde</p>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="?action=projects" class="filters-row">
            <input type="hidden" name="action" value="projects">
            <div class="filter-group">
                <label>Rechercher</label>
                <input type="text" name="search" placeholder="Rechercher un projet..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="filter-group">
                <label>Trier par</label>
                <select name="sort">
                    <option value="date_desc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'date_desc') ? 'selected' : '' ?>>Plus récents</option>
                    <option value="date_asc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'date_asc') ? 'selected' : '' ?>>Plus anciens</option>
                    <option value="alpha_asc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'alpha_asc') ? 'selected' : '' ?>>A-Z</option>
                    <option value="alpha_desc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'alpha_desc') ? 'selected' : '' ?>>Z-A</option>
                </select>
            </div>
            <div class="filter-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn-search">
                    <span style="margin-right: 8px;">🔍</span>
                    Rechercher
                </button>
            </div>
        </form>
    </div>

    <!-- Projects Grid -->
    <div class="projects-grid">
        <?php if (empty($projects)): ?>
            <div class="empty-state" style="grid-column: 1 / -1;">
                <h3>Aucun projet trouvé</h3>
                <p>Essayez de modifier vos critères de recherche</p>
            </div>
        <?php else: ?>
            <?php foreach($projects as $project): 
                $imagePath = !empty($project['image']) ? $project['image'] : 'public/images/logo.png';
            ?>
            <div class="project-card" onclick="window.location.href='?action=project_details&id=<?= $project['id'] ?>'">
                <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($project['title']) ?>" class="project-image" onerror="this.src='public/images/logo.png'">
                
                <div class="project-content">
                    <span class="project-category"><?= htmlspecialchars($project['category']) ?></span>
                    <h3 class="project-title"><?= htmlspecialchars($project['title']) ?></h3>
                    <p class="project-description">
                        <?= htmlspecialchars(substr($project['description'], 0, 120)) ?><?= strlen($project['description']) > 120 ? '...' : '' ?>
                    </p>
                    
                    <div class="project-actions" onclick="event.stopPropagation()">
                        <button class="btn-action btn-view" onclick="window.location.href='?action=project_details&id=<?= $project['id'] ?>'">
                            <span>📖</span>
                            <span>Voir plus</span>
                        </button>
                        <button class="btn-action btn-donate" onclick="window.location.href='?action=donation&project_id=<?= $project['id'] ?>'">
                            <span>💝</span>
                            <span>Faire un don</span>
                        </button>
                        <div class="share-container" style="position: relative; width: 100%; margin-top: 10px;">
                            <button class="btn-action btn-share" onclick="toggleShareMenu(<?= $project['id'] ?>, event)" style="background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%); color: #ffffff; width: 100%;">
                                <span>🔗</span>
                                <span>Partager</span>
                            </button>
                            <div id="share-menu-<?= $project['id'] ?>" class="share-menu" style="display: none;">
                                <button class="share-option" onclick="copyProjectLink(<?= $project['id'] ?>, '<?= htmlspecialchars($project['title'], ENT_QUOTES) ?>', event)">
                                    <span>📋</span>
                                    <span>Copier le lien</span>
                                </button>
                                <button class="share-option" onclick="shareOnFacebook(<?= $project['id'] ?>, '<?= htmlspecialchars($project['title'], ENT_QUOTES) ?>')">
                                    <span>📘</span>
                                    <span>Facebook</span>
                                </button>
                                <button class="share-option" onclick="shareOnTwitter(<?= $project['id'] ?>, '<?= htmlspecialchars($project['title'], ENT_QUOTES) ?>')">
                                    <span>🐦</span>
                                    <span>X (Twitter)</span>
                                </button>
                            </div>
                        </div>
                        <button class="btn-action btn-ai-summary" onclick="generateAISummary(<?= $project['id'] ?>, '<?= htmlspecialchars(addslashes($project['title'])) ?>', '<?= htmlspecialchars(addslashes($project['description'])) ?>', this)" style="background: linear-gradient(135deg, #9333ea 0%, #7c3aed 50%, #6d28d9 100%); color: #ffffff; margin-top: 10px; width: 100%;">
                            <span>🤖</span>
                            <span>Résumer avec IA</span>
                        </button>
                    </div>
                    <div id="ai-summary-<?= $project['id'] ?>" class="ai-summary-container" style="display: none; margin-top: 15px; padding: 15px; background: rgba(147, 51, 234, 0.1); border: 1px solid rgba(147, 51, 234, 0.3); border-radius: 10px;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; color: #a78bfa;">
                            <span>🤖</span>
                            <strong style="color: #c084fc;">Résumé IA</strong>
                        </div>
                        <div id="ai-summary-content-<?= $project['id'] ?>" style="color: rgba(255, 255, 255, 0.9); font-size: 14px; line-height: 1.6;">
                            <div class="loading-spinner" style="text-align: center; padding: 20px;">
                                <div style="display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(147, 51, 234, 0.3); border-top-color: #9333ea; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                                <p style="margin-top: 10px; color: #a78bfa;">Génération du résumé...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

