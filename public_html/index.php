<?php 
// Configurações da página para SEO
$page_title = 'Plano A - Sua saúde em primeiro lugar';
$meta_description = 'Encontre o plano de saúde ideal com a Plano A. Atendimento humanizado, ampla rede credenciada e os melhores preços.';
include 'partials/header.php';
?>

<!-- HERO SLIDER CORRIGIDO -->
<section class="hero-slider">
    <div class="slider-container">
        
        <!-- Slide 1 -->
        <div class="slide active" data-bg="img/bem-vindo.webp">
            <div class="slide-overlay"></div>
            <div class="hero-content">
                <h1>O plano de saúde ideal para você está aqui!</h1>
                <ul class="hero-benefits">
                    <li>✓ Fácil de contratar</li>
                    <li>✓ Fácil de usar</li>
                    <li>✓ Fácil e seguro de pagar</li>
                </ul>
                <a href="planos.php" class="btn-cta">Contrate Agora</a>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="slide" data-bg="img/1.webp">
            <div class="slide-overlay"></div>
            <div class="hero-content">
                <h1>Cuidado completo para toda sua família</h1>
                <ul class="hero-benefits">
                    <li>✓ Ampla rede credenciada</li>
                    <li>✓ Atendimento 24/7</li>
                    <li>✓ Planos personalizados</li>
                </ul>
                <a href="planos.php?categoria=familias" class="btn-cta">Ver Planos Família</a>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="slide" data-bg="img/2.webp">
            <div class="slide-overlay"></div>
            <div class="hero-content">
                <h1>Planos exclusivos para empresas</h1>
                <ul class="hero-benefits">
                    <li>✓ Condições especiais</li>
                    <li>✓ Gestão simplificada</li>
                    <li>✓ Suporte dedicado</li>
                </ul>
                <a href="planos.php?categoria=empresas" class="btn-cta">Solicite Cotação</a>
            </div>
        </div>

        <!-- Slide 4 -->
        <div class="slide" data-bg="img/hero-slide-4.jpg">
            <div class="slide-overlay"></div>
            <div class="hero-content">
                <h1>Mais de 15 anos cuidando de você</h1>
                <ul class="hero-benefits">
                    <li>✓ +50.000 clientes satisfeitos</li>
                    <li>✓ Experiência comprovada</li>
                    <li>✓ Atendimento humanizado</li>
                </ul>
                <a href="contato.php" class="btn-cta">Fale Conosco</a>
            </div>
        </div>

    </div>

    <!-- Controles de navegação -->
    <div class="slider-nav">
        <button class="nav-dot active" data-slide="0"></button>
        <button class="nav-dot" data-slide="1"></button>
        <button class="nav-dot" data-slide="2"></button>
        <button class="nav-dot" data-slide="3"></button>
    </div>

    <!-- Setas de navegação -->
    <button class="slider-arrow slider-prev" aria-label="Slide anterior">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button class="slider-arrow slider-next" aria-label="Próximo slide">
        <i class="fas fa-chevron-right"></i>
    </button>

    <!-- Indicador de progresso -->
    <div class="slide-progress">
        <div class="progress-bar"></div>
    </div>
</section>

<!-- SEÇÃO SOBRE MODERNIZADA -->
<section class="animate-on-scroll">
    <div class="container-section">
        <h2>Conheça a Plano A</h2>
        <p>Cuidar da sua saúde é o nosso plano. Oferecemos soluções personalizadas e um atendimento humanizado para você e sua família.</p>
    </div>
</section>


