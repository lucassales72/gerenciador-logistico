<?php
require_once __DIR__ . '/../includes/helpers.php';

$pdo = db();
$id = (int) ($_GET['id'] ?? 0);
$erros = [];
$campos = ['nome', 'cpf_cnpj', 'telefone', 'veiculo', 'placa'];
$t = array_fill_keys($campos, '');

if ($id > 0) {
    $st = $pdo->prepare('SELECT * FROM transportadores WHERE id = ?');
    $st->execute([$id]);
    $t = $st->fetch() ?: redirecionar('transportadores/index.php', 'Transportador nao encontrado.', 'danger');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($campos as $c) {
        $t[$c] = post($c);
    }
    $t['placa'] = strtoupper($t['placa']);

    if ($t['nome'] === '') { $erros[] = 'Informe o nome do transportador.'; }
    // Nao valida a autenticidade do CPF/CNPJ (conforme o roteiro), so exige o preenchimento.
    if ($t['cpf_cnpj'] === '') { $erros[] = 'Informe o CPF ou CNPJ.'; }

    if (!$erros) {
        $dados = [$t['nome'], $t['cpf_cnpj'], $t['telefone'], $t['veiculo'], $t['placa']];
        if ($id > 0) {
            $pdo->prepare('UPDATE transportadores SET nome=?, cpf_cnpj=?, telefone=?, veiculo=?, placa=? WHERE id=?')
                ->execute([...$dados, $id]);
            redirecionar('transportadores/index.php', 'Transportador atualizado com sucesso.');
        }
        $pdo->prepare('INSERT INTO transportadores (nome, cpf_cnpj, telefone, veiculo, placa) VALUES (?,?,?,?,?)')->execute($dados);
        redirecionar('transportadores/index.php', 'Transportador cadastrado com sucesso.');
    }
}

$pagina = 'transportadores';
$titulo = $id > 0 ? 'Editar transportador' : 'Novo transportador';
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
                <label for="nome" class="form-label">Nome / Razao social *</label>
                <input type="text" class="form-control" id="nome" name="nome" maxlength="120" required value="<?= e($t['nome']) ?>">
            </div>
            <div class="col-md-4">
                <label for="cpf_cnpj" class="form-label">CPF/CNPJ *</label>
                <input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj" maxlength="18" required value="<?= e($t['cpf_cnpj']) ?>">
            </div>
            <div class="col-md-4">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" class="form-control" id="telefone" name="telefone" maxlength="20" value="<?= e($t['telefone']) ?>">
            </div>
            <div class="col-md-5">
                <label for="veiculo" class="form-label">Veiculo</label>
                <input type="text" class="form-control" id="veiculo" name="veiculo" maxlength="60" placeholder="Ex.: Caminhao bau" value="<?= e($t['veiculo']) ?>">
            </div>
            <div class="col-md-3">
                <label for="placa" class="form-label">Placa</label>
                <input type="text" class="form-control" id="placa" name="placa" data-mask="upper" maxlength="8" value="<?= e($t['placa']) ?>">
            </div>
            <div class="col-12">
                <button class="btn btn-success" type="submit"><i class="bi bi-check-lg"></i> Salvar</button>
                <a class="btn btn-outline-secondary" href="<?= url('transportadores/index.php') ?>">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
