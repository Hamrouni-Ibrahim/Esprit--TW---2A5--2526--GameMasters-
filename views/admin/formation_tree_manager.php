<?php 
$pageTitle = 'Gérer l\'Arbre de Compétences - ' . htmlspecialchars($result['title']);
$currentPage = 'formations';
include "views/admin/includes/header.php"; 
?>

<!-- Admin Content -->
<section class="admin-content">
    <div class="admin-bg"></div>
    <div class="admin-shapes">
        <div class="admin-shape shape1"></div>
        <div class="admin-shape shape2"></div>
        <div class="admin-shape shape3"></div>
        <div class="admin-shape shape4"></div>
        <div class="admin-shape shape5"></div>
        <div class="admin-shape shape6"></div>
    </div>
    <div class="admin-particles" id="adminParticles"></div>
    <div class="admin-container">
        <div class="admin-header-section">
            <div>
                <h2>Arbre de Compétences: <?php echo htmlspecialchars($result['title']); ?></h2>
                <p style="color: #a0a0a0; font-size: 14px; margin-top: 10px;">Définissez l'ordre d'apprentissage en sélectionnant un parent pour chaque éducation.</p>
            </div>
            <a href="?controller=formation&action=adminList" class="btn btn-secondary">Retour</a>
        </div>

        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success">
                La hiérarchie a été mise à jour avec succès !
            </div>
        <?php } ?>

        <div class="admin-form-container" style="max-width: 1200px; margin: 0 auto;">
            <!-- Formation Root Indicator -->
            <div style="background: linear-gradient(135deg, rgba(147, 51, 234, 0.2) 0%, rgba(232, 121, 249, 0.15) 100%); border: 2px solid rgba(232, 121, 249, 0.4); border-radius: 15px; padding: 20px; margin-bottom: 25px; text-align: center;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 15px;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #9333ea, #c084fc); display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; font-weight: bold; box-shadow: 0 4px 15px rgba(147, 51, 234, 0.4);">
                        📚
                    </div>
                    <div>
                        <h3 style="color: #e879f9; margin: 0; font-size: 20px; font-weight: 700;"><?php echo htmlspecialchars($result['title']); ?></h3>
                        <p style="color: #c084fc; margin: 5px 0 0 0; font-size: 14px;">Racine de l'arbre de compétences</p>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <form action="?controller=formation&action=manageTree&id=<?php echo $result['id']; ?>" method="POST">
                    <div class="admin-table-container">
                        <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Éducation (Enfant)</th>
                            <th>Difficulté</th>
                            <th>Parent (Prérequis)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Remove duplicates by id to prevent showing same education twice
                        $uniqueEducations = [];
                        $seenIds = [];
                        if (!empty($educations)) {
                            foreach ($educations as $edu) {
                                if (!in_array($edu['id'], $seenIds)) {
                                    $uniqueEducations[] = $edu;
                                    $seenIds[] = $edu['id'];
                                }
                            }
                        }
                        $educations = $uniqueEducations;
                        
                        if (empty($educations)) { ?>
                            <tr>
                                <td colspan="3" style="text-align: center;">Aucune éducation trouvée pour cette formation.</td>
                            </tr>
                        <?php } else { 
                            foreach ($educations as $edu) { ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($edu['title']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge"><?php echo htmlspecialchars($edu['difficulte']); ?></span>
                                    </td>
                                    <td>
                                        <select name="parents[<?php echo $edu['id']; ?>]" class="form-control">
                                            <option value="" <?php echo (empty($edu['parent_id'])) ? 'selected' : ''; ?>>-- <?php echo htmlspecialchars($result['title']); ?> (Racine) --</option>
                                            <?php 
                                            // Remove duplicates from parent options too
                                            $uniqueParents = [];
                                            $seenParentIds = [];
                                            foreach ($educations as $parent) {
                                                if (!in_array($parent['id'], $seenParentIds) && $parent['id'] != $edu['id']) {
                                                    $uniqueParents[] = $parent;
                                                    $seenParentIds[] = $parent['id'];
                                                }
                                            }
                                            foreach ($uniqueParents as $parent) {
                                                $selected = ($edu['parent_id'] == $parent['id']) ? 'selected' : '';
                                                echo '<option value="' . $parent['id'] . '" ' . $selected . '>' . htmlspecialchars($parent['title']) . '</option>';
                                            } ?>
                                        </select>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                        </tbody>
                        </table>
                    </div>
                    
                    <?php if (!empty($educations)) { ?>
                        <div class="form-actions" style="margin-top: 20px; text-align: right;">
                            <button type="submit" class="btn btn-primary">Enregistrer la Hiérarchie</button>
                        </div>
                    <?php } ?>
                </form>
            </div>
            
            <div class="card preview-section" style="margin-top: 30px;">
                <div class="preview-header">
                    <h3 class="preview-title">Aperçu de la Structure</h3>
                    <p class="preview-description">Voici comment l'arbre sera affiché aux utilisateurs :</p>
                </div>
                <div class="preview-container" style="padding: 20px; border-radius: 10px;">
                    <?php 
                    // Reuse the frontend tree view for preview
                    // We need to fetch the tree structure first
                    $educationModel = new Education();
                    $skillTree = $educationModel->getTreeByFormationId($result['id']);
                    include "views/front/skill_tree.php"; 
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Modern Dark Theme Styling - Integrated with Admin Template */
.card {
    background: linear-gradient(135deg, rgba(18, 18, 22, 0.7) 0%, rgba(15, 15, 18, 0.6) 100%);
    border: 1px solid rgba(232, 121, 249, 0.2);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: #fff;
    width: 100%;
    box-sizing: border-box;
}

/* Use admin-table styles from template */
.table,
.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.table th,
.admin-table th {
    color: #ffffff;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 1px;
    padding: 20px;
    text-align: left;
    background: linear-gradient(135deg, rgba(147, 51, 234, 0.9) 0%, rgba(232, 121, 249, 0.85) 100%);
    border-bottom: 2px solid rgba(232, 121, 249, 0.3);
}

.table td,
.admin-table td {
    background: rgba(15, 15, 18, 0.25);
    padding: 20px;
    vertical-align: middle;
    color: #e0e0e0;
    border-top: 1px solid rgba(255, 255, 255, 0.15);
}

.table tbody tr:nth-child(even) td,
.admin-table tbody tr:nth-child(even) td {
    background: rgba(18, 18, 22, 0.25);
}

.table tbody tr:hover td,
.admin-table tbody tr:hover td {
    background: rgba(147, 51, 234, 0.4) !important;
    transform: scale(1.01);
    transition: all 0.2s ease;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid rgba(232, 121, 249, 0.3);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.05);
    color: #fff;
    font-size: 14px;
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: #e879f9;
    background: rgba(255, 255, 255, 0.08);
    box-shadow: 0 0 20px rgba(232, 121, 249, 0.2);
}

.form-control option {
    background-color: rgba(15, 15, 18, 0.95);
    color: #fff;
    padding: 10px;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-primary {
    background: linear-gradient(135deg, #9333ea, #c084fc);
    border: none;
    padding: 12px 25px;
    border-radius: 10px;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    text-decoration: none;
    display: inline-block;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(147, 51, 234, 0.4);
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 12px 24px;
    border-radius: 10px;
    color: #fff;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
}

.alert-success {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.25), rgba(5, 150, 105, 0.2));
    border: 2px solid rgba(16, 185, 129, 0.6);
    color: #ffffff;
    padding: 18px 24px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3), 
                0 0 40px rgba(16, 185, 129, 0.15);
    font-weight: 600;
    font-size: 15px;
    position: relative;
    z-index: 10;
    animation: slideInDown 0.5s ease-out;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.alert-success::before {
    content: "✓";
    display: inline-block;
    width: 28px;
    height: 28px;
    background: linear-gradient(135deg, #10b981, #059669);
    border-radius: 50%;
    text-align: center;
    line-height: 28px;
    margin-right: 12px;
    font-weight: bold;
    box-shadow: 0 2px 10px rgba(16, 185, 129, 0.4);
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Preview section styling */
.preview-section {
    margin-top: 30px;
}

.preview-header {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(232, 121, 249, 0.2);
}

.preview-title {
    font-size: 24px;
    font-weight: 700;
    background: linear-gradient(135deg, #ffffff 0%, #e879f9 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 10px;
}

.preview-description {
    color: #c084fc;
    font-size: 14px;
    margin: 0;
    opacity: 0.9;
}

.preview-container {
    background: rgba(147, 51, 234, 0.05) !important;
    border: 1px solid rgba(232, 121, 249, 0.15) !important;
    box-shadow: inset 0 0 30px rgba(147, 51, 234, 0.1) !important;
    transition: all 0.3s ease;
}

.preview-container:hover {
    border-color: rgba(232, 121, 249, 0.25) !important;
    box-shadow: inset 0 0 40px rgba(147, 51, 234, 0.15), 0 4px 20px rgba(147, 51, 234, 0.2) !important;
}
</style>

<?php include "views/admin/includes/footer.php"; ?>
