document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("form-inscricao");
  const nomeInput = document.getElementById("nome");
  const emailInput = document.getElementById("email");
  const experienciaSelect = document.getElementById("experiencia");
  const turnoRadios = document.querySelectorAll('input[name="turno"]');
  const oficinasCheckboxes = document.querySelectorAll(
    'input[name="oficinas[]"]',
  );
  const grupoOficinas = document.getElementById("grupo-oficinas");
  const mensagemTextarea = document.getElementById("mensagem");
  const contadorCaracteres = document.getElementById("contador-caracteres");

  const maxCaracteres =
    parseInt(mensagemTextarea.getAttribute("maxlength"), 10) || 200;

  // 1. Contador dinâmico de caracteres em tempo real
  const atualizarContador = () => {
    const restantes = maxCaracteres - mensagemTextarea.value.length;
    contadorCaracteres.textContent = `Restam ${restantes} caracteres`;

    if (restantes <= 25) {
      contadorCaracteres.classList.add("warning");
    } else {
      contadorCaracteres.classList.remove("warning");
    }
  };

  mensagemTextarea.addEventListener("input", atualizarContador);
  atualizarContador();

  // Utilitário para exibir/limpar feedback de erro
  const definirErro = (elemento, idMensagem, mensagem) => {
    const spanErro = document.getElementById(idMensagem);
    if (mensagem) {
      elemento.classList.add("is-invalid");
      spanErro.textContent = mensagem;
    } else {
      elemento.classList.remove("is-invalid");
      spanErro.textContent = "";
    }
  };

  // Limpa erro de oficinas assim que o usuário marca uma opção
  oficinasCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
      const algumaMarcada = Array.from(oficinasCheckboxes).some(
        (cb) => cb.checked,
      );
      if (algumaMarcada) {
        definirErro(grupoOficinas, "erro-oficinas", "");
      }
    });
  });

  // 2. Validação preventiva no envio do formulário
  form.addEventListener("submit", (event) => {
    let formularioValido = true;

    // Valida Nome
    const nomeValor = nomeInput.value.trim();
    if (nomeValor.length < 3) {
      definirErro(
        nomeInput,
        "erro-nome",
        "Informe seu nome completo com pelo menos 3 caracteres.",
      );
      formularioValido = false;
    } else {
      definirErro(nomeInput, "erro-nome", "");
    }

    // Valida E-mail
    const emailValor = emailInput.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailValor || !emailRegex.test(emailValor)) {
      definirErro(
        emailInput,
        "erro-email",
        "Informe um endereço de e-mail válido.",
      );
      formularioValido = false;
    } else {
      definirErro(emailInput, "erro-email", "");
    }

    // Valida Nível de Experiência
    if (!experienciaSelect.value) {
      definirErro(
        experienciaSelect,
        "erro-experiencia",
        "Selecione o seu nível de experiência.",
      );
      formularioValido = false;
    } else {
      definirErro(experienciaSelect, "erro-experiencia", "");
    }

    // Valida Turno
    const turnoSelecionado = Array.from(turnoRadios).some(
      (radio) => radio.checked,
    );
    const spanErroTurno = document.getElementById("erro-turno");
    if (!turnoSelecionado) {
      spanErroTurno.textContent = "Escolha um turno de preferência.";
      formularioValido = false;
    } else {
      spanErroTurno.textContent = "";
    }

    // Valida Oficinas (ao menos uma marcada)
    const oficinaSelecionada = Array.from(oficinasCheckboxes).some(
      (cb) => cb.checked,
    );
    if (!oficinaSelecionada) {
      definirErro(
        grupoOficinas,
        "erro-oficinas",
        "Selecione pelo menos uma oficina de interesse.",
      );
      formularioValido = false;
    } else {
      definirErro(grupoOficinas, "erro-oficinas", "");
    }

    if (!formularioValido) {
      event.preventDefault();
    }
  });
});