<!-- CARDS DE PLANOS MODERNIZADOS -->
<section class="planos-cards-section-fullwidth">

    <div class="card-fullwidth animate-on-scroll">
        <div class="card-content">
            <div class="card-content-inner">
                <h2>Planos de saúde para Pessoa Física</h2>
                <p>Aqui na Plano A, temos parceria com as mais importantes empresas, com qualidade e preços que cabem no seu bolso.</p>
                
                <ul class="card-benefits">
                    <li><img src="img/icons/atendimento.svg" alt="Check"> Ampla Rede Credenciada</li>
                    <li><img src="img/icons/atendimento.svg" alt="Check"> Contratação Rápida</li>
                    <li><img src="img/icons/atendimento.svg" alt="Check"> Atendimento Humanizado</li>
                </ul>

                <div class="card-actions">
                    <a href="planos.php" class="btn-cta-secondary">SIMULE AGORA</a>
                    <a href="contato.php" class="btn-cta">FALE COM A GENTE</a>
                </div>
            </div>
        </div>
        <div class="card-image">
            <img src="img/1.webp" alt="Pessoa sorrindo e saudável" loading="lazy">
        </div>
    </div>

    <div class="card-fullwidth card-reverse animate-on-scroll">
        <div class="card-content">
            <div class="card-content-inner">
                <h2>Planos de saúde para Empresas</h2>
                <p>Para você, empreendedor ou empreendedora, a Plano A oferece diversas opções de convênios médicos que combinam com o seu negócio.</p>
                <div class="card-actions">
                    <a href="planos.php" class="btn-cta-secondary">PEÇA UMA COTAÇÃO</a>
                    <a href="contato.php" class="btn-cta">FALE COM A GENTE</a>
                </div>
            </div>
        </div>
        <div class="card-image">
             <img src="img/2.webp" alt="Equipe de trabalho colaborando" loading="lazy">
        </div>
    </div>

    <div class="card-fullwidth animate-on-scroll">
        <div class="card-content">
            <div class="card-content-inner">
                <h2>O que nossos clientes dizem</h2>
                
                <blockquote class="card-testimonial">
                    <p>"O atendimento da Plano A foi fundamental para encontrar o plano certo para minha família. O cuidado e a atenção fizeram toda a diferença. Recomendo!"</p>
                    <footer>- Maria Silva, Cliente Satisfeita</footer>
                </blockquote>

                <div class="card-actions">
                    <a href="contato.php" class="btn-cta">SEJA NOSSO CLIENTE</a>
                </div>
            </div>
        </div>
        <div class="card-image">
            <img src="img/2.webp" alt="Cliente satisfeito" loading="lazy">
        </div>
    </div>

</section>

<!-- DIFERENCIAIS MODERNIZADOS -->
<section class="diferenciais animate-on-scroll">
    <div class="container-section">
        <h2>Por que escolher a Plano A?</h2>
        <div class="diferenciais-grid">
            
            <div class="diferencial-item shadow-hover">
                <img src="img/icons/atendimento.svg" alt="Ícone de Atendimento Humanizado" class="diferencial-icon">
                <h3>Atendimento Humanizado</h3>
                <p>Nossa equipe está sempre pronta para oferecer um suporte próximo e eficiente.</p>
            </div>

            <div class="diferencial-item shadow-hover">
                <img src="img/consul.png" alt="Ícone de Consultoria" class="diferencial-icon">
                <h3>Consultoria Especializada</h3>
                <p>Identificamos o plano mais adequado para o seu perfil e necessidade.</p>
            </div>

            <div class="diferencial-item shadow-hover">
                <img src="img/money.png" alt="Ícone de Custo-Benefício" class="diferencial-icon">
                <h3>Melhor Custo-Benefício</h3>
                <p>As melhores condições do mercado para planos individuais, familiares ou empresariais.</p>
            </div>

            <div class="diferencial-item shadow-hover">
                <img src="img/tech.png" alt="Ícone de Tecnologia" class="diferencial-icon">
                <h3>Tecnologia a seu favor</h3>
                <p>Use nosso portal do cliente para gerenciar seu plano com facilidade e segurança.</p>
            </div>

            <div class="diferencial-item shadow-hover">
                <img src="img/conf.png" alt="Ícone de Confiança" class="diferencial-icon">
                <h3>Transparência e Confiança</h3>
                <p>Processos claros e sem surpresas. A sua tranquilidade é nossa prioridade.</p>
            </div>

            <div class="diferencial-item shadow-hover">
                <img src="img/icons/heart.svg" alt="Ícone de Cuidado" class="diferencial-icon">
                <h3>Cuidado em primeiro lugar</h3>
                <p>Mais do que planos, oferecemos cuidado e bem-estar contínuos.</p>
            </div>

        </div>
        <div class="diferenciais-cta">
             <a href="contato.php" class="btn-cta">Fale com Nossos Especialistas</a>
        </div>
    </div>
