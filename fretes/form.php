<?php
require_once __DIR__ . '/../includes/helpers.php';

$pdo = db();
$id = (int) ($_GET['id'] ?? 0);
$erros = [];

$transportadores = $pdo->query('SELECT id, nome, placa FROM transportadores ORDER BY nome')->fetchAll();
$enderecos = $pdo->query('SELECT id, apelido, cidade, uf FROM enderecos ORDER BY apelido')->fetchAll();
$produtos = $pdo->query('SELECT id, nome FROM produtos ORDER BY nome')->fetchAll();

$f = [
    'transportador_id' => '', 'origem_id' => '', 'destino_id' => '',
    'data_frete' => date('Y-m-d'), 'valor_frete' => '', 'observacao' => '',
];
$itens = [['produto_id' => '', 'quantidade' => 1]];

if ($id > 0) {
    $st = $pdo->prepare('SELECT * FROM fretes WHERE id = ?');
    $st->execute([$id]);
    $f = $st->fetch() ?: redirecionar('fretes/index.php', 'Frete nao encontrado.', 'danger');
    $st = $pdo->prepare('SELECT produto_id, quantidade FROM frete_produtos WHERE frete_id = ? ORDER BY id');
    $st->execute([$id]);
    $itens = $st->fetchAll() ?: $itens;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $f = [
        'transportador_id' => (int) post('transportador_id'),
        'origem_id'        => (int) post('origem_id'),
        'destino_id'       => (int) post('destino_id'),
        'data_frete'       => post('data_frete'),
        'valor_frete'      => post('valor_frete'),
        'observacao'       => post('observacao'),
    ];

    // Junta as linhas de produto (soma quantidades de produtos repetidos).
    $agrupados = [];
    foreach ((array) ($_POST['produto_id'] ?? []) as $i => $pid) {
        $pid = (int) $pid;
        $qtd = (int) ($_POST['quantidade'][$i] ?? 0);
        if ($pid > 0 && $qtd > 0) {
            $agrupados[$pid] = ($agrupados[$pid] ?? 0) + $qtd;
        }
    }
    $itens = [];
    foreach ($agrupados as $pid => $qtd) {
        $itens[] = ['produto_id' => $pid, 'quantidade' => $qtd];
    }

    if ($f['transportador_id'] <= 0) { $erros[] = 'Selecione o transportador.'; }
    if ($f['origem_id'] <= 0) { $erros[] = 'Selecione o endereco de origem.'; }
    if ($f['destino_id'] <= 0) { $erros[] = 'Selecione o endereco de destino.'; }
    if ($f['origem_id'] > 0 && $f['origem_id'] === $f['destino_id']) { $erros[] = 'Origem e destino devem ser diferentes.'; }
    if (!dataValida($f['data_frete'])) { $erros[] = 'Informe uma data valida.'; }
    if (!is_numeric($f['valor_frete']) || (float) $f['valor_frete'] < 0) { $erros[] = 'Informe um valor de frete valido.'; }
    if (!$itens) { $erros[] = 'Adicione pelo menos um produto ao frete.'; }
    if (!$itens) { $itens = [['produto_id' => '', 'quantidade' => 1]]; }

    if (!$erros) {
        $pdo->beginTransaction();
        try {
            $dados = [$f['transportador_id'], $f['origem_id'], $f['destino_id'], $f['data_frete'], (float) $f['valor_frete'], $f['observacao']];
            if ($id > 0) {
                $pdo->prepare('UPDATE fretes SET transportador_id=?, origem_id=?, destino_id=?, data_frete=?, valor_frete=?, observacao=? WHERE id=?')
                    ->execute([...$dados, $id]);
                $pdo->prepare('DELETE FROM frete_produtos WHERE frete_id = ?')->execute([$id]);
                $freteId = $id;
            } else {
                $pdo->prepare('INSERT INTO fretes (transportador_id, origem_id, destino_id, data_frete, valor_frete, observacao) VALUES (?,?,?,?,?,?)')
                    ->execute($dados);
                $freteId = (int) $pdo->lastInsertId();
            }
            $ins = $pdo->prepare('INSERT INTO frete_produtos (frete_id, produto_id, quantidade) VALUES (?,?,?)');
            foreach ($itens as $item) {
                $ins->execute([$freteId, $item['produto_id'], $item['quantidade']]);
            }
            $pdo->commit();
            redirecionar('fretes/ver.php?id=' . $freteId, $id > 0 ? 'Frete atualizado com sucesso.' : 'Frete gerado com sucesso.');
        } catch (PDOException $ex) {
            $pdo->rollBack();
            $erros[] = 'Erro ao salvar o frete. Confira se os cadastros selecionados existem.';
        }
    }
}

