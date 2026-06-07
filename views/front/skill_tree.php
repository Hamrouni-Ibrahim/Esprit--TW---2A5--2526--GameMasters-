<?php if (!empty($skillTree)) { ?>
<div class="skill-tree-container">
    <h3 class="skill-tree-title">Arbre de Compétences</h3>
    <div class="skill-tree-wrapper">
        <?php 
        function renderTreeNode($node, $level = 0) {
            $hasChildren = !empty($node['children']);
            $statusClass = 'unlocked'; // Default to unlocked for now, logic can be added later
            ?>
            <div class="tree-node-wrapper level-<?php echo $level; ?>">
                <div class="tree-node <?php echo $statusClass; ?>" 
                     data-id="<?php echo $node['id']; ?>"
                     onclick="window.location.href='?controller=education&action=detail&id=<?php echo $node['id']; ?>'">
                    <div class="node-icon">
                        <?php if ($level == 0) { ?>
                            <span>🌱</span>
                        <?php } else { ?>
                            <span>🌿</span>
                        <?php } ?>
                    </div>
                    <div class="node-content">
                        <h4><?php echo htmlspecialchars($node['title']); ?></h4>
                        <div class="node-meta">
                            <span class="difficulty"><?php echo htmlspecialchars($node['difficulte']); ?></span>
                        </div>
                    </div>
                    <?php if ($hasChildren) { ?>
                        <div class="connector-line"></div>
                    <?php } ?>
                </div>
                
                <?php if ($hasChildren) { ?>
                    <div class="node-children">
                        <?php foreach ($node['children'] as $child) {
                            renderTreeNode($child, $level + 1);
                        } ?>
                    </div>
                <?php } ?>
            </div>
            <?php
        }

        foreach ($skillTree as $rootNode) {
            renderTreeNode($rootNode);
        }
        ?>
    </div>
</div>

<style>
.skill-tree-container {
    margin: 40px 0;
    padding: 30px;
    background: rgba(10, 14, 39, 0.6);
    border: 1px solid rgba(0, 255, 204, 0.1);
    border-radius: 20px;
    backdrop-filter: blur(10px);
}

.skill-tree-title {
    color: #00ffcc;
    margin-bottom: 30px;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 2px;
    text-shadow: 0 0 10px rgba(0, 255, 204, 0.3);
}

.skill-tree-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 40px;
    padding: 20px;
    overflow-x: auto;
}

.tree-node-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

.node-children {
    display: flex;
    gap: 40px;
    margin-top: 40px;
    position: relative;
}

/* Connecting lines */
.node-children::before {
    content: '';
    position: absolute;
    top: -20px;
    left: 50%;
    transform: translateX(-50%);
    width: 2px;
    height: 20px;
    background: linear-gradient(to bottom, #00ffcc, rgba(0, 255, 204, 0.3));
}

.tree-node {
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid rgba(0, 255, 204, 0.3);
    border-radius: 15px;
    padding: 15px;
    width: 200px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    z-index: 2;
}

.tree-node:hover {
    transform: translateY(-5px) scale(1.05);
    background: rgba(0, 255, 204, 0.1);
    border-color: #00ffcc;
    box-shadow: 0 0 20px rgba(0, 255, 204, 0.2);
}

.node-icon {
    font-size: 24px;
    margin-bottom: 10px;
    text-align: center;
}

.node-content h4 {
    color: #fff;
    font-size: 14px;
    margin: 0 0 5px 0;
    text-align: center;
}

.node-meta {
    display: flex;
    justify-content: center;
}

.difficulty {
    font-size: 10px;
    padding: 2px 8px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    color: #a0a0a0;
}

/* Connector line from parent to children container */
.connector-line {
    position: absolute;
    bottom: -40px;
    left: 50%;
    width: 2px;
    height: 40px;
    background: linear-gradient(to bottom, rgba(0, 255, 204, 0.3), rgba(0, 255, 204, 0.1));
    z-index: 1;
}
</style>
<?php } ?>