</section>

<!-- SEÇÃO DE ESTATÍSTICAS -->
<section class="estatisticas animate-on-scroll" style="background: linear-gradient(135deg, #003366 0%, #003388 100%); color: white; text-align: center; padding: 80px 5%;">
    <div class="container-section">
        <h2 style="color: white; margin-bottom: 20px;">Números que Falam por Si</h2>
        <p style="color: rgba(255,255,255,0.9); font-size: 1.2rem; margin-bottom: 50px;">Conheça os resultados da nossa dedicação em cuidar da sua saúde</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; margin-top: 50px;">
            
            <div class="stat-item" style="padding: 30px 20px; background: rgba(255,255,255,0.1); border-radius: 12px; backdrop-filter: blur(10px);">
                <div style="font-size: 3.5rem; font-weight: 900; margin-bottom: 10px; color: #e3bd20;" data-count="50000">50.000+</div>
                <div style="font-size: 1.2rem; opacity: 0.9; font-weight: 600;">Clientes Satisfeitos</div>
            </div>
            
            <div class="stat-item" style="padding: 30px 20px; background: rgba(255,255,255,0.1); border-radius: 12px; backdrop-filter: blur(10px);">
                <div style="font-size: 3.5rem; font-weight: 900; margin-bottom: 10px; color: #e3bd20;" data-count="200">200+</div>
                <div style="font-size: 1.2rem; opacity: 0.9; font-weight: 600;">Planos Disponíveis</div>
            </div>
            
            <div class="stat-item" style="padding: 30px 20px; background: rgba(255,255,255,0.1); border-radius: 12px; backdrop-filter: blur(10px);">
                <div style="font-size: 3.5rem; font-weight: 900; margin-bottom: 10px; color: #e3bd20;" data-count="5000">5.000+</div>
                <div style="font-size: 1.2rem; opacity: 0.9; font-weight: 600;">Rede Credenciada</div>
            </div>
            
            <div class="stat-item" style="padding: 30px 20px; background: rgba(255,255,255,0.1); border-radius: 12px; backdrop-filter: blur(10px);">
                <div style="font-size: 3.5rem; font-weight: 900; margin-bottom: 10px; color: #e3bd20;" data-count="15">15+</div>
                <div style="font-size: 1.2rem; opacity: 0.9; font-weight: 600;">Anos de Experiência</div>
            </div>
            
        </div>
    </div>
</section>

<!-- SEÇÃO CTA FINAL -->
<section class="cta-final animate-on-scroll" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); text-align: center; padding: 80px 5%;">
    <div class="container-section">
        <h2 style="color: #003366; margin-bottom: 20px;">Pronto para Cuidar da Sua Saúde?</h2>
        <p style="font-size: 1.3rem; color: #666; margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto;">
            Não deixe para depois o que pode fazer hoje. Sua saúde e de sua família merecem o melhor cuidado.
        </p>
        
        <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; margin-top: 40px;">
            <a href="planos.php" class="btn-cta" style="font-size: 1.1rem; padding: 18px 40px;">
                Ver Todos os Planos
            </a>
            <a href="contato.php" class="btn-cta-secondary" style="font-size: 1.1rem; padding: 18px 40px;">
                Falar com Especialista
            </a>
        </div>
        
        <div style="margin-top: 40px; padding: 30px; background: rgba(64, 130, 35, 0.05); border-radius: 12px; border-left: 4px solid #003366;">
            <p style="margin: 0; font-size: 1.1rem; color: #003366; font-weight: 600;">
                💬 <strong>Atendimento Personalizado:</strong> Nossa equipe está pronta para encontrar o plano ideal para você!
            </p>
        </div>
    </div>
</section>

<!-- CSS ESPECÍFICO PARA OS NOVOS CARDS E HERO SLIDER -->
<style>
/* === RESET PARA HERO SLIDER === */
.hero-slider * {
    box-sizing: border-box;
}

.hero-slider {
    clear: both;
    display: block;
    margin: 0 auto;
}

