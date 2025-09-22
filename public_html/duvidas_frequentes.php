<?php 
// duvidas_frequentes.php

$page_title = "Dúvidas Frequentes - Plano A";
$meta_description = "Encontre respostas para as perguntas mais comuns sobre nossos planos de saúde, cobertura, rede credenciada e muito mais.";

include 'partials/header.php'; 
?>

<section>
    <div class="container-section">
        <h2>Dúvidas Frequentes</h2>
        <p>Reunimos aqui as respostas para as perguntas mais comuns. Se sua dúvida não estiver aqui, por favor, entre em contato conosco.</p>

        <div class="faq-container" style="margin-top: 40px; text-align: left;">
            
            <details class="faq-item">
                <summary>Como faço para contratar um plano?</summary>
                <p>Você pode entrar em contato conosco através do nosso formulário na página "Fale Conosco" ou pelo WhatsApp. Um de nossos consultores especializados irá guiá-lo para encontrar o plano ideal para suas necessidades.</p>
            </details>

            <details class="faq-item">
                <summary>O que é carência?</summary>
                <p>Carência é o período previsto em contrato que o beneficiário precisa aguardar para começar a usar determinados serviços do plano de saúde, como consultas, exames ou internações. Os prazos são regulamentados pela ANS.</p>
            </details>
            
            <details class="faq-item">
                <summary>Como encontro a rede credenciada do meu plano?</summary>
                <p>Você pode acessar a página "Rede Credenciada" em nosso site para fazer uma busca completa por hospitais, clínicas e especialistas que atendem pelo seu plano.</p>
            </details>

            <details class="faq-item">
                <summary>Como emitir a 2ª via do meu boleto?</summary>
                <p>A 2ª via do boleto está disponível na "Área do Cliente". Basta fazer o login com seu e-mail e senha para acessar suas faturas e outros serviços.</p>
            </details>
            
            </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>