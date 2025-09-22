<?php
include 'config/db.php';

// --- BACKEND DO COTADOR AJAX ---
if (isset($_GET['cotar']) && $_GET['cotar'] === 'true') {
    header('Content-Type: application/json; charset=utf-8');
    $plano_id = intval($_GET['id']);
    $grupo = intval($_GET['entidade']);
    $detalhes = [];
    $precoTotal = 0;

    if (!empty($_GET['faixas'])) {
        $faixas = json_decode($_GET['faixas'], true);
        foreach ($faixas as $faixa) {
            $sql = "SELECT valor_plano FROM faixas_de_preco WHERE plano_id = ? AND codigo_grupo_contrato = ? AND idade_minima = ? AND idade_maxima = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$plano_id, $grupo, $faixa['idadeMin'], $faixa['idadeMax']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $subtotal = $row['valor_plano'] * $faixa['numPessoas'];
                $detalhes[] = [
                    'faixa' => "{$faixa['idadeMin']} a {$faixa['idadeMax']}",
                    'pessoas' => $faixa['numPessoas'],
                    'subtotal' => number_format($subtotal,2,',','.')
                ];
                $precoTotal += $subtotal;
            }
        }
        echo json_encode([
            'success' => true,
            'data' => [
                'precoTotal' => number_format($precoTotal,2,',','.'),
                'detalhes' => $detalhes
            ]
        ]);
        exit;
    }

    if (!empty($_GET['idades'])) {
        $idades = json_decode($_GET['idades'], true);
        foreach ($idades as $idade) {
            $sql = "SELECT idade_minima, idade_maxima, valor_plano FROM faixas_de_preco WHERE plano_id = ? AND codigo_grupo_contrato = ? AND idade_minima <= ? AND idade_maxima >= ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$plano_id, $grupo, $idade, $idade]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $detalhes[] = [
                    'idade' => $idade,
                    'faixa' => "{$row['idade_minima']} a {$row['idade_maxima']}",
                    'subtotal' => number_format($row['valor_plano'],2,',','.')
                ];
                $precoTotal += $row['valor_plano'];
            } else {
                $detalhes[] = [
                    'idade' => $idade,
                    'faixa' => '-',
                    'subtotal' => 'N/A'
                ];
            }
        }
        echo json_encode([
            'success' => true,
            'data' => [
                'precoTotal' => number_format($precoTotal,2,',','.'),
                'detalhes' => $detalhes
            ]
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos.']);
    exit;
}

// --- BUSCA DADOS DO PLANO E FAIXAS ---
include 'partials/header.php';

$plano_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($plano_id === 0) {
    header("Location: planos.php");
    exit();
}

$selected_entidade_id = isset($_GET['entidade']) ? (int)$_GET['entidade'] : 0;

try {
    $stmt_plano = $conn->prepare("SELECT nome_plano, operadora FROM planos WHERE id = :id");
    $stmt_plano->execute(['id' => $plano_id]);
    $plano = $stmt_plano->fetch(PDO::FETCH_ASSOC);

    if (!$plano) {
        die("Plano não encontrado.");
    }

    $stmt_entidades = $conn->prepare("
        SELECT DISTINCT gc.codigo_grupo, gc.nome_entidade 
        FROM faixas_de_preco fp
        JOIN grupos_contrato gc ON fp.codigo_grupo_contrato = gc.codigo_grupo
        WHERE fp.plano_id = :plano_id AND fp.codigo_grupo_contrato IS NOT NULL AND fp.codigo_grupo_contrato != ''
        ORDER BY gc.nome_entidade
    ");
    $stmt_entidades->execute(['plano_id' => $plano_id]);
    $entidades_disponiveis = $stmt_entidades->fetchAll(PDO::FETCH_ASSOC);

    $sql_faixas = "
        SELECT fp.idade_minima, fp.idade_maxima, fp.valor_plano, gc.nome_entidade
        FROM faixas_de_preco fp
        LEFT JOIN grupos_contrato gc ON fp.codigo_grupo_contrato = gc.codigo_grupo
        WHERE fp.plano_id = :plano_id";
    
    $params = ['plano_id' => $plano_id];

    if ($selected_entidade_id > 0) {
        $sql_faixas .= " AND gc.codigo_grupo = :codigo_grupo";
        $params['codigo_grupo'] = $selected_entidade_id;
    }

    $sql_faixas .= " ORDER BY gc.nome_entidade, fp.idade_minima";
    
    $stmt_faixas = $conn->prepare($sql_faixas);
    $stmt_faixas->execute($params);
    $faixas_de_preco = $stmt_faixas->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("ERRO DE BANCO DE DADOS: " . $e->getMessage());
}

$precos_por_entidade = [];
foreach ($faixas_de_preco as $faixa) {
    $entidade_nome = !empty($faixa['nome_entidade']) ? $faixa['nome_entidade'] : 'Tabela Geral (Todos os Contratos)';
    $precos_por_entidade[$entidade_nome][] = $faixa;
}

$page_title = 'Preços - ' . htmlspecialchars($plano['nome_plano']);
?>

<section>
    <div class="container-section">
        <h2><?= htmlspecialchars($plano['nome_plano']) ?></h2>
        <p class="operadora-tag"><?= htmlspecialchars($plano['operadora']) ?></p>

        <!-- COTADOR HTML INÍCIO -->
        <div class="cotador-card">
          <h2>Simule o valor do plano</h2>
          <form id="cotador-form">
            <div class="cotador-mode-switch">
              <label>
                <input type="radio" name="cotador-mode" value="faixa" checked>
                Por Faixa Etária
              </label>
              <label>
                <input type="radio" name="cotador-mode" value="idade">
                Por Idade Individual
              </label>
            </div>
            <div class="cotador-fields faixa-mode">
              <div id="faixas-etarias-inputs">
                <?php
                // Monta faixas conforme filtro atual
                $faixas_para_cotador = [];
                if ($selected_entidade_id > 0) {
                    foreach ($faixas_de_preco as $faixa) {
                        $faixas_para_cotador[] = $faixa;
                    }
                } else {
                    if (!empty($faixas_de_preco)) {
                        $faixas_para_cotador = $faixas_de_preco;
                    }
                }
                $faixas_unicas = [];
                foreach ($faixas_para_cotador as $f) {
                    $key = $f['idade_minima'].'-'.$f['idade_maxima'];
                    if (!isset($faixas_unicas[$key])) $faixas_unicas[$key] = $f;
                }
                foreach ($faixas_unicas as $faixa):
                ?>
                <div class="cotador-field">
                  <label><?= $faixa['idade_minima'] ?> a <?= $faixa['idade_maxima'] ?> anos</label>
                  <input type="number" min="0"
                         data-idade-min="<?= $faixa['idade_minima'] ?>"
                         data-idade-max="<?= $faixa['idade_maxima'] ?>"
                         placeholder="Qtd." value="0">
                </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="cotador-fields idade-mode" style="display:none;">
              <div id="idades-individuais-list"></div>
              <button type="button" class="btn-add-idade" id="btn-add-idade">+ Adicionar Idade</button>
            </div>
            <input type="hidden" name="id" value="<?= $plano_id ?>">
            <select name="cotador-entidade" style="margin-bottom:12px;width:100%;padding:8px;border-radius:6px;">
              <?php foreach ($entidades_disponiveis as $entidade): ?>
              <option value="<?= $entidade['codigo_grupo'] ?>" <?= ($selected_entidade_id == $entidade['codigo_grupo']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($entidade['nome_entidade']) ?>
              </option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="btn-cta" id="cotador-btn">Calcular</button>
          </form>
          <div id="cotador-resultado" style="display:none;"></div>
        </div>
        <!-- FIM DO COTADOR HTML -->

        <?php if (!empty($entidades_disponiveis)): ?>
            <form action="plano_detalhe.php" method="GET" class="filter-form">
                <input type="hidden" name="id" value="<?= $plano_id ?>">
                <label for="entidade">Filtrar por Entidade:</label>
                <select name="entidade" id="entidade" onchange="this.form.submit()">
                    <option value="0">Ver todas as entidades</option>
                    <?php foreach ($entidades_disponiveis as $entidade): ?>
                        <option value="<?= $entidade['codigo_grupo'] ?>" <?= ($selected_entidade_id == $entidade['codigo_grupo']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($entidade['nome_entidade']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php endif; ?>

        <?php if (empty($precos_por_entidade)): ?>
            <p style="margin-top: 40px;">Nenhuma tabela de preços encontrada para a seleção atual.</p>
        <?php else: ?>
            <?php foreach ($precos_por_entidade as $entidade_nome => $faixas): ?>
                <h3 style="margin-top: 40px;"><?= htmlspecialchars($entidade_nome) ?></h3>
                <table class="tabela-precos">
                    <thead>
                        <tr>
                            <th>Faixa Etária</th>
                            <th>Valor (R$)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($faixas as $faixa): ?>
                            <tr>
                                <td><?= $faixa['idade_minima'] ?> a <?= $faixa['idade_maxima'] ?> anos</td>
                                <td><?= number_format($faixa['valor_plano'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="back-link-container">
            <a href="planos.php" class="btn-cta">‹ Voltar para Planos</a>
            <a href="https://wa.me/5592981524960?text=Olá! Vi a tabela de preços do plano <?= urlencode($plano['nome_plano']) ?> e gostaria de contratar." class="btn-cta" target="_blank">Contrate Agora</a>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>