/* === HERO SLIDER CSS CORRIGIDO === */
.hero-slider {
    position: relative;
    width: 100%;
    height: 80vh;
    min-height: 500px;
    max-height: 600px;
    overflow: hidden;
    margin: 0;
    padding: 0;
}

.slider-container {
    position: relative;
    width: 100%;
    height: 100%;
}

.slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transform: scale(1.05);
    transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.slide.active {
    opacity: 1;
    transform: scale(1);
}

/* APLICANDO BACKGROUNDS CORRIGIDOS */
.slide:nth-child(1) {
    background: linear-gradient(135deg, rgba(0, 51, 102, 0.7), rgba(68, 167, 141, 0.7)), 
                url('img/bem-vindo.webp') center/cover;
}

.slide:nth-child(2) {
   background: linear-gradient(135deg, rgba(0, 51, 102, 0.7), rgba(68, 167, 141, 0.7)), 
                url('img/1.webp') center/cover;
}

.slide:nth-child(3) {
   background: linear-gradient(135deg, rgba(0, 51, 102, 0.7), rgba(68, 167, 141, 0.7)), 
                url('img/2.webp') center/cover;
}

.slide:nth-child(4) {
   background: linear-gradient(135deg, rgba(0, 51, 102, 0.7), rgba(68, 167, 141, 0.7)), 
                url('img/hero-slide-4.jpg') center/cover;
}

/* FALLBACK para quando as imagens não carregarem */
.slide:nth-child(1):not([style*="background-image"]) {
    background: linear-gradient(135deg, #003366, #44A78D);
}

.slide:nth-child(2):not([style*="background-image"]) {
    background: linear-gradient(135deg, #003366, #4169e1);
}

.slide:nth-child(3):not([style*="background-image"]) {
    background: linear-gradient(135deg, #003366, #e3bd20);
}

.slide:nth-child(4):not([style*="background-image"]) {
    background: linear-gradient(135deg, #e3bd20, #003366);
}

.slide-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: white;
    max-width: 800px;
    padding: 0 20px;
}

.hero-content h1 {
    font-size: 2.8rem;
    font-weight: 800;
    margin-bottom: 25px;
    line-height: 1.2;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.8);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.hero-benefits {
    list-style: none;
    margin: 40px 0;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
    padding: 0;
}

.hero-benefits li {
    font-size: 1.3rem;
    font-weight: 600;
    padding: 12px 25px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 30px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.hero-benefits li:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-3px);
}

.btn-cta {
    display: inline-block;
    padding: 18px 45px;
    background: linear-gradient(135deg, #003366, #4169e1);
    color: white;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 700;
    font-size: 1.2rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.4s ease;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
    margin-top: 20px;
    position: relative;
    overflow: hidden;
}

.btn-cta::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.btn-cta:hover::before {
    left: 100%;
}

.btn-cta:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
    background: linear-gradient(135deg, #003366, #44A78D);
    text-decoration: none;
    color: white;
}

/* Controles de navegação */
.slider-nav {
    position: absolute;
    bottom: 40px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 15px;
    z-index: 3;
}

.nav-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.4);
    border: 2px solid rgba(255, 255, 255, 0.6);
    cursor: pointer;
    transition: all 0.3s ease;
}

.nav-dot.active {
    background: #44A78D;
    border-color: white;
    transform: scale(1.2);
}

.nav-dot:hover {
    background: rgba(255, 255, 255, 0.7);
    transform: scale(1.1);
}

/* Setas de navegação */
.slider-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.3);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    color: white;
    font-size: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 3;
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
}

.slider-prev {
    left: 30px;
}

.slider-next {
    right: 30px;
}

.slider-arrow:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-50%) scale(1.1);
}

/* Barra de progresso */
.slide-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: rgba(255, 255, 255, 0.2);
    z-index: 3;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #44A78D, #003366);
    width: 0%;
    transition: width 5s linear;
}

/* === CSS PARA PLANOS EXCLUSIVOS === */
.planos-exclusivos-section {
    padding: 80px 5%;
    background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
}

.planos-exclusivos-header {
    text-align: center;
    margin-bottom: 60px;
}

.planos-exclusivos-subtitle {
    color: #6c757d;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 15px;
}

.planos-exclusivos-title {
    font-size: 2.8rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 25px;
    padding-bottom: 20px;
    position: relative;
    display: inline-block;
}

