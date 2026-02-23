/* =====================================================
   MERCATU — Formulário de Franquia (JavaScript)
   Validação, máscaras, navegação multi-step e animações
   ===================================================== */

// =====================================================
// PARTÍCULAS DE FUNDO
// =====================================================
function createParticles() {
  const container = document.getElementById('particles');
  if (!container) return;

  const particleCount = 25;

  for (let i = 0; i < particleCount; i++) {
    const particle = document.createElement('div');
    particle.classList.add('particle');

    const size = Math.random() * 4 + 2;
    particle.style.width = size + 'px';
    particle.style.height = size + 'px';
    particle.style.left = Math.random() * 100 + '%';
    particle.style.animationDuration = (Math.random() * 15 + 10) + 's';
    particle.style.animationDelay = (Math.random() * 10) + 's';

    container.appendChild(particle);
  }
}

// =====================================================
// ESTADO DO FORMULÁRIO
// =====================================================
let currentStep = 1;
const totalSteps = 3;

// =====================================================
// NAVEGAÇÃO ENTRE ETAPAS
// =====================================================
function nextStep(step) {
  if (!validateStep(step)) return;

  const currentEl = document.getElementById('step' + step);
  const nextEl = document.getElementById('step' + (step + 1));

  if (!currentEl || !nextEl) return;

  currentEl.classList.remove('active');
  currentEl.style.animation = 'none';

  setTimeout(() => {
    nextEl.classList.add('active');
    nextEl.style.animation = 'fadeInStep 0.5s cubic-bezier(0.16, 1, 0.3, 1) both';
    currentStep = step + 1;
    updateProgress();
    scrollToTop();
  }, 100);
}

function prevStep(step) {
  const currentEl = document.getElementById('step' + step);
  const prevEl = document.getElementById('step' + (step - 1));

  if (!currentEl || !prevEl) return;

  currentEl.classList.remove('active');
  currentEl.style.animation = 'none';

  setTimeout(() => {
    prevEl.classList.add('active');
    prevEl.style.animation = 'fadeInStep 0.5s cubic-bezier(0.16, 1, 0.3, 1) both';
    currentStep = step - 1;
    updateProgress();
    scrollToTop();
  }, 100);
}

