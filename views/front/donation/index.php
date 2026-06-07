<style>
    .donation-container {
        max-width: 1200px;
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

    .form-section {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 40px;
        margin-bottom: 40px;
    }

    .section-title {
        text-align: center;
        margin-bottom: 30px;
        color: #00ffcc;
        font-size: 2em;
        font-weight: 600;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #00ffcc;
        font-weight: 500;
        font-size: 1em;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 12px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        color: #fff;
        font-size: 14px;
        transition: all 0.3s;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #00ffcc;
        box-shadow: 0 0 10px rgba(0, 255, 204, 0.3);
        background: rgba(255, 255, 255, 0.15);
    }

    .form-group select option {
        background: #1a1a2e;
        color: #fff;
        padding: 10px;
    }

    .btn-submit {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #4CAF50, #2E7D32);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 10px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
    }

    .success-message {
        background: rgba(76, 175, 80, 0.2);
        border: 1px solid #4CAF50;
        color: #4CAF50;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
    }

    .error-message {
        background: rgba(255, 77, 77, 0.2);
        border: 1px solid #ff4d4d;
        color: #ff4d4d;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
    }

    .data-table {
        overflow-x: auto;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        padding: 20px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    thead th {
        background: transparent;
        color: #00ffcc;
        text-transform: uppercase;
        font-size: 0.85em;
        letter-spacing: 1px;
        padding: 15px;
        border-bottom: 1px solid rgba(0, 255, 204, 0.3);
        text-align: left;
    }

    tbody tr {
        background: rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease;
    }

    tbody tr:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-2px);
    }

    tbody td {
        padding: 15px;
        color: #fff;
    }

    .amount-cell {
        font-weight: bold;
        color: #4CAF50;
        font-size: 1.1em;
    }

    .project-badge {
        display: inline-block;
        background: rgba(0, 255, 204, 0.15);
        color: #00ffcc;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85em;
        border: 1px solid rgba(0, 255, 204, 0.3);
    }

    .action-btn {
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-right: 5px;
        font-size: 16px;
    }

    .btn-edit {
        background: rgba(255, 193, 7, 0.2);
        color: #ffc107;
    }

    .btn-edit:hover {
        background: #ffc107;
        color: #000;
    }

    .btn-delete {
        background: rgba(255, 77, 77, 0.2);
        color: #ff4d4d;
    }

    .btn-delete:hover {
        background: #ff4d4d;
        color: #fff;
    }

    .search-form {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .search-form input {
        flex: 1;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    .search-form button {
        padding: 12px 30px;
        background: linear-gradient(135deg, #00ffcc, #00ccff);
        color: #0a0a0a;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s;
    }

    .search-form button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 255, 204, 0.3);
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: #1a1a2e;
        padding: 30px;
        border-radius: 15px;
        width: 90%;
        max-width: 500px;
        position: relative;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .close-modal {
        position: absolute;
        top: 15px;
        right: 20px;
        color: #fff;
        cursor: pointer;
        font-size: 24px;
        font-weight: bold;
    }

    .close-modal:hover {
        color: #00ffcc;
    }
</style>

<div class="donation-container">
    <div class="page-title">
        <h1>💝 Faire un Don</h1>
        <p style="color: #a0a0a0;">Soutenez nos projets et notre mission</p>
    </div>

    <?php if (isset($_SESSION['donation_success'])): ?>
        <div class="success-message">
            <?= htmlspecialchars($_SESSION['donation_success']) ?>
        </div>
        <?php unset($_SESSION['donation_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['donation_error'])): ?>
        <div class="error-message">
            <?= htmlspecialchars($_SESSION['donation_error']) ?>
        </div>
        <?php unset($_SESSION['donation_error']); ?>
    <?php endif; ?>

    <!-- Donation Form -->
    <?php 
    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['email'])):
    ?>
        <div class="form-section">
            <div style="background: rgba(255, 193, 7, 0.2); border: 1px solid #ffc107; color: #ffc107; padding: 20px; border-radius: 8px; text-align: center;">
                <p style="font-size: 1.1em; margin-bottom: 15px;">🔒 Vous devez être connecté pour faire un don</p>
                <a href="?action=login" style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #00ffcc, #00ccff); color: #0a0a0a; text-decoration: none; border-radius: 8px; font-weight: 700; transition: all 0.3s;">
                    Se connecter
                </a>
            </div>
        </div>
    <?php else: 
        $userName = $_SESSION['username'] ?? 'Utilisateur';
        $userEmail = $_SESSION['email'] ?? '';
    ?>
        <div class="form-section">
            <h3 class="section-title">Soutenir Nos Projets</h3>
            
            <!-- User Info Display -->
            <div style="background: rgba(0, 255, 204, 0.1); border: 1px solid rgba(0, 255, 204, 0.3); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <p style="color: #00ffcc; margin: 0 0 10px 0; font-weight: 600; font-size: 0.9em;">👤 Votre compte :</p>
                <p style="color: #fff; margin: 5px 0; font-size: 1em;"><strong>Nom :</strong> <?= htmlspecialchars($userName) ?></p>
                <p style="color: #fff; margin: 5px 0; font-size: 1em;"><strong>Email :</strong> <?= htmlspecialchars($userEmail) ?></p>
            </div>
            
            <?php if ($selectedProject): ?>
                <div style="background: rgba(0, 255, 204, 0.1); border: 1px solid rgba(0, 255, 204, 0.3); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <p style="color: #00ffcc; margin: 0; font-weight: 600;">💝 Vous faites un don pour :</p>
                    <p style="color: #fff; margin: 5px 0 0 0; font-size: 1.1em;"><?= htmlspecialchars($selectedProject['title']) ?></p>
                </div>
            <?php endif; ?>
            
            <form id="donationForm" method="POST" action="?action=donation_add">
                <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                
                <div class="form-group">
                    <label for="project_id">Projet</label>
                    <?php if ($selectedProjectId && $selectedProject): ?>
                        <div style="background: rgba(0, 255, 204, 0.1); border: 1px solid rgba(0, 255, 204, 0.3); padding: 12px; border-radius: 8px; margin-bottom: 10px;">
                            <p style="color: #00ffcc; margin: 0; font-weight: 600; font-size: 0.9em;">✓ Projet présélectionné :</p>
                            <p style="color: #fff; margin: 5px 0 0 0;"><?= htmlspecialchars($selectedProject['title']) ?></p>
                        </div>
                        <input type="hidden" name="project_id" value="<?= $selectedProjectId ?>">
                        <select id="project_id" name="project_id_override" onchange="updateProjectId(this.value)">
                            <option value="">-- Changer pour un autre projet --</option>
                            <option value="0">-- Don Général --</option>
                            <?php foreach ($projects as $project): ?>
                                <?php if ($project['id'] != $selectedProjectId): ?>
                                    <option value="<?= $project['id'] ?>">
                                        <?= htmlspecialchars($project['title']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <select id="project_id" name="project_id">
                            <option value="">-- Don Général --</option>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?= $project['id'] ?>">
                                    <?= htmlspecialchars($project['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
            
                <div class="form-group">
                    <label for="amount">Montant (€) *</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="1" placeholder="50" required>
                </div>
                
                <button type="submit" class="btn-submit">
                    💝 Faire un Don
                </button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Search Donations -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="form-section">
        <h3 class="section-title">Mes Donations</h3>
        
        <?php if (!empty($searchEmail) && $searchEmail === ($_SESSION['email'] ?? '')): ?>
            <div style="background: rgba(0, 255, 204, 0.1); border: 1px solid rgba(0, 255, 204, 0.3); padding: 12px; border-radius: 8px; margin-bottom: 15px;">
                <p style="color: #00ffcc; margin: 0; font-weight: 600; font-size: 0.9em;">📧 Affichage de vos donations pour : <?= htmlspecialchars($searchEmail) ?></p>
            </div>
        <?php endif; ?>
        
        <form method="GET" class="search-form">
            <input type="hidden" name="action" value="donation">
            <input type="email" name="search_email" value="<?= htmlspecialchars($searchEmail) ?>" placeholder="Votre email de recherche" required>
            <button type="submit" class="btn-search-modal" style="padding: 12px 25px; background: linear-gradient(135deg, #00ffcc, #00ccff); color: #0a0a0a; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: all 0.3s;">
                🔍 Rechercher
            </button>
        </form>
        
        <?php if (!empty($searchEmail) && !empty($userDonations)): ?>
            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Projet</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($userDonations as $donation): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($donation['created_at'])) ?></td>
                            <td class="amount-cell"><?= number_format($donation['amount'], 2, ',', ' ') ?>€</td>
                            <td>
                                <?php if (!empty($donation['project_title'])): ?>
                                    <span class="project-badge"><?= htmlspecialchars($donation['project_title']) ?></span>
                                <?php else: ?>
                                    <span style="color: #a0a0a0; font-style: italic;">Don Général</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button onclick="editDonation(<?= $donation['id'] ?>, '<?= htmlspecialchars(addslashes($donation['name'])) ?>', '<?= htmlspecialchars(addslashes($donation['email'])) ?>', <?= $donation['amount'] ?>, '<?= $donation['project_id'] ?? '' ?>')" class="action-btn btn-edit" title="Modifier">
                                    ✎
                                </button>
                                <button onclick="deleteDonation(<?= $donation['id'] ?>, '<?= htmlspecialchars(addslashes($searchEmail)) ?>')" class="action-btn btn-delete" title="Supprimer">
                                    ×
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif(!empty($searchEmail)): ?>
            <p style="text-align: center; color: #a0a0a0;">Aucune donation trouvée pour cet email.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Edit Donation Modal -->
<div id="editDonationModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeEditModal()">&times;</span>
        <h3 style="color: #00ffcc; text-align: center; margin-bottom: 20px;">Modifier la Donation</h3>
        
        <form id="editDonationForm" method="POST" action="?action=donation_update">
            <input type="hidden" id="edit_id" name="id">
            <input type="hidden" name="search_email_redirect" value="<?= htmlspecialchars($searchEmail) ?>">
            
            <?php if (isset($_SESSION['username']) && isset($_SESSION['email'])): ?>
                <input type="hidden" id="edit_name" name="name" value="<?= htmlspecialchars($_SESSION['username']) ?>">
                <input type="hidden" id="edit_email" name="email" value="<?= htmlspecialchars($_SESSION['email']) ?>">
                <div style="background: rgba(0, 255, 204, 0.1); border: 1px solid rgba(0, 255, 204, 0.3); padding: 12px; border-radius: 8px; margin-bottom: 15px;">
                    <p style="color: #00ffcc; margin: 0; font-weight: 600; font-size: 0.9em;">👤 Informations de votre compte :</p>
                    <p style="color: #fff; margin: 5px 0; font-size: 0.9em;"><strong>Nom :</strong> <?= htmlspecialchars($_SESSION['username']) ?></p>
                    <p style="color: #fff; margin: 5px 0; font-size: 0.9em;"><strong>Email :</strong> <?= htmlspecialchars($_SESSION['email']) ?></p>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label for="edit_name">Nom *</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email">Email *</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="edit_project_id">Projet</label>
                <select id="edit_project_id" name="project_id">
                    <option value="">-- Don Général --</option>
                    <?php foreach($projects as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="edit_amount">Montant (€) *</label>
                <input type="number" id="edit_amount" name="amount" step="0.01" min="1" required>
            </div>
            
            <button type="submit" class="btn-submit" style="background: linear-gradient(135deg, #ffc107, #ff9800);">Mettre à jour</button>
        </form>
    </div>
</div>

<script>
    function editDonation(id, name, email, amount, projectId) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_amount').value = amount;
        document.getElementById('edit_project_id').value = projectId || '';
        document.getElementById('editDonationModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editDonationModal').style.display = 'none';
    }

    function deleteDonation(id, searchEmail) {
        if(confirm('Êtes-vous sûr de vouloir supprimer cette donation ?')) {
            window.location.href = `?action=donation_delete&id=${id}&search_email_redirect=${encodeURIComponent(searchEmail)}`;
        }
    }

    // Function to update project_id when user changes the selection
    function updateProjectId(newProjectId) {
        const hiddenInput = document.querySelector('input[name="project_id"]');
        if (hiddenInput) {
            if (newProjectId === '0' || newProjectId === '') {
                hiddenInput.value = '';
            } else {
                hiddenInput.value = newProjectId;
            }
        } else {
            // If no hidden input, update the select directly
            const select = document.getElementById('project_id');
            if (select) {
                select.value = newProjectId === '0' ? '' : newProjectId;
            }
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('editDonationModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>