.planos-exclusivos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.plano-exclusivo-card {
    position: relative;
    height: 400px;
    border-radius: 20px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.4s ease;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.plano-exclusivo-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.plano-card-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    transition: transform 0.4s ease;
}

.plano-exclusivo-card:hover .plano-card-background {
    transform: scale(1.05);
}

.plano-card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.4) 0%, 
        rgba(255, 255, 255, 0.3) 100%);
    border-radius: 20px;
    transition: all 0.4s ease;
    backdrop-filter: blur(2px);
}

.plano-exclusivo-card:hover .plano-card-overlay {
    background: linear-gradient(135deg, 
        rgba(0, 51, 102, 0.8) 0%, 
        rgba(0, 51, 102, 0.6) 100%);
    backdrop-filter: blur(5px);
}

.plano-card-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 40px 30px;
    color: white;
    z-index: 2;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
}

.plano-card-badge {
    display: inline-block;
    background: linear-gradient(135deg, #003366, #44A78D );
    color: white;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 8px 15px;
    border-radius: 20px;
    margin-bottom: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.plano-card-title {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 20px;
    line-height: 1.2;
}

.plano-card-button {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.9);
    color: #003366;
    text-decoration: none;
    padding: 15px 30px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    border: 2px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.plano-card-button:hover {
    background: white;
    color: #003366;
    text-decoration: none;
    transform: translateX(5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.plano-card-button i {
    transition: transform 0.3s ease;
}

.plano-card-button:hover i {
    transform: translateX(3px);
}

/* Imagens específicas - substitua pelos caminhos corretos das suas imagens */
.plano-card-servidores {
    background-image: url('img/alasp.webp');
}

.plano-card-seniors {
    background-image: url('img/pme.webp');
}

.plano-card-comerciarios {
    background-image: url('img/comerciario.webp');
}

.plano-card-profissionais {
    background-image: url('img/alapl.webp');
}

.plano-card-empresas {
    background-image: url('img/empresas.webp');
}

.plano-card-familias {
    background-image: url('img/familia.webp');
}
.plano-card-estudantes {
    background-image: url('img/estudante.webp');
}

/* === RESPONSIVIDADE HERO SLIDER === */
@media (max-width: 1024px) {
    .hero-content h1 {
        font-size: 2.5rem;
    }
    
    .slider-arrow {
        width: 50px;
        height: 50px;
        font-size: 18px;
    }
    
    .slider-prev {
        left: 20px;
    }
    
    .slider-next {
        right: 20px;
    }
}

@media (max-width: 768px) {
    .hero-slider {
        height: 70vh;
        min-height: 450px;
        max-height: 600px;
    }
    
    .hero-content {
        padding: 0 15px;
    }
    
    .hero-content h1 {
        font-size: 2.2rem;
    }
    
    .hero-benefits {
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    
    .hero-benefits li {
        font-size: 1.1rem;
        padding: 10px 20px;
    }
    
    .btn-cta {
        padding: 15px 35px;
        font-size: 1.1rem;
    }
    
    .slider-arrow {
        display: none;
    }
    
    .slider-nav {
        bottom: 20px;
    }
}

@media (max-width: 480px) {
    .hero-slider {
        height: 60vh;
        min-height: 400px;
    }
    
    .hero-content h1 {
        font-size: 1.8rem;
        margin-bottom: 20px;
    }
    
    .hero-benefits li {
        font-size: 1rem;
        padding: 8px 16px;
    }
    
    .btn-cta {
        padding: 12px 25px;
        font-size: 1rem;
    }
    
    .nav-dot {
        width: 10px;
        height: 10px;
    }
}

/* Responsividade específica dos cards exclusivos */
@media (max-width: 768px) {
    .planos-exclusivos-section {
        padding: 60px 15px;
    }

    .planos-exclusivos-title {
        font-size: 36px;
    }

    .planos-exclusivos-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .plano-exclusivo-card {
        height: 350px;
    }

    .plano-card-content {
        padding: 30px 25px;
    }

    .plano-card-title {
        font-size: 26px;
    }

    .plano-card-button {
        padding: 12px 25px;
        font-size: 13px;
    }
}

@media (max-width: 480px) {
    .planos-exclusivos-title {
        font-size: 28px;
    }

    .plano-exclusivo-card {
        height: 300px;
    }

    .plano-card-content {
        padding: 25px 20px;
    }

    .plano-card-title {
        font-size: 22px;
    }
}

/* Animações de entrada */
.slide.active .hero-content h1 {
    animation: fadeInUp 1s ease-out 0.3s both;
}

.slide.active .hero-benefits li {
    animation: fadeInUp 1s ease-out 0.5s both;
}

.slide.active .hero-benefits li:nth-child(2) {
    animation-delay: 0.6s;
}

.slide.active .hero-benefits li:nth-child(3) {
    animation-delay: 0.7s;
}

.slide.active .btn-cta {
    animation: fadeInUp 1s ease-out 0.8s both;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<!-- JAVASCRIPT DO HERO SLIDER CORRIGIDO -->
<script>
// JAVASCRIPT CORRIGIDO PARA O HERO SLIDER
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.slide');
    const navDots = document.querySelectorAll('.nav-dot');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    const progressBar = document.querySelector('.progress-bar');
    
    let currentSlide = 0;
    let slideInterval;
    let progressInterval;
    const slideDelay = 5000; // 5 segundos por slide
    
    // Configuração das imagens de background
    const slideImages = [
        'img/bem-vindo.webp',
        'img/1.webp', 
        'img/2.webp',
        'img/hero-slide-4.jpg'
    ];
    
    const gradients = [
        'linear-gradient(135deg, rgba(0, 51, 102, 0.7), rgba(68, 167, 141, 0.7))',
        'linear-gradient(135deg, rgba(68, 167, 141, 0.7), rgba(0, 51, 102, 0.7))',
        'linear-gradient(135deg, rgba(0, 51, 102, 0.7), rgba(227, 189, 32, 0.7))',
        'linear-gradient(135deg, rgba(227, 189, 32, 0.7), rgba(0, 51, 102, 0.7))'
    ];
    
    // Função para carregar imagens de background
    function loadSlideBackgrounds() {
        slides.forEach((slide, index) => {
            const img = new Image();
            img.onload = function() {
                slide.style.backgroundImage = `${gradients[index]}, url('${slideImages[index]}')`;
                slide.style.backgroundSize = 'cover';
                slide.style.backgroundPosition = 'center';
                slide.style.backgroundRepeat = 'no-repeat';
            };
            img.onerror = function() {
                // Fallback para gradiente se a imagem não carregar
                slide.style.background = gradients[index].replace(/rgba\([^)]+\)/g, function(match) {
                    return match.replace(/0\.\d+/, '1');
                });
                console.warn(`Imagem não encontrada: ${slideImages[index]}`);
            };
            img.src = slideImages[index];
        });
    }
    
    // Função para atualizar slide
    function updateSlide(index) {
        // Remove active de todos os slides e dots
        slides.forEach(slide => slide.classList.remove('active'));
        navDots.forEach(dot => dot.classList.remove('active'));
        
        // Adiciona active no slide atual
        slides[index].classList.add('active');
        if (navDots[index]) {
            navDots[index].classList.add('active');
        }
        
        currentSlide = index;
        resetProgress();
    }
    
    // Função para próximo slide
    function nextSlide() {
        const next = (currentSlide + 1) % slides.length;
        updateSlide(next);
    }
    
    // Função para slide anterior
    function prevSlide() {
        const prev = (currentSlide - 1 + slides.length) % slides.length;
        updateSlide(prev);
    }
    
    // Função para resetar e iniciar barra de progresso
    function resetProgress() {
        if (!progressBar) return;
        
        progressBar.style.width = '0%';
        clearInterval(progressInterval);
        
        let progress = 0;
        progressInterval = setInterval(() => {
            progress += 100 / (slideDelay / 50); // Atualiza a cada 50ms
            progressBar.style.width = progress + '%';
            
            if (progress >= 100) {
                clearInterval(progressInterval);
            }
        }, 50);
    }
    
    // Função para iniciar autoplay
    function startAutoplay() {
        slideInterval = setInterval(nextSlide, slideDelay);
        resetProgress();
    }
    
    // Função para parar autoplay
    function stopAutoplay() {
        clearInterval(slideInterval);
        clearInterval(progressInterval);
    }
    
    // Event listeners para navegação manual
    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            stopAutoplay();
            prevSlide();
            startAutoplay();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            stopAutoplay();
            nextSlide();
            startAutoplay();
        });
    }
    
    // Event listeners para dots
    navDots.forEach((dot, index) => {
        dot.addEventListener('click', (e) => {
            e.preventDefault();
            stopAutoplay();
            updateSlide(index);
            startAutoplay();
        });
    });
    
    // Pausar autoplay ao passar mouse
    const sliderContainer = document.querySelector('.hero-slider');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', stopAutoplay);
        sliderContainer.addEventListener('mouseleave', startAutoplay);
    }
    
    // Navegação por teclado
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            stopAutoplay();
            prevSlide();
            startAutoplay();
        } else if (e.key === 'ArrowRight') {
            stopAutoplay();
            nextSlide();
            startAutoplay();
        }
    });
    
    // Touch/Swipe support para mobile
    let startX = 0;
    let endX = 0;
    let startY = 0;
    let endY = 0;
    
    if (sliderContainer) {
        sliderContainer.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        }, { passive: true });
        
        sliderContainer.addEventListener('touchmove', (e) => {
            // Previne scroll vertical durante swipe horizontal
            if (Math.abs(e.touches[0].clientX - startX) > Math.abs(e.touches[0].clientY - startY)) {
                e.preventDefault();
            }
        }, { passive: false });
        
        sliderContainer.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].clientX;
            endY = e.changedTouches[0].clientY;
            handleSwipe();
        }, { passive: true });
    }
    
    function handleSwipe() {
        const threshold = 50;
        const diffX = startX - endX;
        const diffY = startY - endY;
        
        // Só processa swipe se for mais horizontal que vertical
        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > threshold) {
            stopAutoplay();
            if (diffX > 0) {
                nextSlide(); // Swipe left = próximo slide
            } else {
                prevSlide(); // Swipe right = slide anterior
            }
            startAutoplay();
        }
    }
    
    // Função de inicialização
    function initSlider() {
        if (slides.length === 0) {
            console.warn('Nenhum slide encontrado');
            return;
        }
        
        // Carrega as imagens de background
        loadSlideBackgrounds();
        
        // Garante que o primeiro slide está ativo
        updateSlide(0);
        
        // Inicia o autoplay
        startAutoplay();
        
        console.log('Slider inicializado com', slides.length, 'slides');
    }
    
    // Função para redimensionamento da janela
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            // Reaplica os backgrounds após redimensionamento
            loadSlideBackgrounds();
        }, 250);
    });
    
    // Inicializar slider
    initSlider();
    
    // Função para pausar quando a aba não está visível
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopAutoplay();
        } else {
            startAutoplay();
        }
    });

    // Animação dos números das estatísticas
    function animateNumbers() {
        const statItems = document.querySelectorAll('.stat-item div[data-count]');
        
        statItems.forEach(item => {
            const target = parseInt(item.getAttribute('data-count'));
            const duration = 2000; // 2 segundos
            const step = target / (duration / 16); // 60 FPS
            let current = 0;
            
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                
                // Formatar números
                if (target >= 1000) {
                    item.textContent = Math.floor(current).toLocaleString() + (target >= 50000 ? '+' : target >= 5000 ? '+' : '+');
                } else {
                    item.textContent = Math.floor(current) + '+';
                }
            }, 16);
        });
    }

    // Observer para animar números quando a seção aparecer
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateNumbers();
                statsObserver.unobserve(entry.target);
            }
        });
    });

    const statsSection = document.querySelector('.estatisticas');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }
});

// Carrega o JavaScript melhorado se existir
if (typeof window !== 'undefined' && !document.querySelector('script[src*="main.js"]')) {
    const script = document.createElement('script');
    script.src = 'js/main.js';
    script.async = true;
    script.onerror = () => {
        console.log('main.js não encontrado, usando funcionalidades básicas');
    };
    document.head.appendChild(script);
}
</script>

<?php include 'partials/footer.php'; ?>