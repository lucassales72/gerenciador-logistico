<?php
require_once __DIR__ . '/../includes/helpers.php';

$pdo = db();
$id = (int) ($_GET['id'] ?? 0);
$erros = [];
$p = ['nome' => '', 'descricao' => '', 'peso_kg' => '', 'valor' => ''];

if ($id > 0) {
    $st = $pdo->prepare('SELECT * FROM produtos WHERE id = ?');
    $st->execute([$id]);
    $p = $st->fetch() ?: redirecionar('produtos/index.php', 'Produto nao encontrado.', 'danger');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $p = [
        'nome'      => post('nome'),
        'descricao' => post('descricao'),
        'peso_kg'   => post('peso_kg'),
        'valor'     => post('valor'),
    ];
    if ($p['nome'] === '') { $erros[] = 'Informe o nome do produto.'; }
    if (!is_numeric($p['peso_kg']) || (float) $p['peso_kg'] < 0) { $erros[] = 'Informe um peso valido (maior ou igual a zero).'; }
    if (!is_numeric($p['valor']) || (float) $p['valor'] < 0) { $erros[] = 'Informe um valor valido (maior ou igual a zero).'; }

    if (!$erros) {
        $dados = [$p['nome'], $p['descricao'], (float) $p['peso_kg'], (float) $p['valor']];
        if ($id > 0) {
            $pdo->prepare('UPDATE produtos SET nome=?, descricao=?, peso_kg=?, valor=? WHERE id=?')
                ->execute([...$dados, $id]);
            redirecionar('produtos/index.php', 'Produto atualizado com sucesso.');
        }
        $pdo->prepare('INSERT INTO produtos (nome, descricao, peso_kg, valor) VALUES (?, ?, ?, ?)')->execute($dados);
        redirecionar('produtos/index.php', 'Produto cadastrado com sucesso.');
    }
}

$pagina = 'produtos';
$titulo = $id > 0 ? 'Editar produto' : 'Novo produto';
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="h3 mb-3"><?= e($titulo) ?></h1>

<?php foreach ($erros as $erro): ?>
    <div class="alert alert-danger py-2"><?= e($erro) ?></div>
<?php endforeach; ?>

<div class="card">
    <div class="card-body">
        <form method="post" class="row g-3">
            <div class="col-md-8">
                <label for="nome" class="form-label">Nome *</label>
                <input type="text" class="form-control" id="nome" name="nome" maxlength="120" required value="<?= e($p['nome']) ?>">
            </div>
            <div class="col-md-4">
                <label for="peso_kg" class="form-label">Peso (kg) *</label>
                <input type="number" step="0.001" min="0" class="form-control" id="peso_kg" name="peso_kg" required value="<?= e($p['peso_kg']) ?>">
            </div>
            <div class="col-md-8">
                <label for="descricao" class="form-label">Descricao</label>
                <input type="text" class="form-control" id="descricao" name="descricao" maxlength="255" value="<?= e($p['descricao']) ?>">
            </div>
            <div class="col-md-4">
                <label for="valor" class="form-label">Valor (R$) *</label>
                <input type="number" step="0.01" min="0" class="form-control" id="valor" name="valor" required value="<?= e($p['valor']) ?>">
            </div>
            <div class="col-12">
                <button class="btn btn-success" type="submit"><i class="bi bi-check-lg"></i> Salvar</button>
                <a class="btn btn-outline-secondary" href="<?= url('produtos/index.php') ?>">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
