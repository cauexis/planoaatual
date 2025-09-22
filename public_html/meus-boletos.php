<?php
session_start();
include 'partials/header.php';
include 'config/db.php';

// Proteção da página: se o usuário não estiver logado, redireciona para o login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Pega o ID do usuário logado a partir da sessão
$user_id = $_SESSION['user_id'];
$boletos = [];

try {
    // Busca no banco de dados todos os boletos que pertencem a este usuário
    $stmt = $conn->prepare(
        "SELECT id, competencia, valor, data_vencimento, caminho_pdf, status 
         FROM boletos 
         WHERE user_id = :user_id 
         ORDER BY data_vencimento DESC"
    );
    $stmt->execute(['user_id' => $user_id]);
    $boletos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar boletos: " . $e->getMessage());
}

$page_title = 'Meus Boletos - Plano A';
?>

<main>
    <section class="boletos-section">
        <div class="container-section">
            <h2>Meus Boletos</h2>
            <p>Acesse a 2ª via do seu boleto e verifique seu histórico de pagamentos.</p>

            <div class="boletos-container">
                <?php if (empty($boletos)): ?>
                    <p class="no-results">Nenhum boleto encontrado em seu nome.</p>
                <?php else: ?>
                    <table class="boletos-table">
                        <thead>
                            <tr>
                                <th>Competência</th>
                                <th>Vencimento</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($boletos as $boleto): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($boleto['competencia']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($boleto['data_vencimento'])); ?></td>
                                    <td>R$ <?php echo number_format($boleto['valor'], 2, ',', '.'); ?></td>
                                    <td>
                                        <span class="status-tag status-<?php echo strtolower(htmlspecialchars($boleto['status'])); ?>">
                                            <?php echo htmlspecialchars($boleto['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="gerar-boleto.php?id=<?php echo $boleto['id']; ?>" class="btn-download" target="_blank">Baixar PDF</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
             <a href="area_cliente_dashboard.php" style="margin-top: 30px; display: inline-block;">‹ Voltar ao Painel</a>
        </div>
    </section>
</main>

<?php include 'partials/footer.php'; ?>