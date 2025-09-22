<?php
// dashboard.php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

// Busca os boletos do usuário
try {
    $stmt_boletos = $conn->prepare("SELECT due_date, amount, status, pdf_url FROM boletos WHERE user_id = :user_id ORDER BY due_date DESC");
    $stmt_boletos->execute(['user_id' => $user_id]);
    $boletos = $stmt_boletos->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) { $boletos = []; }

// Busca os documentos já enviados pelo usuário
try {
    $stmt_docs = $conn->prepare("SELECT document_type, status, uploaded_at, notes FROM user_documents WHERE user_id = :user_id ORDER BY uploaded_at DESC");
    $stmt_docs->execute(['user_id' => $user_id]);
    $documentos = $stmt_docs->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) { $documentos = []; }
?>
<?php include 'partials/header.php'; ?>

<section>
    <div class="container-section">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Minha Conta</h2>
            <a href="logout.php" style="color: red;">Sair</a>
        </div>
        <p>Bem-vindo(a), <strong><?= htmlspecialchars($user_email) ?></strong>!</p>

        <div class="dashboard-card">
            <h3>Envio de Documentos</h3>
            <p>Anexe a documentação solicitada para análise.</p>
            <form action="upload_document.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="document_type">Tipo de Documento:</label>
                    <select name="document_type" id="document_type" required>
                        <option value="">-- Selecione --</option>
                        <option value="RG_Frente">RG (Frente)</option>
                        <option value="RG_Verso">RG (Verso)</option>
                        <option value="CPF">CPF</option>
                        <option value="Comprovante_de_Residencia">Comprovante de Residência</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="document_file" class="file-upload-label">
                    <span>Selecione o arquivo (PDF, JPG, PNG - Máx 5MB):</span>
                    <input type="file" name="document_file" id="document_file" required>
                    </label>
                </div>
                <button type="submit">Enviar Documento</button>
            </form>
        </div>

        <div class="dashboard-card">
            <h3>Meus Documentos Enviados</h3>
            <table class="boletos-table">
                <thead>
                    <tr>
                        <th>Tipo de Documento</th>
                        <th>Data de Envio</th>
                        <th>Status</th>
                        <th>Observações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($documentos)): ?>
                        <tr><td colspan="4">Nenhum documento enviado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($documentos as $doc): ?>
                            <tr>
                                <td><?= htmlspecialchars(str_replace('_', ' ', $doc['document_type'])) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($doc['uploaded_at'])) ?></td>
                                <td><span class="status <?= strtolower($doc['status']) ?>"><?= htmlspecialchars($doc['status']) ?></span></td>
                                <td><?= htmlspecialchars($doc['notes']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="dashboard-card">
            <h3>2ª Via de Boletos</h3>
            </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>