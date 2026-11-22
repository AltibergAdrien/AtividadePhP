<?php
declare(strict_types=1);

// 1. Verificação obrigatória do método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>405 — Método Não Permitido</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <main class="container" style="margin-top: 3rem;">
            <section class="card">
                <h2>Erro 405 — Método Não Permitido</h2>
                <div class="alert-error">
                    <p>Este endpoint aceita exclusivamente requisições enviadas via método <strong>POST</strong>.</p>
                </div>
                <a href="index.html" class="btn-secondary">Ir para o formulário de inscrição</a>
            </section>
        </main>
    </body>
    </html>
    <?php
    exit;
}

// Função auxiliar de escape de saída (proteção contra XSS)
function esc(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

// 2. Leitura e normalização das entradas
$nome        = trim((string) ($_POST['nome'] ?? ''));
$email       = trim((string) ($_POST['email'] ?? ''));
$experiencia = trim((string) ($_POST['experiencia'] ?? ''));
$turno       = trim((string) ($_POST['turno'] ?? ''));
$mensagem    = trim((string) ($_POST['mensagem'] ?? ''));
$oficinasRaw = $_POST['oficinas'] ?? [];

// Listas permitidas (Whitelists) com rótulos amigáveis para exibição
$mapaExperiencia = [
    'iniciante'     => 'Iniciante (Primeiros passos na Web)',
    'intermediario' => 'Intermediário (Já construo páginas e scripts)',
    'avancado'      => 'Avançado (Experiência com arquitetura e APIs)',
];

$mapaTurnos = [
    'manha' => 'Manhã (08h00 às 12h00)',
    'noite' => 'Noite (19h00 às 22h30)',
];

$mapaOficinas = [
    'html_css'    => 'HTML5 Semântico & CSS3 Moderno',
    'js_dom'      => 'JavaScript Interativo e Manipulação do DOM',
    'php_backend' => 'Back-end Seguro com PHP 8+',
];

// 3. Validação rigorosa no servidor
$erros = [];

if (mb_strlen($nome, 'UTF-8') < 3) {
    $erros[] = 'O campo Nome completo é obrigatório e deve conter no mínimo 3 caracteres.';
}

if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $erros[] = 'Informe um endereço de e-mail válido.';
}

if (!in_array($experiencia, array_keys($mapaExperiencia), true)) {
    $erros[] = 'O nível de experiência selecionado é inválido.';
}

if (!in_array($turno, array_keys($mapaTurnos), true)) {
    $erros[] = 'O turno de preferência selecionado é inválido.';
}

$oficinasValidas = [];
if (!is_array($oficinasRaw) || count($oficinasRaw) === 0) {
    $erros[] = 'Selecione pelo menos uma oficina de interesse.';
} else {
    foreach ($oficinasRaw as $item) {
        if (is_string($item) && in_array($item, array_keys($mapaOficinas), true)) {
            $oficinasValidas[] = $item;
        } else {
            $erros[] = 'Uma ou mais oficinas selecionadas não são reconhecidas pelo sistema.';
            break;
        }
    }
    $oficinasValidas = array_unique($oficinasValidas);
}

if (mb_strlen($mensagem, 'UTF-8') > 200) {
    $erros[] = 'A mensagem de motivação não pode ultrapassar 200 caracteres.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado da Inscrição — Tech Experience 2026</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <h1>Tech Experience 2026</h1>
            <p>Processamento de Inscrição</p>
        </div>
    </header>

    <main class="container">
        <section class="card">
            <?php if (!empty($erros)): ?>
                <h2>Não foi possível concluir sua inscrição</h2>
                <p class="subtitle">Corrija os apontamentos abaixo e tente novamente:</p>

                <div class="alert-error" role="alert">
                    <strong>Erros encontrados na validação:</strong>
                    <ul>
                        <?php foreach ($erros as $erro): ?>
                            <li><?= esc($erro) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <a href="index.html" class="btn-secondary">Voltar ao formulário</a>

            <?php else: ?>
                <h2>Inscrição Confirmada!</h2>
                <div class="alert-success" role="status">
                    Seus dados foram validados e registrados com sucesso para o Tech Experience 2026.
                </div>

                <ul class="summary-list">
                    <li>
                        <span class="summary-label">Participante</span>
                        <span class="summary-value"><?= esc($nome) ?></span>
                    </li>
                    <li>
                        <span class="summary-label">E-mail de Contato</span>
                        <span class="summary-value"><?= esc($email) ?></span>
                    </li>
                    <li>
                        <span class="summary-label">Nível de Experiência</span>
                        <span class="summary-value"><?= esc($mapaExperiencia[$experiencia]) ?></span>
                    </li>
                    <li>
                        <span class="summary-label">Turno Preferencial</span>
                        <span class="summary-value"><?= esc($mapaTurnos[$turno]) ?></span>
                    </li>
                    <li>
                        <span class="summary-label">Oficinas Selecionadas</span>
                        <span class="summary-value">
                            <?php
                            $nomesOficinas = array_map(
                                fn(string $chave): string => $mapaOficinas[$chave],
                                $oficinasValidas
                            );
                            echo esc(implode(' | ', $nomesOficinas));
                            ?>
                        </span>
                    </li>
                    <li>
                        <span class="summary-label">Mensagem / Motivação</span>
                        <span class="summary-value">
                            <?= $mensagem !== '' ? esc($mensagem) : 'Nenhuma mensagem informada.' ?>
                        </span>
                    </li>
                </ul>

                <a href="index.html" class="btn-secondary">Realizar nova inscrição</a>
            <?php endif; ?>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; 2026 Tech Experience — Evento Acadêmico de Tecnologia e Desenvolvimento Web.</p>
        </div>
    </footer>
</body>
</html>
