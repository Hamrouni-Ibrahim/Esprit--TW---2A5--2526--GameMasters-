<?php
$pageTitle = 'Ajouter une Catégorie - Game Master';
$currentPage = 'categories';
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
        <h2 style="font-size: 32px; background: linear-gradient(135deg, #ffffff 0%, #e879f9 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 30px;">Ajouter une Catégorie</h2>

        <div class="admin-form-container">
            <form method="POST" action="?controller=category&action=adminAdd" id="categoryForm">
                <div class="admin-form-group">
                    <label for="nom">Nom de la Catégorie *</label>
                    <input type="text" id="nom" name="nom" required placeholder="Entrez le nom de la catégorie">
                </div>

                <div class="form-actions">
                    <button type="submit" name="category" class="btn btn-primary">Ajouter la Catégorie</button>
                    <a href="?controller=category&action=adminList" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</section>

