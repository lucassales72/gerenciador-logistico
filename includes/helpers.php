<?php
/**
 * Funcoes auxiliares usadas em todas as paginas.
 */
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Escapa HTML para evitar XSS ao imprimir dados do usuario. */
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

/** Monta uma URL a partir da raiz da aplicacao. */
function url(string $caminho = ''): string
{
    return BASE_URL . '/' . ltrim($caminho, '/');
}

/** Redireciona (opcionalmente guardando uma mensagem para a proxima tela). */
function redirecionar(string $caminho, ?string $mensagem = null, string $tipo = 'success'): never
{
    if ($mensagem !== null) {
        $_SESSION['flash'] = [$tipo, $mensagem];
    }
    header('Location: ' . url($caminho));
    exit;
}

/** Exibe (e apaga) a mensagem guardada por redirecionar(). */
function exibirFlash(): void
{
    if (!empty($_SESSION['flash'])) {
        [$tipo, $mensagem] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . e($tipo) . ' alert-dismissible fade show" role="alert">'
            . e($mensagem)
            . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button></div>';
    }
}

/** Le um campo do POST ja sem espacos nas pontas. */
function post(string $campo): string
{
    return trim((string) ($_POST[$campo] ?? ''));
}

function moeda($valor): string
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

function dataBr(string $dataIso): string
{
    $d = DateTime::createFromFormat('Y-m-d', substr($dataIso, 0, 10));
    return $d ? $d->format('d/m/Y') : $dataIso;
}

/** Confere se a string e uma data valida no formato AAAA-MM-DD. */
function dataValida(string $data): bool
{
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return $d && $d->format('Y-m-d') === $data;
}

function enderecoTexto(array $e): string
{
    $linha = $e['logradouro'] . ', ' . $e['numero'];
    if (!empty($e['complemento'])) {
        $linha .= ' - ' . $e['complemento'];
    }
    return $linha . ' - ' . $e['bairro'] . ', ' . $e['cidade'] . '/' . $e['uf'] . ' - CEP ' . $e['cep'];
}

function listaUfs(): array
{
    return ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA',
            'PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
}

/** Executa um DELETE tratando o caso de registro em uso (chave estrangeira). */
function excluirRegistro(string $tabela, int $id, string $voltar, string $rotulo): never
{
    try {
        $st = db()->prepare("DELETE FROM {$tabela} WHERE id = ?");
        $st->execute([$id]);
        redirecionar($voltar, "{$rotulo} excluido com sucesso.");
    } catch (PDOException $ex) {
        redirecionar($voltar, "Nao foi possivel excluir: {$rotulo} esta vinculado a um frete.", 'danger');
    }
}
