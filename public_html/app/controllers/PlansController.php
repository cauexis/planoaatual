<?php
/**
 * Controlador de Planos MELHORADO
 * MANTÉM TODO O DESIGN ORIGINAL
 * Adiciona cache, filtros otimizados e melhor performance
 */
class PlansController extends BaseController
{
    /**
     * Lista planos com cache e filtros otimizados
     * MANTÉM O MESMO LAYOUT E CORES
     */
    public function index()
    {
        try {
            // Obtém filtros da URL (MESMA FUNCIONALIDADE)
            $filters = [
                'operadora' => $this->input('operadora'),
                'tipo_plano' => $this->input('tipo_plano'),
                'search' => $this->input('search')
            ];
            
            // Cache das categorias (MELHORIA DE PERFORMANCE)
            $categories = Cache::remember('plan_categories', function() {
                return $this->db->select(
                    "SELECT DISTINCT tipo_plano FROM planos 
                     WHERE is_active = 1 AND tipo_plano IS NOT NULL AND tipo_plano != '' 
                     ORDER BY tipo_plano"
                );
            }, 1800); // Cache por 30 minutos
            
            // Cache das operadoras (MELHORIA DE PERFORMANCE)
            $operators = Cache::remember('plan_operators', function() {
                return $this->db->select(
                    "SELECT DISTINCT operadora FROM planos 
                     WHERE is_active = 1 AND operadora IS NOT NULL AND operadora != '' 
                     ORDER BY operadora"
                );
            }, 1800); // Cache por 30 minutos
            
            // Busca planos com cache inteligente
            $cacheKey = 'plans_' . md5(serialize($filters));
            $plans = Cache::remember($cacheKey, function() use ($filters) {
                return $this->getFilteredPlans($filters);
            }, 600); // Cache por 10 minutos
            
            // Log da busca para analytics
            if (!empty($filters['search']) || !empty($filters['operadora']) || !empty($filters['tipo_plano'])) {
                Logger::info('Busca de planos realizada', [
                    'filters' => $filters,
                    'results_count' => count($plans),
                    'user_id' => $_SESSION['user_id'] ?? 'anonymous'
                ]);
            }
            
            // MANTÉM OS MESMOS DADOS PARA A VIEW ORIGINAL
            $this->with([
                'plans' => $plans,
                'categories' => array_column($categories, 'tipo_plano'),
                'operators' => array_column($operators, 'operadora'),
                'current_filters' => $filters,
                'page_title' => 'Planos de Saúde - Plano A',
                'meta_description' => 'Encontre o plano de saúde ideal para você e sua família'
            ]);
            
        } catch (Exception $e) {
            Logger::error('Erro ao carregar planos: ' . $e->getMessage());
            
            // Fallback para dados básicos
            $this->with([
                'plans' => [],
                'categories' => [],
                'operators' => [],
                'current_filters' => [],
                'error_message' => 'Erro ao carregar planos. Tente novamente.'
            ]);
        }
    }
    
    /**
     * Detalhes de um plano específico
     * MANTÉM O MESMO LAYOUT DE DETALHES
     */
    public function show($planId)
    {
        try {
            $planId = (int) $planId;
            
            // Cache dos detalhes do plano
            $plan = Cache::remember("plan_details_{$planId}", function() use ($planId) {
                return $this->db->selectOne(
                    "SELECT * FROM planos WHERE id = :id AND is_active = 1",
                    ['id' => $planId]
                );
            }, 1800);
            
            if (!$plan) {
                $_SESSION['error_message'] = 'Plano não encontrado.';
                redirect('planos.php');
            }
            
            // Cache das faixas de preço
            $priceRanges = Cache::remember("plan_prices_{$planId}", function() use ($planId) {
                return $this->db->select(
                    "SELECT * FROM plano_precos 
                     WHERE plano_id = :plano_id 
                     ORDER BY idade_minima",
                    ['plano_id' => $planId]
                );
            }, 1800);
            
            // Log da visualização para analytics
            Logger::info('Plano visualizado', [
                'plan_id' => $planId,
                'plan_name' => $plan['nome_plano'],
                'user_id' => $_SESSION['user_id'] ?? 'anonymous'
            ]);
            
            $this->with([
                'plan' => $plan,
                'price_ranges' => $priceRanges,
                'page_title' => $plan['nome_plano'] . ' - Detalhes do Plano',
                'meta_description' => 'Detalhes do plano ' . $plan['nome_plano'] . ' da ' . $plan['operadora']
            ]);
            
        } catch (Exception $e) {
            Logger::error('Erro ao carregar detalhes do plano: ' . $e->getMessage());
            $_SESSION['error_message'] = 'Erro ao carregar detalhes do plano.';
            redirect('planos.php');
        }
    }
    
