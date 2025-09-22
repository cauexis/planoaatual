<?php 
// planos.php (versão com filtro de operadora)
include 'partials/header.php'; 
include 'config/db.php';

// --- LÓGICA DO FILTRO ---

// 1. Busca todas as categorias distintas para os botões de filtro
try {
    $category_stmt = $conn->query("SELECT DISTINCT tipo_plano FROM planos WHERE is_active = 1 AND tipo_plano IS NOT NULL AND tipo_plano != '' ORDER BY tipo_plano");
    $categories = $category_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch(PDOException $e) { $categories = []; }

// 2. Busca todas as operadoras distintas para os botões de filtro
try {
    $operator_stmt = $conn->query("SELECT DISTINCT operadora FROM planos WHERE is_active = 1 AND operadora IS NOT NULL AND operadora != '' ORDER BY operadora");
    $operators = $operator_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch(PDOException $e) { $operators = []; }

// 3. Pega os filtros ativos da URL
$selected_category = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$selected_acomodacao = isset($_GET['acomodacao']) ? $_GET['acomodacao'] : '';
$selected_operator = isset($_GET['operadora']) ? $_GET['operadora'] : '';

// 4. Monta a busca principal de planos dinamicamente
$sql = "SELECT id, nome_plano, operadora FROM planos WHERE is_active = 1";
$params = [];

// Adiciona os filtros à busca se eles estiverem ativos
if (!empty($selected_category)) {
    $sql .= " AND tipo_plano = :category";
    $params['category'] = $selected_category;
}
if (!empty($selected_acomodacao)) {
    $sql .= " AND nome_plano LIKE :acomodacao";
    $params['acomodacao'] = '%' . $selected_acomodacao . '%';
}
if (!empty($selected_operator)) {
    $sql .= " AND operadora = :operator";
    $params['operator'] = $selected_operator;
}

$sql .= " ORDER BY nome_plano";

try {
    $plan_stmt = $conn->prepare($sql);
    $plan_stmt->execute($params);
    $plans = $plan_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $plans = [];
    $error_message = "Erro ao carregar planos: " . $e->getMessage();
}
?>

<section>
    <div class="container-section">
        <h2>Encontre o Plano Ideal</h2>
        <p>Use os filtros abaixo para encontrar o plano que melhor se adapta às suas necessidades.</p>
        
        <div class="filter-group">
            <h4>Filtrar por Operadora:</h4>
            <div class="plan-filters">
                <a href="planos.php?categoria=<?= urlencode($selected_category) ?>&acomodacao=<?= urlencode($selected_acomodacao) ?>" class="<?= empty($selected_operator) ? 'active' : '' ?>">Todas as Operadoras</a>
                <?php foreach ($operators as $operator): ?>
                    <a href="planos.php?operadora=<?= urlencode($operator) ?>&categoria=<?= urlencode($selected_category) ?>&acomodacao=<?= urlencode($selected_acomodacao) ?>" class="<?= ($selected_operator == $operator) ? 'active' : '' ?>">
                        <?= htmlspecialchars($operator) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="filter-group">
            <h4>Filtrar por Categoria:</h4>
            <div class="plan-filters">
                <a href="planos.php?operadora=<?= urlencode($selected_operator) ?>&acomodacao=<?= urlencode($selected_acomodacao) ?>" class="<?= empty($selected_category) ? 'active' : '' ?>">Todas as Categorias</a>
                <?php foreach ($categories as $category): ?>
                    <a href="planos.php?categoria=<?= urlencode($category) ?>&operadora=<?= urlencode($selected_operator) ?>&acomodacao=<?= urlencode($selected_acomodacao) ?>" class="<?= ($selected_category == $category) ? 'active' : '' ?>">
                        <?= htmlspecialchars($category) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="filter-group">
            <h4>Filtrar por Acomodação:</h4>
            <div class="plan-filters">
                <a href="planos.php?operadora=<?= urlencode($selected_operator) ?>&categoria=<?= urlencode($selected_category) ?>" class="<?= empty($selected_acomodacao) ? 'active' : '' ?>">Todas as Acomodações</a>
                <a href="planos.php?operadora=<?= urlencode($selected_operator) ?>&categoria=<?= urlencode($selected_category) ?>&acomodacao=Apartamento" class="<?= ($selected_acomodacao == 'Apartamento') ? 'active' : '' ?>">Apartamento</a>
                <a href="planos.php?operadora=<?= urlencode($selected_operator) ?>&categoria=<?= urlencode($selected_category) ?>&acomodacao=Enfermaria" class="<?= ($selected_acomodacao == 'Enfermaria') ? 'active' : '' ?>">Enfermaria</a>
            </div>
        </div>
        
        <div class="planos-grid-container">
            <?php if (empty($plans)): ?>
                <p style="text-align: center; width: 100%; margin-top: 30px;">Nenhum plano encontrado para os filtros selecionados.</p>
            <?php else: ?>
                <?php foreach ($plans as $plan): ?>
                    <a href="plano_detalhe.php?id=<?= $plan['id'] ?>" class="plano-card">
                        <div class="plano-card-content">
                            <h3><?= htmlspecialchars($plan['nome_plano']) ?></h3>
                            <p class="operadora-tag"><?= htmlspecialchars($plan['operadora']) ?></p>
                        </div>
                        <span class="plano-card-footer">
                            Ver tabela de preços →
                        </span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php include 'partials/footer.php'; ?>