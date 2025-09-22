<?php
/**
 * EXEMPLO: planos.php MELHORADO
 * MANTÉM 100% DO SEU DESIGN E CORES ORIGINAIS
 * Adiciona cache, performance e funcionalidades avançadas
 */

// Carrega o sistema melhorado
require_once 'core/bootstrap.php';

// Usa o controlador melhorado
$controller = new PlansController();
$controller->index();

// Agora temos acesso às variáveis otimizadas:
// $plans, $categories, $operators, $current_filters (com cache!)

// MANTÉM SEU HEADER ORIGINAL
include 'partials/header.php';
?>

<!-- MANTÉM SEU DESIGN EXATO -->
<section>
    <div class="container-section">
        <h2>Nossos Planos de Saúde</h2>
        <p>Encontre o plano ideal para você e sua família com as melhores condições do mercado.</p>
        
        <!-- MELHORIA: Mensagens de erro/sucesso mais seguras -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- MANTÉM SEUS FILTROS COM AS MESMAS CORES -->
<section class="filter-section">
    <div class="container-section">
        <form method="GET" action="planos.php" class="plan-filters-form">
            <!-- MELHORIA: Filtros mais inteligentes com cache -->
            <div class="filter-group">
                <h4>Filtrar por Categoria</h4>
                <div class="plan-filters">
                    <a href="planos.php" class="<?= empty($current_filters['tipo_plano']) ? 'active' : '' ?>">
                        Todos
                    </a>
                    <?php foreach ($categories as $category): ?>
                        <a href="planos.php?tipo_plano=<?= urlencode($category) ?>" 
                           class="<?= ($current_filters['tipo_plano'] ?? '') === $category ? 'active' : '' ?>">
                            <?= htmlspecialchars($category) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="filter-group">
                <h4>Filtrar por Operadora</h4>
                <div class="plan-filters">
                    <a href="planos.php" class="<?= empty($current_filters['operadora']) ? 'active' : '' ?>">
                        Todas
                    </a>
                    <?php foreach ($operators as $operator): ?>
                        <a href="planos.php?operadora=<?= urlencode($operator) ?>" 
                           class="<?= ($current_filters['operadora'] ?? '') === $operator ? 'active' : '' ?>">
                            <?= htmlspecialchars($operator) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- NOVA FUNCIONALIDADE: Busca em tempo real -->
            <div class="filter-group">
                <h4>Buscar Planos</h4>
                <div class="search-container">
                    <input type="text" 
                           name="search" 
                           id="plan-search"
                           placeholder="Digite o nome do plano ou operadora..."
                           value="<?= htmlspecialchars($current_filters['search'] ?? '') ?>"
                           class="search-input">
                    <button type="submit" class="btn-search">Buscar</button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- MANTÉM SEU GRID DE PLANOS COM AS MESMAS CORES -->
<section>
    <div class="container-section">
        <?php if (empty($plans)): ?>
            <div class="no-results">
                <h3>Nenhum plano encontrado</h3>
                <p>Tente ajustar os filtros ou <a href="planos.php">ver todos os planos</a>.</p>
            </div>
        <?php else: ?>
            <!-- MELHORIA: Contador de resultados -->
            <div class="results-info">
                <p>Encontrados <strong><?= count($plans) ?></strong> planos</p>
                
                <!-- NOVA FUNCIONALIDADE: Botão de comparação -->
                <div class="compare-section" id="compare-section" style="display: none;">
                    <button type="button" id="compare-btn" class="btn-cta-secondary">
                        Comparar Selecionados (<span id="compare-count">0</span>)
                    </button>
                </div>
            </div>
            
            <!-- MANTÉM SEU GRID ORIGINAL -->
            <div class="planos-grid-container">
                <?php foreach ($plans as $plan): ?>
                    <div class="plano-card">
                        <!-- NOVA FUNCIONALIDADE: Checkbox para comparação -->
                        <div class="plan-compare-checkbox">
                            <input type="checkbox" 
                                   class="compare-checkbox" 
                                   value="<?= $plan['id'] ?>"
                                   id="compare-<?= $plan['id'] ?>">
                            <label for="compare-<?= $plan['id'] ?>">Comparar</label>
                        </div>
                        
                        <div class="plano-card-content">
                            <!-- MANTÉM SEU LAYOUT EXATO -->
                            <h3><?= htmlspecialchars($plan['nome_plano']) ?></h3>
                            <div class="operadora-tag">
                                <?= htmlspecialchars($plan['operadora']) ?>
                            </div>
                            
                            <?php if (!empty($plan['tipo_plano'])): ?>
                                <p><strong>Tipo:</strong> <?= htmlspecialchars($plan['tipo_plano']) ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($plan['descricao'])): ?>
                                <p><?= htmlspecialchars(substr($plan['descricao'], 0, 150)) ?>...</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="plano-card-footer">
                            <!-- MANTÉM SEUS BOTÕES COM AS MESMAS CORES -->
                            <a href="plano_detalhe.php?id=<?= $plan['id'] ?>" class="btn-saiba-mais">
                                Ver Detalhes
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- NOVA FUNCIONALIDADE: JavaScript para comparação e busca -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sistema de comparação de planos
    const compareCheckboxes = document.querySelectorAll('.compare-checkbox');
    const compareSection = document.getElementById('compare-section');
    const compareBtn = document.getElementById('compare-btn');
    const compareCount = document.getElementById('compare-count');
    
    function updateCompareSection() {
        const selected = document.querySelectorAll('.compare-checkbox:checked');
        const count = selected.length;
        
        compareCount.textContent = count;
        
        if (count >= 2) {
            compareSection.style.display = 'block';
            compareBtn.disabled = false;
        } else {
            compareSection.style.display = 'none';
            compareBtn.disabled = true;
        }
        
        if (count > 4) {
            // Desabilita checkboxes não selecionados se já tem 4
            compareCheckboxes.forEach(cb => {
                if (!cb.checked) {
                    cb.disabled = true;
                }
            });
        } else {
            // Reabilita todos os checkboxes
            compareCheckboxes.forEach(cb => {
                cb.disabled = false;
            });
        }
    }
    
    compareCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCompareSection);
    });
    
    compareBtn.addEventListener('click', function() {
        const selected = Array.from(document.querySelectorAll('.compare-checkbox:checked'))
                             .map(cb => cb.value);
        
        if (selected.length >= 2) {
            const params = selected.map(id => `plans[]=${id}`).join('&');
            window.location.href = `compare_plans.php?${params}`;
        }
    });
    
    // Busca em tempo real (opcional)
    const searchInput = document.getElementById('plan-search');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 2) {
                // Implementar busca AJAX aqui se desejar
                console.log('Buscando por:', this.value);
            }
        }, 500);
    });
});
</script>

<!-- MANTÉM SEU FOOTER ORIGINAL -->
<?php include 'partials/footer.php'; ?>