    /**
     * API para busca AJAX de planos
     * MELHORIA: Busca em tempo real
     */
    public function search()
    {
        if (!is_ajax()) {
            $this->error('Acesso negado', 403);
        }
        
        try {
            $query = $this->input('q', '', false);
            $limit = min((int) $this->input('limit', 10), 50); // Máximo 50 resultados
            
            if (strlen($query) < 2) {
                $this->json(['results' => []]);
            }
            
            // Cache da busca
            $cacheKey = "search_plans_" . md5($query . $limit);
            $results = Cache::remember($cacheKey, function() use ($query, $limit) {
                return $this->db->select(
                    "SELECT id, nome_plano, operadora, tipo_plano 
                     FROM planos 
                     WHERE is_active = 1 
                     AND (nome_plano LIKE :query OR operadora LIKE :query OR tipo_plano LIKE :query)
                     ORDER BY nome_plano 
                     LIMIT :limit",
                    [
                        'query' => '%' . $query . '%',
                        'limit' => $limit
                    ]
                );
            }, 300); // Cache por 5 minutos
            
            $this->json(['results' => $results]);
            
        } catch (Exception $e) {
            Logger::error('Erro na busca de planos: ' . $e->getMessage());
            $this->json(['error' => 'Erro na busca'], 500);
        }
    }
    
    /**
     * Compara planos (NOVA FUNCIONALIDADE)
     */
    public function compare()
    {
        try {
            $planIds = $this->input('plans', []);
            
            if (!is_array($planIds) || count($planIds) < 2 || count($planIds) > 4) {
                $_SESSION['error_message'] = 'Selecione entre 2 e 4 planos para comparar.';
                redirect('planos.php');
            }
            
            $planIds = array_map('intval', $planIds);
            $placeholders = str_repeat('?,', count($planIds) - 1) . '?';
            
            $plans = $this->db->select(
                "SELECT * FROM planos WHERE id IN ({$placeholders}) AND is_active = 1",
                $planIds
            );
            
            if (count($plans) !== count($planIds)) {
                $_SESSION['error_message'] = 'Alguns planos selecionados não foram encontrados.';
                redirect('planos.php');
            }
            
            // Busca preços para cada plano
            foreach ($plans as &$plan) {
                $plan['prices'] = $this->db->select(
                    "SELECT * FROM plano_precos WHERE plano_id = :id ORDER BY idade_minima",
                    ['id' => $plan['id']]
                );
            }
            
            Logger::info('Comparação de planos realizada', [
                'plan_ids' => $planIds,
                'user_id' => $_SESSION['user_id'] ?? 'anonymous'
            ]);
            
            $this->with([
                'plans' => $plans,
                'page_title' => 'Comparar Planos - Plano A'
            ]);
            
        } catch (Exception $e) {
            Logger::error('Erro na comparação de planos: ' . $e->getMessage());
            $_SESSION['error_message'] = 'Erro ao comparar planos.';
            redirect('planos.php');
        }
    }
    
    /**
     * Busca planos filtrados (MÉTODO PRIVADO OTIMIZADO)
     */
    private function getFilteredPlans($filters)
    {
        $query = "SELECT * FROM planos WHERE is_active = 1";
        $params = [];
        
        // Filtro por operadora
        if (!empty($filters['operadora'])) {
            $query .= " AND operadora = :operadora";
            $params['operadora'] = $filters['operadora'];
        }
        
        // Filtro por tipo de plano
        if (!empty($filters['tipo_plano'])) {
            $query .= " AND tipo_plano = :tipo_plano";
            $params['tipo_plano'] = $filters['tipo_plano'];
        }
        
        // Busca por texto
        if (!empty($filters['search'])) {
            $query .= " AND (nome_plano LIKE :search OR operadora LIKE :search OR descricao LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $query .= " ORDER BY nome_plano";
        
        return $this->db->select($query, $params);
    }
    
    /**
     * Invalida cache quando planos são atualizados
     * MÉTODO PARA USO ADMINISTRATIVO
     */
    public function invalidateCache()
    {
        if (!Security::isAdmin()) {
            $this->error('Acesso negado', 403);
        }
        
        $cleared = Cache::invalidatePlans();
        
        Logger::info('Cache de planos invalidado', [
            'files_cleared' => $cleared,
            'admin_id' => $_SESSION['user_id']
        ]);
        
        if (is_ajax()) {
            $this->json(['success' => true, 'message' => "Cache limpo: {$cleared} arquivos removidos"]);
        } else {
            $_SESSION['success_message'] = "Cache de planos limpo com sucesso!";
            redirect('admin_plans.php');
        }
    }
}

// EXEMPLO DE USO NO SEU planos.php EXISTENTE:
/*
require_once 'core/bootstrap.php';
$controller = new PlansController();
$controller->index();

// Agora você tem acesso às variáveis:
// $plans, $categories, $operators, $current_filters
// 
// Seu HTML permanece EXATAMENTE IGUAL:
// - Mesmas cores (verde #408223, amarelo #e3bd20)
// - Mesmo layout de cards
// - Mesmos filtros
// - Apenas com MELHOR PERFORMANCE e CACHE
*/
