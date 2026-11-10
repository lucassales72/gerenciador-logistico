<?php
require_once __DIR__ . '/../includes/helpers.php';

$pdo = db();
$id = (int) ($_GET['id'] ?? 0);
$erros = [];
$campos = ['apelido', 'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf'];
$en = array_fill_keys($campos, '');

if ($id > 0) {
    $st = $pdo->prepare('SELECT * FROM enderecos WHERE id = ?');
    $st->execute([$id]);
    $en = $st->fetch() ?: redirecionar('enderecos/index.php', 'Endereco nao encontrado.', 'danger');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($campos as $c) {
        $en[$c] = post($c);
    }
    $en['uf'] = strtoupper($en['uf']);

    foreach (['apelido' => 'o apelido', 'logradouro' => 'o logradouro', 'numero' => 'o numero', 'bairro' => 'o bairro', 'cidade' => 'a cidade'] as $c => $rotulo) {
        if ($en[$c] === '') { $erros[] = "Informe {$rotulo}."; }
    }
    if (!preg_match('/^\d{5}-?\d{3}$/', $en['cep'])) { $erros[] = 'Informe um CEP no formato 00000-000.'; }
    if (!in_array($en['uf'], listaUfs(), true)) { $erros[] = 'Selecione uma UF valida.'; }

    if (!$erros) {
        $dados = [$en['apelido'], $en['cep'], $en['logradouro'], $en['numero'], $en['complemento'], $en['bairro'], $en['cidade'], $en['uf']];
        if ($id > 0) {
            $pdo->prepare('UPDATE enderecos SET apelido=?, cep=?, logradouro=?, numero=?, complemento=?, bairro=?, cidade=?, uf=? WHERE id=?')
                ->execute([...$dados, $id]);
            redirecionar('enderecos/index.php', 'Endereco atualizado com sucesso.');
        }
        $pdo->prepare('INSERT INTO enderecos (apelido, cep, logradouro, numero, complemento, bairro, cidade, uf) VALUES (?,?,?,?,?,?,?,?)')
            ->execute($dados);
        redirecionar('enderecos/index.php', 'Endereco cadastrado com sucesso.');
    }
}

$pagina = 'enderecos';
$titulo = $id > 0 ? 'Editar endereco' : 'Novo endereco';
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
                <label for="apelido" class="form-label">Apelido (ex.: Deposito Central) *</label>
                <input type="text" class="form-control" id="apelido" name="apelido" maxlength="80" required value="<?= e($en['apelido']) ?>">
            </div>
            <div class="col-md-4">
                <label for="cep" class="form-label">CEP *</label>
                <input type="text" class="form-control" id="cep" name="cep" data-mask="cep" maxlength="9" placeholder="00000-000" required value="<?= e($en['cep']) ?>">
            </div>
            <div class="col-md-8">
                <label for="logradouro" class="form-label">Logradouro *</label>
                <input type="text" class="form-control" id="logradouro" name="logradouro" maxlength="150" required value="<?= e($en['logradouro']) ?>">
            </div>
            <div class="col-md-2">
                <label for="numero" class="form-label">Numero *</label>
                <input type="text" class="form-control" id="numero" name="numero" maxlength="10" required value="<?= e($en['numero']) ?>">
            </div>
            <div class="col-md-2">
                <label for="complemento" class="form-label">Complemento</label>
                <input type="text" class="form-control" id="complemento" name="complemento" maxlength="80" value="<?= e($en['complemento']) ?>">
            </div>
            <div class="col-md-5">
                <label for="bairro" class="form-label">Bairro *</label>
                <input type="text" class="form-control" id="bairro" name="bairro" maxlength="80" required value="<?= e($en['bairro']) ?>">
            </div>
            <div class="col-md-5">
                <label for="cidade" class="form-label">Cidade *</label>
                <input type="text" class="form-control" id="cidade" name="cidade" maxlength="80" required value="<?= e($en['cidade']) ?>">
            </div>
            <div class="col-md-2">
                <label for="uf" class="form-label">UF *</label>
                <select class="form-select" id="uf" name="uf" required>
                    <option value="">--</option>
                    <?php foreach (listaUfs() as $uf): ?>
                        <option value="<?= $uf ?>" <?= $en['uf'] === $uf ? 'selected' : '' ?>><?= $uf ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <button class="btn btn-success" type="submit"><i class="bi bi-check-lg"></i> Salvar</button>
                <a class="btn btn-outline-secondary" href="<?= url('enderecos/index.php') ?>">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
