<?php 
// rede_credenciada.php (VERSÃO COM FILTRO POR OPERADORA)

$page_title = 'Rede Credenciada - Administradora Plano A';
include 'partials/header.php'; 
include 'config/db.php';

// --- LÓGICA DE BUSCA E FILTRO APRIMORADA ---

// 1. Busca todas as OPERADORAS distintas que têm rede para popular o filtro
try {
    $operator_stmt = $conn->query("
        SELECT DISTINCT p.operadora 
        FROM planos p
        JOIN plan_network pn ON p.id = pn.plan_id
        WHERE p.operadora IS NOT NULL AND p.operadora != '' 
        ORDER BY p.operadora
    ");
    $all_operators = $operator_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch(PDOException $e) {
    $all_operators = [];
}

// Pega os filtros da URL
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$operator_filter = isset($_GET['operadora']) ? trim($_GET['operadora']) : '';
// (outros filtros como cidade, etc., podem continuar aqui)

// --- MONTAGEM DA QUERY SQL PRINCIPAL ---
$sql = "SELECT DISTINCT p.* FROM network_partners p";
$params = [];
$where_clauses = [];

// JOIN com as tabelas de ligação apenas se um filtro que depende delas for usado
if (!empty($operator_filter)) {
    $sql .= " JOIN plan_network pn ON p.id = pn.partner_id JOIN planos pl ON pn.plan_id = pl.id";
    $where_clauses[] = "pl.operadora = :operadora";
    $params[':operadora'] = $operator_filter;
}

if (!empty($search_term)) {
    $where_clauses[] = "(p.name LIKE :search OR p.specialty LIKE :search)";
    $params[':search'] = '%' . $search_term . '%';
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}

$sql .= " ORDER BY p.name";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao consultar o banco de dados: " . $e->getMessage());
}
?>

<main>
    <section class="rede-section">
        <div class="container-section">
            <h2>Nossa Rede Credenciada</h2>
            <p>Encontre hospitais, clínicas e especialistas perto de você.</p>

            <form action="rede_credenciada.php" method="GET" class="search-form">
                <select name="operadora" onchange="this.form.submit()">
                    <option value="">Filtrar por Operadora</option>
                    <?php foreach ($all_operators as $operator): ?>
                        <option value="<?= htmlspecialchars($operator) ?>" <?= ($operator_filter == $operator) ? 'selected' : '' ?>><?= htmlspecialchars($operator) ?></option>
                    <?php endforeach; ?>
                </select>

                <div class="search-input-group">
                    <input type="text" name="search" placeholder="Busque por nome..." value="<?= htmlspecialchars($search_term) ?>">
                    <button type="submit">Buscar</button>
                </div>
            </form>

            <div class="network-grid">
                <?php if (empty($partners)): ?>
                    <p class="no-results">Nenhum resultado encontrado para sua busca.</p>
                <?php else: ?>
                    <?php foreach ($partners as $partner): ?>
                        <div class="network-card">
                            <div class="card-header">
                                <span class="card-type-tag"><?= htmlspecialchars($partner['type']) ?></span>
                            </div>
                            <div class="card-body">
                                <h3><?= htmlspecialchars($partner['name']) ?></h3>
                                <?php if(!empty($partner['specialty'])): ?>
                                    <p class="card-info">
                                        <img src="img/icons/atendimento.svg" alt="Ícone de Especialidade">
                                        <span><?= htmlspecialchars($partner['specialty']) ?></span>
                                    </p>
                                <?php endif; ?>
                                <?php if(!empty($partner['address'])): ?>
                                    <p class="card-info">
                                        <img src="img/icons/tech.svg" alt="Ícone de Endereço">
                                        <span><?= htmlspecialchars($partner['address']) . ', ' . htmlspecialchars($partner['city']) ?></span>
                                    </p>
                                <?php endif; ?>
                                <?php if(!empty($partner['address'])): ?>
                                    <p class="card-info">
                                        <img src="img/icons/whats.png" alt="Ícone de Endereço">
                                        <span><?= htmlspecialchars($partner['phone']) . ', ' . htmlspecialchars($partner['phone']) ?></span>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <?php if(!empty($partner['phone'])): ?>
                                <div class="card-footer">
                                    <a href="https://wa.me/55<?= preg_replace('/\D/', '', $partner['phone']) ?>" target="_blank" class="btn-whatsapp-card">
                                        <img src="img/icons/whats.png" alt="Ícone do WhatsApp">
                                        <span><?= htmlspecialchars($partner['phone']) ?></span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'partials/footer.php'; ?>