function scrollToTop() {
  const container = document.querySelector('.main-container');
  if (container) {
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

// =====================================================
// ATUALIZAR BARRA DE PROGRESSO
// =====================================================
function updateProgress() {
  const fill = document.getElementById('progressFill');
  const percentage = (currentStep / totalSteps) * 100;
  fill.style.width = percentage + '%';

  const steps = document.querySelectorAll('.step');
  steps.forEach((step, index) => {
    const stepNum = index + 1;
    step.classList.remove('active', 'completed');

    if (stepNum === currentStep) {
      step.classList.add('active');
    } else if (stepNum < currentStep) {
      step.classList.add('completed');
    }
  });
}

// =====================================================
// VALIDAÇÃO
// =====================================================
function validateStep(step) {
  let isValid = true;

  clearErrors();

  if (step === 1) {
    const nome = document.getElementById('nome');
    const email = document.getElementById('email');
    const telefone = document.getElementById('telefone');

    if (!nome.value.trim()) {
      showError('nome', 'Por favor, informe seu nome completo.');
      isValid = false;
    } else if (nome.value.trim().length < 3) {
      showError('nome', 'O nome deve ter pelo menos 3 caracteres.');
      isValid = false;
    }

    if (!email.value.trim()) {
      showError('email', 'Por favor, informe seu e-mail.');
      isValid = false;
    } else if (!isValidEmail(email.value)) {
      showError('email', 'Por favor, informe um e-mail válido.');
      isValid = false;
    }

    if (!telefone.value.trim()) {
      showError('telefone', 'Por favor, informe seu telefone.');
      isValid = false;
    } else if (telefone.value.replace(/\D/g, '').length < 10) {
      showError('telefone', 'Telefone deve ter pelo menos 10 dígitos.');
      isValid = false;
    }

  } else if (step === 2) {
    const estado = document.getElementById('estado');
    const cidade = document.getElementById('cidade');

    if (!estado.value) {
      showError('estado', 'Por favor, selecione seu estado.');
      isValid = false;
    }

    if (!cidade.value.trim()) {
      showError('cidade', 'Por favor, informe sua cidade.');
      isValid = false;
    }

  } else if (step === 3) {
    const investimento = document.getElementById('investimento');
    const termos = document.getElementById('termos');

    if (!investimento.value) {
      showError('investimento', 'Por favor, selecione a faixa de investimento.');
      isValid = false;
    }

    if (!termos.checked) {
      showError('termos', 'Você precisa concordar com os termos.');
      isValid = false;
    }
  }

  if (!isValid) {
    const stepEl = document.getElementById('step' + step);
    stepEl.classList.add('shake');
    setTimeout(() => stepEl.classList.remove('shake'), 500);
  }

  return isValid;
}

function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

function showError(fieldId, message) {
  const errorEl = document.getElementById(fieldId + 'Error');
  const formGroup = document.getElementById(fieldId)?.closest('.form-group');

  if (errorEl) {
    errorEl.textContent = message;
  }
  if (formGroup) {
    formGroup.classList.add('has-error');
  }
}

function clearErrors() {
  document.querySelectorAll('.error-msg').forEach(el => el.textContent = '');
  document.querySelectorAll('.form-group.has-error').forEach(el => el.classList.remove('has-error'));
}

// =====================================================
// MÁSCARAS DE INPUT
// =====================================================
function maskPhone(input) {
  let value = input.value.replace(/\D/g, '');
  if (value.length > 11) value = value.slice(0, 11);

  if (value.length > 6) {
    if (value.length > 10) {
      value = '(' + value.slice(0, 2) + ') ' + value.slice(2, 7) + '-' + value.slice(7);
    } else {
      value = '(' + value.slice(0, 2) + ') ' + value.slice(2, 6) + '-' + value.slice(6);
    }
  } else if (value.length > 2) {
    value = '(' + value.slice(0, 2) + ') ' + value.slice(2);
  } else if (value.length > 0) {
    value = '(' + value;
  }

  input.value = value;
}

function maskCPF(input) {
  let value = input.value.replace(/\D/g, '');
  if (value.length > 11) value = value.slice(0, 11);

  if (value.length > 9) {
    value = value.slice(0, 3) + '.' + value.slice(3, 6) + '.' + value.slice(6, 9) + '-' + value.slice(9);
  } else if (value.length > 6) {
    value = value.slice(0, 3) + '.' + value.slice(3, 6) + '.' + value.slice(6);
  } else if (value.length > 3) {
    value = value.slice(0, 3) + '.' + value.slice(3);
  }

  input.value = value;
}

// =====================================================
// ENVIO DO FORMULÁRIO
// =====================================================
function handleSubmit(e) {
  e.preventDefault();

  if (!validateStep(3)) return;

  const submitBtn = document.querySelector('.btn-submit');
  submitBtn.classList.add('loading');
  submitBtn.innerHTML = '<span class="btn-text">Enviando...</span><div class="spinner"></div>';

  // Coletar dados do formulário
  const formData = {
    nome: document.getElementById('nome').value.trim(),
    email: document.getElementById('email').value.trim(),
    telefone: document.getElementById('telefone').value.trim(),
    cpf: document.getElementById('cpf').value.trim(),
    estado: document.getElementById('estado').value,
    cidade: document.getElementById('cidade').value.trim(),
    bairro: document.getElementById('bairro').value.trim(),
    condominio: document.getElementById('condominio').value.trim(),
    investimento: document.getElementById('investimento').value,
    experiencia: document.querySelector('input[name="experiencia"]:checked')?.value || '',
    mensagem: document.getElementById('mensagem').value.trim()
  };

  console.log('Dados do formulário:', formData);

  // Disparar evento Lead no Meta Pixel
  if (typeof fbq !== 'undefined') {
    fbq('track', 'Lead', {
      content_name: 'Formulário Franquia Mercatu',
      content_category: 'Franquia',
      value: formData.investimento,
      currency: 'BRL'
    });
  }

  // Simular envio (substituir pela integração real)
  setTimeout(() => {
    submitBtn.classList.remove('loading');

    // Esconder etapa 3 e mostrar sucesso
    document.getElementById('step3').classList.remove('active');
    const successScreen = document.getElementById('successScreen');
    successScreen.classList.add('active');

    // Atualizar progresso para 100%
    document.getElementById('progressFill').style.width = '100%';
    document.querySelectorAll('.step').forEach(s => {
      s.classList.remove('active');
      s.classList.add('completed');
    });

  }, 2000);
}

// =====================================================
// RESETAR FORMULÁRIO
// =====================================================
function resetForm() {
  const form = document.getElementById('mercatuForm');
  form.reset();

  document.getElementById('successScreen').classList.remove('active');
  document.getElementById('step1').classList.add('active');

  currentStep = 1;
  updateProgress();
  clearErrors();
  scrollToTop();
}

// =====================================================
// EFEITOS VISUAIS NOS INPUTS
// =====================================================
function addInputEffects() {
  const inputs = document.querySelectorAll('input, select, textarea');

  inputs.forEach(input => {
    // Efeito de foco com glow
    input.addEventListener('focus', function() {
      this.closest('.input-wrapper')?.classList.add('focused');
    });

    input.addEventListener('blur', function() {
      this.closest('.input-wrapper')?.classList.remove('focused');
      // Limpar erro ao sair do campo se preenchido
      const formGroup = this.closest('.form-group');
      if (formGroup && formGroup.classList.contains('has-error') && this.value.trim()) {
        formGroup.classList.remove('has-error');
        const errorEl = formGroup.querySelector('.error-msg');
        if (errorEl) errorEl.textContent = '';
      }
    });
  });
}

// =====================================================
// NAVEGAÇÃO POR TECLADO
// =====================================================
function setupKeyboardNav() {
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && e.target.tagName !== 'BUTTON') {
      e.preventDefault();
      if (currentStep < totalSteps) {
        nextStep(currentStep);
      } else {
        handleSubmit(e);
      }
    }
  });
}

// =====================================================
// INICIALIZAÇÃO
// =====================================================
document.addEventListener('DOMContentLoaded', function() {
  // Criar partículas
  createParticles();

  // Configurar máscaras
  const telefoneInput = document.getElementById('telefone');
  if (telefoneInput) {
    telefoneInput.addEventListener('input', function() { maskPhone(this); });
  }

  const cpfInput = document.getElementById('cpf');
  if (cpfInput) {
    cpfInput.addEventListener('input', function() { maskCPF(this); });
  }

  // Configurar envio do formulário
  const form = document.getElementById('mercatuForm');
  if (form) {
    form.addEventListener('submit', handleSubmit);
  }

  // Adicionar efeitos visuais
  addInputEffects();

  // Navegação por teclado
  setupKeyboardNav();

  // Atualizar progresso inicial
  updateProgress();
});
