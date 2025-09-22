<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Verifica se o usuário tem a permissão de 'admin'
if ($_SESSION['admin_role'] !== 'admin') {
    // Se não for admin, mostra uma mensagem de erro e impede o acesso.
    die("Acesso negado. Você não tem permissão para acessar esta página.");
}
include 'partials/header.php';
include 'config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$message = '';
$error = '';

// --- NOVO: Busca todos os grupos de contrato para popular o formulário ---
try {
    $stmt_grupos = $conn->query("SELECT codigo_grupo, nome_entidade FROM grupos_contrato ORDER BY nome_entidade");
    $grupos_de_contrato = $stmt_grupos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $grupos_de_contrato = []; // Se der erro, a lista fica vazia
    $error = "Erro ao buscar grupos de contrato: " . $e->getMessage();
}
// --- FIM DA NOVA BUSCA ---


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Dados do Plano
    $nome_plano = $_POST['nome_plano'];
    $operadora = $_POST['operadora'];
    $codigo_plano = $_POST['codigo_plano'];

    // Dados das Faixas de Preço
    $idades_minimas = $_POST['idade_minima'];
    $idades_maximas = $_POST['idade_maxima'];
    $valores_plano = $_POST['valor_plano'];
    $codigos_grupo = $_POST['codigo_grupo_contrato']; // Pega os códigos dos grupos

    $conn->beginTransaction();

    try {
        // 1. Insere o plano
        $stmt_plano = $conn->prepare("INSERT INTO planos (nome_plano, operadora, codigo_plano) VALUES (:nome_plano, :operadora, :codigo_plano)");
        $stmt_plano->execute([
            'nome_plano' => $nome_plano,
            'operadora' => $operadora,
            'codigo_plano' => $codigo_plano
        ]);

        $plano_id_novo = $conn->lastInsertId();

        // 2. Prepara a inserção para as faixas de preço
        $stmt_faixas = $conn->prepare(
            "INSERT INTO faixas_de_preco (plano_id, idade_minima, idade_maxima, valor_plano, codigo_grupo_contrato) 
             VALUES (:plano_id, :idade_minima, :idade_maxima, :valor_plano, :codigo_grupo_contrato)"
        );

        // 3. Loop para inserir cada faixa de preço
        foreach ($idades_minimas as $key => $idade_minima) {
            if (!empty($idade_minima) && !empty($idades_maximas[$key]) && !empty($valores_plano[$key])) {
                
                $valor_formatado = str_replace('.', '', $valores_plano[$key]);
                $valor_formatado = (float) str_replace(',', '.', $valor_formatado);

                // --- ATUALIZAÇÃO: Pega o código do grupo do array enviado ---
                $codigo_grupo_contrato = !empty($codigos_grupo[$key]) ? $codigos_grupo[$key] : null;

                $stmt_faixas->execute([
                    ':plano_id' => $plano_id_novo,
                    ':idade_minima' => (int)$idade_minima,
                    ':idade_maxima' => (int)$idades_maximas[$key],
                    ':valor_plano' => $valor_formatado,
                    ':codigo_grupo_contrato' => $codigo_grupo_contrato // Salva o código do grupo
                ]);
            }
        }

        $conn->commit();
        $message = "Plano e suas faixas de preço criados com sucesso!";

    } catch(PDOException $e) {
        $conn->rollBack();
        $error = "Erro ao criar plano: " . $e->getMessage();
    }
}
?>

<section class="admin-section">
    <div class="container-section">
        <h2>Adicionar Novo Plano de Saúde e Preços</h2>
        <a href="admin_dashboard.php">‹ Voltar ao Painel</a>

        <?php if ($message): ?><p class="admin-success"><?= htmlspecialchars($message) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="admin-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        
        <form action="admin_plans.php" method="POST" class="admin-form">
            
            <fieldset>
                <legend>Informações do Plano</legend>
                <div class="form-group">
                    <label for="nome_plano">Nome do Plano:</label>
                    <input type="text" id="nome_plano" name="nome_plano" required>
                </div>
                <div class="form-group">
                    <label for="operadora">Operadora:</label>
                    <input type="text" id="operadora" name="operadora" required>
                </div>
                 <div class="form-group">
                    <label for="codigo_plano">Código do Plano (para importação):</label>
                    <input type="text" id="codigo_plano" name="codigo_plano" required>
                </div>
            </fieldset>

            <fieldset>
                <legend>Tabela de Preços por Faixa Etária</legend>
                <div id="faixas-container">
                    <div class="faixa-row">
                        <input type="number" name="idade_minima[]" placeholder="Idade Mín." required>
                        <input type="number" name="idade_maxima[]" placeholder="Idade Máx." required>
                        <input type="text" name="valor_plano[]" placeholder="Valor (ex: 1.234,56)" required>
                        <select name="codigo_grupo_contrato[]">
                            <option value="">Tabela Geral (Sem Grupo)</option>
                            <?php foreach ($grupos_de_contrato as $grupo): ?>
                                <option value="<?= $grupo['codigo_grupo'] ?>"><?= htmlspecialchars($grupo['nome_entidade']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn-remove-faixa" onclick="removerFaixa(this)">Remover</button>
                    </div>
                </div>
                <button type="button" id="btn-add-faixa" class="btn-add-item">Adicionar Faixa Etária</button>
            </fieldset>

            <button type="submit" class="btn-cta">Salvar Plano e Preços</button>
        </form>
    </div>
</section>

<script>
    document.getElementById('btn-add-faixa').addEventListener('click', function() {
        const container = document.getElementById('faixas-container');
        const novaLinha = document.createElement('div');
        novaLinha.classList.add('faixa-row');
        // ATUALIZAÇÃO: O HTML da nova linha agora inclui o <select>
        novaLinha.innerHTML = `
            <input type="number" name="idade_minima[]" placeholder="Idade Mín." required>
            <input type="number" name="idade_maxima[]" placeholder="Idade Máx." required>
            <input type="text" name="valor_plano[]" placeholder="Valor (ex: 1.234,56)" required>
            <select name="codigo_grupo_contrato[]">
                <option value="">Tabela Geral (Sem Grupo)</option>
                <?php foreach ($grupos_de_contrato as $grupo): ?>
                    <option value="<?= $grupo['codigo_grupo'] ?>"><?= htmlspecialchars($grupo['nome_entidade']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="btn-remove-faixa" onclick="removerFaixa(this)">Remover</button>
        `;
        container.appendChild(novaLinha);
    });

    function removerFaixa(button) {
        if (document.querySelectorAll('.faixa-row').length > 1) {
            button.parentElement.remove();
        } else {
            alert('É necessário ter pelo menos uma faixa de preço.');
        }
    }
</script>

<?php include 'partials/footer.php'; ?>