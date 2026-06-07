<?php
// This file is included by the controller, so it doesn't need full HTML structure
// The header and footer are included by the controller

// Ensure variables are set to avoid errors if controller doesn't provide them
if (!isset($donations)) $donations = [];
if (!isset($donationStats)) $donationStats = [
    'total_amount' => 0,
    'total_donations' => 0,
    'donations_per_project' => []
];
if (!isset($projects)) $projects = []; // For the project dropdown in stats
?>

<section class="admin-content">
    <div class="admin-bg"></div>
    <div class="admin-container">
        <div class="admin-header-section">
            <h2>💝 Gestion des Donations</h2>
            <p style="color: #a0a0a0; font-size: 14px;">Gérer toutes les donations reçues</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card" style="--card-accent: #4caf50;">
                <div class="stat-header">
                    <div class="stat-icon">💰</div>
                    <div class="stat-title">Total Collecté</div>
                </div>
                <div class="stat-value"><?= number_format($donationStats['total_amount'] ?? 0, 2, ',', ' ') ?>€</div>
            </div>
            <div class="stat-card" style="--card-accent: #2196f3;">
                <div class="stat-header">
                    <div class="stat-icon">👥</div>
                    <div class="stat-title">Nbr. Dons</div>
                </div>
                <div class="stat-value"><?= $donationStats['total_donations'] ?? 0 ?></div>
            </div>
            <div class="stat-card" style="--card-accent: #9c27b0;">
                <div class="stat-header">
                    <div class="stat-icon">📊</div>
                    <div class="stat-title">Moyenne</div>
                </div>
                <div class="stat-value"><?= $donationStats['total_donations'] > 0 ? number_format($donationStats['total_amount'] / $donationStats['total_donations'], 2, ',', ' ') : '0,00' ?>€</div>
            </div>
        </div>

        <!-- Donations List -->
        <div class="admin-card">
            <h3>Historique des Donations</h3>
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Projet lié</th>
                            <th>Montant</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($donations)): ?>
                            <tr><td colspan="6" style="text-align:center;">Aucune donation pour le moment.</td></tr>
                        <?php else: ?>
                            <?php foreach($donations as $d): 
                                // Check if user exists (has user_id)
                                $hasUser = !empty($d['user_id']);
                                $userId = $d['user_id'] ?? null;
                                $username = $d['username'] ?? $d['name'];
                                $userEmail = $d['email'];
                                $userRole = $d['role'] ?? 'player';
                                $userStatus = $d['status'] ?? 'active';
                                $userCreatedAt = $d['user_created_at'] ?? date('Y-m-d');
                            ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--accent-cyan);"><?= htmlspecialchars($d['name']) ?></strong>
                                </td>
                                <td style="color: #a0a0a0;">
                                    <?= htmlspecialchars($d['email']) ?>
                                </td>
                                <td>
                                    <?php if (!empty($d['project_title'])): ?>
                                        <span class="badge badge-cat"><?= htmlspecialchars($d['project_title']) ?></span>
                                    <?php else: ?>
                                        <span style="opacity:0.5;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td style="color: #4caf50; font-weight: bold;"><?= number_format($d['amount'], 2, ',', ' ') ?>€</td>
                                <td><?= date('d/m/Y H:i', strtotime($d['created_at'])) ?></td>
                                <td>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <button class="btn-admin btn-edit btn-view-donation" 
                                                data-donation-id="<?= $d['id'] ?>"
                                                data-donation-nom="<?= htmlspecialchars($d['name'], ENT_QUOTES) ?>"
                                                data-donation-email="<?= htmlspecialchars($d['email'], ENT_QUOTES) ?>"
                                                data-donation-montant="<?= number_format($d['amount'], 2, ',', ' ') ?>"
                                                data-donation-projet="<?= htmlspecialchars($d['project_title'] ?? 'Don Général', ENT_QUOTES) ?>"
                                                data-donation-date="<?= date('d/m/Y H:i', strtotime($d['created_at'])) ?>"
                                                data-donation-user-id="<?= $userId ?? '' ?>"
                                                data-donation-username="<?= htmlspecialchars($username ?? '', ENT_QUOTES) ?>"
                                                data-donation-user-role="<?= htmlspecialchars($userRole ?? '', ENT_QUOTES) ?>"
                                                data-donation-user-status="<?= htmlspecialchars($userStatus ?? '', ENT_QUOTES) ?>"
                                                data-donation-user-registration="<?= $userId ? date('d/m/Y', strtotime($userCreatedAt)) : '' ?>"
                                                title="Voir les détails" style="min-width: 40px; height: 40px; padding: 0;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="?action=admin_donation_delete&id=<?= $d['id'] ?>" class="btn-admin btn-delete" onclick="return confirm('Confirmer la suppression ?')" title="Supprimer" style="min-width: 40px; height: 40px; padding: 0;">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Donations per Project -->
        <?php if (!empty($donationStats['donations_per_project'])): ?>
        <div class="admin-card">
            <h3>Donations par Projet</h3>
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Projet</th>
                            <th>Nombre de Dons</th>
                            <th>Total Collecté</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donationStats['donations_per_project'] as $project): ?>
                        <tr>
                            <td><?= htmlspecialchars($project['title'] ?? 'Don Général') ?></td>
                            <td><?= $project['count'] ?></td>
                            <td style="color: #4caf50; font-weight: bold;"><?= number_format($project['total'] ?? 0, 2, ',', ' ') ?>€</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal Détails Donation -->
