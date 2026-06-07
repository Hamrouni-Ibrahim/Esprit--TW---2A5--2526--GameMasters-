<style>
    .project-details-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
        min-height: calc(100vh - 200px);
    }

    .project-header {
        margin-bottom: 40px;
    }

    .project-image-large {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
        border-radius: 15px;
        margin-bottom: 30px;
    }

    .project-category {
        display: inline-block;
        background: rgba(0, 255, 204, 0.15);
        color: #00ffcc;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 20px;
        border: 1px solid rgba(0, 255, 204, 0.3);
    }

    .project-title {
        font-size: 2.5em;
        font-weight: 700;
        color: #fff;
        margin-bottom: 20px;
        background: linear-gradient(135deg, #00ffcc, #00ccff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .project-description {
        color: #ccc;
        font-size: 16px;
        line-height: 1.8;
        margin-bottom: 30px;
    }

    .project-actions {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn-action-large {
        padding: 18px 35px;
        border: none;
        border-radius: 12px;
        font-size: 17px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        flex: 1;
        min-width: 200px;
        position: relative;
        overflow: hidden;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .btn-donate-large {
        background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
        background-size: 200% 200%;
        color: #ffffff;
        box-shadow: 
            0 4px 20px rgba(16, 185, 129, 0.4),
            0 0 0 1px rgba(5, 150, 105, 0.3) inset;
        animation: gradientShift 3s ease infinite;
    }

    .btn-donate-large::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }

    .btn-donate-large:hover::before {
        left: 100%;
    }

    .btn-donate-large:hover {
        transform: translateY(-4px);
        box-shadow: 
            0 10px 30px rgba(16, 185, 129, 0.6),
            0 0 0 1px rgba(5, 150, 105, 0.5) inset,
            0 0 40px rgba(16, 185, 129, 0.5);
        background-position: right center;
    }

    .btn-donate-large:active {
        transform: translateY(-2px);
    }

    .btn-back {
        padding: 14px 28px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
        color: #00ffcc;
        border: 2px solid rgba(0, 255, 204, 0.4);
        border-radius: 12px;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 15px rgba(0, 255, 204, 0.2);
        text-decoration: none;
        display: inline-block;
        font-size: 15px;
        font-weight: 600;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        letter-spacing: 0.5px;
    }

    .btn-back::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(0, 255, 204, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn-back:hover::before {
        left: 100%;
    }

    .btn-back:hover {
        transform: translateY(-3px);
        background: linear-gradient(135deg, rgba(0, 255, 204, 0.2) 0%, rgba(0, 204, 255, 0.15) 100%);
        border-color: rgba(0, 255, 204, 0.7);
        box-shadow: 
            0 8px 25px rgba(0, 255, 204, 0.4),
            0 0 30px rgba(0, 255, 204, 0.3);
        color: #4dd0e1;
    }

    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
</style>

<div class="project-details-container">
    <a href="?action=projects" class="btn-back" style="margin-bottom: 20px;">
        <span style="margin-right: 8px;">←</span>
        Retour aux projets
    </a>

    <div class="project-header">
        <?php if (!empty($project['image'])): ?>
            <img src="<?= htmlspecialchars($project['image']) ?>" alt="<?= htmlspecialchars($project['title']) ?>" class="project-image-large" onerror="this.src='public/images/logo.png'">
        <?php endif; ?>
        
        <span class="project-category"><?= htmlspecialchars($project['category']) ?></span>
        <h1 class="project-title"><?= htmlspecialchars($project['title']) ?></h1>
        <div class="project-description">
            <?= nl2br(htmlspecialchars($project['description'])) ?>
        </div>
        
        <div class="project-actions">
            <button class="btn-action-large btn-donate-large" onclick="window.location.href='?action=donation&project_id=<?= $project['id'] ?>'">
                <span style="margin-right: 10px; font-size: 20px;">💝</span>
                Faire un don pour ce projet
            </button>
        </div>
    </div>
</div>

