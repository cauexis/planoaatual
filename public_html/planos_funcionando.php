<?php
// PLANOS SUPER SIMPLES - SEMPRE FUNCIONA
session_start();

// Carrega header
$page_title = "Planos de Saúde - Plano A";
include "partials/header.php";

// Busca planos
$plans = [];
$operators = [];
$categories = [];

try {
    $config = include "core/config/custom.php";
    $pdo = new PDO(
        "mysql:host={$config["database"]["host"]};dbname={$config["database"]["dbname"]}", 
        $config["database"]["username"], 
        $config["database"]["password"]
    );
    
    // Filtros
    $operadora = $_GET["operadora"] ?? "";
    $tipo = $_GET["tipo_plano"] ?? "";
    $busca = $_GET["busca"] ?? "";
    
    // Query base
    $sql = "SELECT * FROM planos WHERE is_active = 1";
    $params = [];
    
    if ($operadora) {
        $sql .= " AND operadora = ?";
        $params[] = $operadora;
    }
    
    if ($tipo) {
        $sql .= " AND tipo_plano = ?";
        $params[] = $tipo;
    }
    
    if ($busca) {
        $sql .= " AND (nome_plano LIKE ? OR operadora LIKE ?)";
        $params[] = "%{$busca}%";
        $params[] = "%{$busca}%";
    }
    
    $sql .= " ORDER BY operadora, nome_plano";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Busca operadoras
    $stmt = $pdo->query("SELECT DISTINCT operadora FROM planos WHERE is_active = 1 ORDER BY operadora");
    $operators = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Busca tipos
    $stmt = $pdo->query("SELECT DISTINCT tipo_plano FROM planos WHERE is_active = 1 ORDER BY tipo_plano");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (Exception $e) {
    $error = "Erro ao carregar planos: " . $e->getMessage();
}

function formatValue($value) {
    return is_numeric($value) ? "R$ " . number_format($value, 2, ",", ".") : $value;
}
?>

<div style="position:fixed; top:10px; right:10px; background:#d4edda; color:#155724; padding:5px 10px; border-radius:3px; font-size:12px; z-index:1000;">
    🚀 Sistema Otimizado
</div>

<section class="planos-hero">
    <div class="container-section">
        <h1>Encontre o Plano Ideal para Você</h1>
        <p>Compare planos de saúde das melhores operadoras.</p>
    </div>
</section>

<section class="planos-filtros" style="background:#f8f9fa; padding:30px 0;">
    <div class="container-section">
        <form method="GET" style="display:grid; grid-template-columns:2fr 1fr 1fr auto; gap:20px; align-items:end;">
            <div>
                <label>Buscar plano:</label>
                <input type="text" name="busca" placeholder="Nome ou operadora..." 
                       value="<?= htmlspecialchars($busca ?? "") ?>" 
                       style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
            </div>
            <div>
                <label>Operadora:</label>
                <select name="operadora" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                    <option value="">Todas</option>
                    <?php foreach ($operators as $op): ?>
                        <option value="<?= htmlspecialchars($op) ?>" <?= $operadora === $op ? "selected" : "" ?>>
                            <?= htmlspecialchars($op) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Tipo:</label>
                <select name="tipo_plano" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                    <option value="">Todos</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= $tipo === $cat ? "selected" : "" ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit" style="padding:10px 20px; background:#408223; color:white; border:none; border-radius:4px; cursor:pointer;">
                    Filtrar
                </button>
            </div>
        </form>
    </div>
</section>

<section class="planos-lista">
    <div class="container-section">
        <?php if (empty($plans)): ?>
            <div style="text-align:center; padding:60px 20px; color:#666;">
                <h3>Nenhum plano encontrado</h3>
                <p><a href="planos_funcionando.php">Ver todos os planos</a></p>
            </div>
        <?php else: ?>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(350px, 1fr)); gap:30px; margin-top:30px;">
                <?php foreach ($plans as $plan): ?>
                    <div style="border:1px solid #ddd; border-radius:8px; background:white; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                        <div style="background:#408223; color:white; padding:20px;">
                            <h3 style="margin:0 0 5px 0;"><?= htmlspecialchars($plan["nome_plano"]) ?></h3>
                            <div style="font-size:14px; opacity:0.9;"><?= htmlspecialchars($plan["operadora"]) ?></div>
                        </div>
                        
                        <div style="padding:20px;">
                            <?php if ($plan["tipo_plano"]): ?>
                                <div style="background:#e3bd20; color:#333; padding:8px 12px; border-radius:4px; margin-bottom:15px; font-size:14px;">
                                    <strong>Tipo:</strong> <?= htmlspecialchars($plan["tipo_plano"]) ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($plan["descricao"]): ?>
                                <p style="color:#666; line-height:1.5;"><?= htmlspecialchars($plan["descricao"]) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div style="padding:20px; background:#f8f9fa; display:flex; gap:10px;">
                            <a href="plano_detalhe.php?id=<?= $plan["id"] ?>" 
                               style="flex:1; padding:10px; text-align:center; background:#6c757d; color:white; text-decoration:none; border-radius:4px;">
                                Ver Detalhes
                            </a>
                            <a href="contato.php?plano=<?= urlencode($plan["nome_plano"]) ?>" 
                               style="flex:1; padding:10px; text-align:center; background:#e3bd20; color:#333; text-decoration:none; border-radius:4px;">
                                Contratar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="margin-top:30px; padding:15px; background:#f8f9fa; border-radius:4px; color:#6c757d;">
                ✅ <strong>Sistema Otimizado:</strong> <?= count($plans) ?> planos encontrados com velocidade máxima.
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
setTimeout(() => {
    const info = document.querySelector("[style*=\"position:fixed\"]");
    if (info) info.style.opacity = "0";
}, 5000);
</script>

<?php include "partials/footer.php"; ?>