$pagina = 'fretes';
$titulo = $id > 0 ? 'Editar frete' : 'Gerar frete';
require_once __DIR__ . '/../includes/header.php';

/** Imprime as <option> de um select marcando a selecionada. */
function opcoes(array $linhas, $selecionado, callable $rotulo): void
{
    echo '<option value="">Selecione...</option>';
    foreach ($linhas as $l) {
        $sel = (int) $selecionado === (int) $l['id'] ? ' selected' : '';
        echo '<option value="' . (int) $l['id'] . '"' . $sel . '>' . e($rotulo($l)) . '</option>';
    }
}
?>
<h1 class="h3 mb-3"><?= e($titulo) ?></h1>

<?php foreach ($erros as $erro): ?>
    <div class="alert alert-danger py-2"><?= e($erro) ?></div>
<?php endforeach; ?>

<?php if (!$transportadores || count($enderecos) < 2 || !$produtos): ?>
    <div class="alert alert-warning">
        Para gerar um frete cadastre antes: pelo menos <strong>1 transportador</strong>,
        <strong>2 enderecos</strong> (origem e destino) e <strong>1 produto</strong>.
    </div>
<?php endif; ?>

<form method="post">
    <div class="card mb-3">
        <div class="card-header bg-white fw-semibold">Dados do frete</div>
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label for="transportador_id" class="form-label">Transportador *</label>
                <select class="form-select" id="transportador_id" name="transportador_id" required>
                    <?php opcoes($transportadores, $f['transportador_id'], fn($t) => $t['nome'] . ($t['placa'] ? ' (' . $t['placa'] . ')' : '')); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="data_frete" class="form-label">Data *</label>
                <input type="date" class="form-control" id="data_frete" name="data_frete" required value="<?= e($f['data_frete']) ?>">
            </div>
            <div class="col-md-3">
                <label for="valor_frete" class="form-label">Valor do frete (R$) *</label>
                <input type="number" step="0.01" min="0" class="form-control" id="valor_frete" name="valor_frete" required value="<?= e($f['valor_frete']) ?>">
            </div>
            <div class="col-md-6">
                <label for="origem_id" class="form-label">Origem *</label>
                <select class="form-select" id="origem_id" name="origem_id" required>
                    <?php opcoes($enderecos, $f['origem_id'], fn($e) => $e['apelido'] . ' - ' . $e['cidade'] . '/' . $e['uf']); ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="destino_id" class="form-label">Destino *</label>
                <select class="form-select" id="destino_id" name="destino_id" required>
                    <?php opcoes($enderecos, $f['destino_id'], fn($e) => $e['apelido'] . ' - ' . $e['cidade'] . '/' . $e['uf']); ?>
                </select>
            </div>
            <div class="col-12">
                <label for="observacao" class="form-label">Observacao</label>
                <input type="text" class="form-control" id="observacao" name="observacao" maxlength="255" value="<?= e($f['observacao']) ?>">
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Produtos transportados</span>
            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-item"><i class="bi bi-plus-lg"></i> Adicionar produto</button>
        </div>
        <div class="card-body" id="itens">
            <?php foreach ($itens as $item): ?>
                <div class="row g-2 item-frete mb-2">
                    <div class="col-md-8">
                        <select name="produto_id[]" class="form-select" required>
                            <?php opcoes($produtos, $item['produto_id'], fn($p) => $p['nome']); ?>
                        </select>
                    </div>
                    <div class="col-8 col-md-3">
                        <input type="number" name="quantidade[]" min="1" value="<?= (int) $item['quantidade'] ?>" class="form-control" required aria-label="Quantidade">
                    </div>
                    <div class="col-4 col-md-1">
                        <button type="button" class="btn btn-outline-danger w-100 remover-item" title="Remover">&times;</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <template id="modelo-item">
        <div class="row g-2 item-frete mb-2">
            <div class="col-md-8">
                <select name="produto_id[]" class="form-select" required>
                    <?php opcoes($produtos, '', fn($p) => $p['nome']); ?>
                </select>
            </div>
            <div class="col-8 col-md-3">
                <input type="number" name="quantidade[]" min="1" value="1" class="form-control" required aria-label="Quantidade">
            </div>
            <div class="col-4 col-md-1">
                <button type="button" class="btn btn-outline-danger w-100 remover-item" title="Remover">&times;</button>
            </div>
        </div>
    </template>

    <button class="btn btn-success" type="submit"><i class="bi bi-check-lg"></i> Salvar frete</button>
    <a class="btn btn-outline-secondary" href="<?= url('fretes/index.php') ?>">Cancelar</a>
</form>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