<div id="donationDetailsModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h2><i class="fas fa-donate" style="margin-right: 10px;"></i> Détails de la Donation</h2>
            <button class="close-btn" onclick="hideModal('donationDetailsModal')"><i class="fas fa-times"></i></button>
        </div>
        <div id="donationDetailsContent">
            <!-- Le contenu sera chargé dynamiquement -->
        </div>
    </div>
</div>

<!-- Modal Détails du Profil Utilisateur (réutilisée depuis users.php) -->
<div id="userProfileModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h2><i class="fas fa-user" style="margin-right: 10px;"></i> Profil Utilisateur</h2>
            <button class="close-btn" onclick="hideModal('userProfileModal')"><i class="fas fa-times"></i></button>
        </div>
        <div id="userProfileContent">
            <!-- Le contenu sera chargé dynamiquement -->
        </div>
    </div>
</div>

<script>
// Load user profile data from users page if available, otherwise fetch via AJAX
function fetchUserProfileData(userId, callback) {
    // Try to use global function if available from users page
    if (typeof window.viewUserProfile === 'function' && typeof window.userDataFromPHP !== 'undefined') {
        const userData = window.userDataFromPHP.find(u => u.id == userId);
        if (userData) {
            callback(userData);
            return;
        }
    }
    
    // Otherwise fetch via AJAX
    fetch(`?action=get_user_data&id=${userId}`)
        .then(response => {
            // Check if response is OK and content type is JSON
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                return response.text().then(text => {
                    console.error('Expected JSON but got:', text.substring(0, 200));
                    throw new Error('Response is not JSON. Server returned: ' + text.substring(0, 100));
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.user) {
                callback(data.user);
            } else {
                console.error('Error fetching user data:', data.error);
                alert('Erreur lors du chargement des données utilisateur: ' + (data.error || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors du chargement des données utilisateur. Veuillez vérifier la console pour plus de détails.');
        });
}

function viewUserProfile(userId, username, email, role, status, registration, hasProfile) {
    // First try to get full user data
    fetchUserProfileData(userId, function(userData) {
        // Use the full viewUserProfile function if available, otherwise create a simplified version
        if (typeof window.viewUserProfile === 'function') {
            // Call the function from users.php if available
            window.viewUserProfile(userId, username, email, role, status, registration, hasProfile);
        } else {
            // Create simplified version
            showSimpleUserProfile(userId, username, email, role, status, registration, userData);
        }
    });
}

function showSimpleUserProfile(userId, username, email, role, status, registration, userData) {
    const profile = userData?.profile || {};
    const userProfile = {
        firstName: profile.first_name || username,
        lastName: profile.last_name || "",
        discord: profile.discord || "Non renseigné",
        country: profile.country || "Non renseigné",
        nationality: profile.nationality || "Non renseigné",
        gender: profile.gender || "Non renseigné",
        birthDate: profile.birth_date ? new Date(profile.birth_date).toLocaleDateString('fr-FR') : "Non renseigné",
        careerLevel: profile.career_level || "Non renseigné",
        expertise: profile.expertise || "Non renseigné",
        techStack: profile.tech_stack || "Non renseigné",
        timezone: profile.timezone || "Europe/Paris",
        bio: profile.bio || profile.description || "Aucune biographie renseignée.",
        avatar: userData?.avatar || '',
    };

    // Build avatar
    const hash = username.split('').reduce((acc, char) => char.charCodeAt(0) + ((acc << 5) - acc), 0);
    const bgColor = '#' + Math.floor(Math.abs(Math.sin(hash) * 16777215)).toString(16).padStart(6, '0');
    const letter = username.charAt(0).toUpperCase();
    
    let avatarHtml = '';
    if (userProfile.avatar && userProfile.avatar.trim() !== '') {
        let cleanAvatarUrl = userProfile.avatar.trim();
        cleanAvatarUrl = cleanAvatarUrl.replace(/^\/+/, '');
        if (cleanAvatarUrl.indexOf('public/') !== 0) {
            cleanAvatarUrl = 'public/' + cleanAvatarUrl;
        }
        avatarHtml = `
            <div style="position: relative; display: inline-block;">
                <img src="${cleanAvatarUrl}" alt="${username}" 
                     style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(0, 255, 204, 0.5); box-shadow: 0 0 20px rgba(0, 255, 204, 0.3); display: block;"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div style="display: none; width: 120px; height: 120px; border-radius: 50%; background-color: ${bgColor}; color: #fff; align-items: center; justify-content: center; font-weight: bold; font-size: 48px; border: 3px solid rgba(0, 255, 204, 0.5); box-shadow: 0 0 20px rgba(0, 255, 204, 0.3); position: absolute; top: 0; left: 0;">
                    ${letter}
                </div>
            </div>
        `;
    } else {
        avatarHtml = `<div style="width: 120px; height: 120px; border-radius: 50%; background-color: ${bgColor}; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 48px; color: #fff; border: 3px solid rgba(0, 255, 204, 0.5); box-shadow: 0 0 20px rgba(0, 255, 204, 0.3); margin: 0 auto;">${letter}</div>`;
    }

    const content = `
        <div style="text-align: center; margin-bottom: 30px;">
            ${avatarHtml}
            <h3 style="color: var(--accent-cyan); margin: 20px 0 10px 0;">
                ${userProfile.firstName} ${userProfile.lastName || ''}
            </h3>
            <p style="color: var(--text-secondary); margin-bottom: 5px;">@${username}</p>
            <p style="color: var(--text-dim); font-size: 14px;">${email}</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 30px;">
            <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px;">
                <h4 style="color: var(--accent-blue); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-info-circle"></i> Informations du Compte
                </h4>
                <div style="display: grid; gap: 12px;">
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Rôle</strong>
                        <span class="status-badge ${role === 'admin' ? 'status-active' : role === 'moderator' ? 'status-pending' : 'status-inactive'}" style="font-size: 11px; padding: 4px 8px; text-transform: capitalize;">
                            ${role === 'admin' ? '👑 Administrateur' : role === 'moderator' ? '🛡️ Modérateur' : '🎮 Joueur'}
                        </span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Statut</strong>
                        <span class="status-badge ${status === 'active' ? 'status-active' : status === 'pending' ? 'status-pending' : status === 'banned' ? 'status-banned' : 'status-inactive'}" style="font-size: 11px; padding: 4px 8px; text-transform: capitalize;">
                            ${status === 'active' ? '✅ Actif' : status === 'pending' ? '⏳ En attente' : status === 'banned' ? '🚫 Banni' : '⏸️ Inactif'}
                        </span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Date d'inscription</strong>
                        <span style="color: var(--text-secondary);">${registration}</span>
                    </div>
                </div>
            </div>

            <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px;">
                <h4 style="color: var(--accent-green); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-user"></i> Informations Personnelles
                </h4>
                <div style="display: grid; gap: 12px;">
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Pays</strong>
                        <span style="color: var(--text-secondary);">${userProfile.country}</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Nationalité</strong>
                        <span style="color: var(--text-secondary);">${userProfile.nationality}</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Genre</strong>
                        <span style="color: var(--text-secondary);">${userProfile.gender}</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 15px; justify-content: center; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 25px;">
            <a href="?action=admin_users&search=${encodeURIComponent(email)}" class="btn-admin btn-edit">
                <i class="fas fa-external-link-alt" style="margin-right: 8px;"></i> Voir dans Utilisateurs
            </a>
            <button class="btn-admin btn-edit" onclick="hideModal('userProfileModal')">
                <i class="fas fa-times" style="margin-right: 8px;"></i> Fermer
            </button>
        </div>
    `;

    const profileContent = document.getElementById('userProfileContent');
    if (profileContent) {
        profileContent.innerHTML = content;
        showModal('userProfileModal');
    }
}

function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.cssText = 'display: flex !important; visibility: visible !important; opacity: 1 !important;';
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.cssText = 'display: none !important;';
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// View donation details
function viewDonationDetails(id, nom, email, montant, projet, date, userId, username, userRole, userStatus, userRegistration) {
    const content = `
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #10b981 0%, #34d399 50%, #6ee7b7 100%); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 48px; color: #fff; border: 3px solid rgba(16, 185, 129, 0.5); box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); margin: 0 auto;">
                💝
            </div>
            <h3 style="color: var(--accent-cyan); margin: 20px 0 10px 0;">${nom}</h3>
            <p style="color: var(--text-secondary); margin-bottom: 10px;">${email}</p>
            <div style="display: inline-block; padding: 12px 30px; background: rgba(16, 185, 129, 0.2); border: 2px solid rgba(16, 185, 129, 0.5); border-radius: 25px; font-size: 24px; font-weight: 700; color: #10b981;">
                ${montant}€
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 30px;">
            <!-- Informations de la donation -->
            <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px;">
                <h4 style="color: var(--accent-blue); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-info-circle"></i> Informations de la Donation
                </h4>
                <div style="display: grid; gap: 12px;">
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">ID Donation</strong>
                        <span style="color: var(--text-secondary);">#${id}</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Montant</strong>
                        <span style="color: #10b981; font-weight: 700; font-size: 18px;">${montant}€</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Date</strong>
                        <span style="color: var(--text-secondary);">${date}</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Email</strong>
                        <span style="color: var(--text-secondary); word-break: break-all;">${email}</span>
                    </div>
                </div>
            </div>

            <!-- Informations du projet -->
            <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px;">
                <h4 style="color: var(--accent-purple); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-project-diagram"></i> Projet
                </h4>
                <div style="display: grid; gap: 12px;">
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Projet lié</strong>
                        <span style="color: var(--text-secondary);">${projet}</span>
                    </div>
                    ${userId ? `
                    <div>
                        <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Donateur</strong>
                        <span style="color: var(--text-secondary);">${username || nom}</span>
                    </div>
                    ` : ''}
                </div>
            </div>
        </div>

        ${userId ? `
        <div style="background: rgba(42, 42, 42, 0.3); padding: 20px; border-radius: 12px; margin-bottom: 30px;">
            <h4 style="color: var(--accent-green); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-user"></i> Informations du Donateur
            </h4>
            <div style="display: grid; gap: 12px;">
                <div>
                    <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Nom d'utilisateur</strong>
                    <span style="color: var(--text-secondary);">${username}</span>
                </div>
                <div>
                    <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Rôle</strong>
                    <span class="status-badge ${userRole === 'admin' ? 'status-active' : userRole === 'moderator' ? 'status-pending' : 'status-inactive'}" style="font-size: 11px; padding: 4px 8px; text-transform: capitalize;">
                        ${userRole === 'admin' ? '👑 Administrateur' : userRole === 'moderator' ? '🛡️ Modérateur' : '🎮 Joueur'}
                    </span>
                </div>
                <div>
                    <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Statut</strong>
                    <span class="status-badge ${userStatus === 'active' ? 'status-active' : userStatus === 'pending' ? 'status-pending' : userStatus === 'banned' ? 'status-banned' : 'status-inactive'}" style="font-size: 11px; padding: 4px 8px; text-transform: capitalize;">
                        ${userStatus === 'active' ? '✅ Actif' : userStatus === 'pending' ? '⏳ En attente' : userStatus === 'banned' ? '🚫 Banni' : '⏸️ Inactif'}
                    </span>
                </div>
                ${userRegistration ? `
                <div>
                    <strong style="color: var(--text-primary); display: block; margin-bottom: 5px;">Date d'inscription</strong>
                    <span style="color: var(--text-secondary);">${userRegistration}</span>
                </div>
                ` : ''}
            </div>
        </div>
        ` : ''}

        <!-- Actions -->
        <div style="display: flex; gap: 15px; justify-content: center; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 25px;">
            <button class="btn-admin btn-edit" onclick="hideModal('donationDetailsModal')">
                <i class="fas fa-times" style="margin-right: 8px;"></i> Fermer
            </button>
        </div>
    `;

    const detailsContent = document.getElementById('donationDetailsContent');
    if (detailsContent) {
        detailsContent.innerHTML = content;
        showModal('donationDetailsModal');
    }
}

// Make functions globally available
window.viewUserProfile = viewUserProfile;
window.viewDonationDetails = viewDonationDetails;
window.showModal = showModal;
window.hideModal = hideModal;

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Event listeners for View Donation buttons
    document.querySelectorAll('.btn-view-donation').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const id = this.getAttribute('data-donation-id');
            const nom = this.getAttribute('data-donation-nom');
            const email = this.getAttribute('data-donation-email');
            const montant = this.getAttribute('data-donation-montant');
            const projet = this.getAttribute('data-donation-projet');
            const date = this.getAttribute('data-donation-date');
            const userId = this.getAttribute('data-donation-user-id');
            const username = this.getAttribute('data-donation-username');
            const userRole = this.getAttribute('data-donation-user-role');
            const userStatus = this.getAttribute('data-donation-user-status');
            const userRegistration = this.getAttribute('data-donation-user-registration');
            
            if (typeof viewDonationDetails === 'function') {
                viewDonationDetails(id, nom, email, montant, projet, date, userId, username, userRole, userStatus, userRegistration);
            } else if (typeof window.viewDonationDetails === 'function') {
                window.viewDonationDetails(id, nom, email, montant, projet, date, userId, username, userRole, userStatus, userRegistration);
            }
        });
    });

    // View User buttons removed - only "Voir les détails" is available now

    // Close modal buttons
    document.querySelectorAll('.close-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const modal = this.closest('.modal');
            if (modal) {
                const modalId = modal.id;
                if (typeof hideModal === 'function') {
                    hideModal(modalId);
                } else if (typeof window.hideModal === 'function') {
                    window.hideModal(modalId);
                }
            }
        });
    });

    // Close modal when clicking outside
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                if (typeof hideModal === 'function') {
                    hideModal(this.id);
                } else if (typeof window.hideModal === 'function') {
                    window.hideModal(this.id);
                }
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

