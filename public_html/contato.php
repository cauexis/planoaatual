<?php
// contato.php

$message = '';
$error = '';

// Verifica se existe uma mensagem na sessão e a exibe
if (isset($_SESSION['form_message'])) {
    $message = $_SESSION['form_message'];
    unset($_SESSION['form_message']); // Limpa a mensagem para não mostrar de novo
}
if (isset($_SESSION['form_error'])) {
    $error = $_SESSION['form_error'];
    unset($_SESSION['form_error']);
}
?>
<?php include 'partials/header.php'; ?>

<section>
    <div class="container-section">
        <h2>Fale com a Plano A</h2>
        <p>Estamos prontos para atender você. Envie sua mensagem pelo formulário abaixo ou escolha outro canal de contato.</p>
        
        <div class="contato-container" style="display: flex; gap: 40px; margin-top: 40px; text-align: left;">
            <div class="contato-info" style="flex: 1;">
                <h3>Nossos Canais</h3>
                <p><strong>Telefone:</strong> +55 69 9272-5666</p>
                <p><strong>E-mail:</strong> contato@admplanoa.com</p>
                <p><strong>Horário de Atendimento:</strong> De Segunda a Sexta, das 8h às 18h.</p>
            </div>

            <div class="contato-form" style="flex: 2;">
                <h3>Mande uma mensagem</h3>

                <?php if ($message): ?>
                    <div style="padding: 15px; background-color: lightgreen; border-radius: 5px; margin-bottom: 20px;">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div style="padding: 15px; background-color: #ffcccb; border-radius: 5px; margin-bottom: 20px;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="handle_form.php" method="POST">
                    <input type="text" name="nome" placeholder="Seu nome completo" required>
                    <input type="email" name="email" placeholder="Seu melhor e-mail" required>
                    <textarea name="mensagem" placeholder="Digite sua mensagem aqui..." required></textarea>
                    <button type="submit">Enviar Mensagem</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>