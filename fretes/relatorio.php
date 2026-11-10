<?php
/**
 * Relatorio de fretes de um dia, em PDF.
 * Uso: fretes/relatorio.php?data=AAAA-MM-DD
 */
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/SimplePDF.php';

$data = $_GET['data'] ?? '';
if (!dataValida($data)) {
    redirecionar('fretes/index.php', 'Escolha uma data valida para gerar o relatorio.', 'warning');
}

$pdo = db();
$st = $pdo->prepare('SELECT f.*, t.nome AS t_nome, t.placa, t.veiculo,
                            o.apelido AS o_apelido, o.cidade AS o_cidade, o.uf AS o_uf,
                            d.apelido AS d_apelido, d.cidade AS d_cidade, d.uf AS d_uf
                     FROM fretes f
                     JOIN transportadores t ON t.id = f.transportador_id
                     JOIN enderecos o ON o.id = f.origem_id
                     JOIN enderecos d ON d.id = f.destino_id
                     WHERE f.data_frete = ? ORDER BY f.id');
$st->execute([$data]);
$fretes = $st->fetchAll();

if (!$fretes) {
    redirecionar('fretes/index.php?data=' . urlencode($data), 'Nenhum frete encontrado em ' . dataBr($data) . '.', 'warning');
}

$stItens = $pdo->prepare('SELECT p.nome, p.peso_kg, fp.quantidade FROM frete_produtos fp
                          JOIN produtos p ON p.id = fp.produto_id WHERE fp.frete_id = ? ORDER BY p.nome');

$pdf = new SimplePDF();
$pdf->linha('Relatorio de Fretes', 18, true);
$pdf->linha('Data: ' . dataBr($data) . '   |   Gerado em ' . date('d/m/Y H:i'), 10);
$pdf->separador();

$totalValor = 0.0;
$totalPeso = 0.0;
foreach ($fretes as $f) {
    $stItens->execute([$f['id']]);
    $itens = $stItens->fetchAll();

    $pdf->garantirEspaco(90 + count($itens) * 14);
    $pdf->linha('Frete #' . $f['id'] . ' - ' . $f['t_nome'] . ($f['placa'] ? ' (' . $f['placa'] . ')' : ''), 12, true);
    $pdf->linha('Origem: ' . $f['o_apelido'] . ' (' . $f['o_cidade'] . '/' . $f['o_uf'] . ')', 10, false, 12);
    $pdf->linha('Destino: ' . $f['d_apelido'] . ' (' . $f['d_cidade'] . '/' . $f['d_uf'] . ')', 10, false, 12);
    $pdf->linha('Valor do frete: ' . moeda($f['valor_frete']), 10, true, 12);
    if ($f['observacao']) {
        $pdf->linha('Obs.: ' . $f['observacao'], 10, false, 12);
    }
    $pdf->linha('Produtos:', 10, true, 12);
    $pesoFrete = 0.0;
    foreach ($itens as $i) {
        $pesoLinha = $i['peso_kg'] * $i['quantidade'];
        $pesoFrete += $pesoLinha;
        $pdf->linha('- ' . $i['quantidade'] . 'x ' . $i['nome'] . ' (' . number_format($pesoLinha, 3, ',', '.') . ' kg)', 10, false, 24);
    }
    $pdf->linha('Peso total: ' . number_format($pesoFrete, 3, ',', '.') . ' kg', 10, false, 12);
    $pdf->separador();

    $totalValor += (float) $f['valor_frete'];
    $totalPeso += $pesoFrete;
}

$pdf->garantirEspaco(60);
$pdf->linha('Resumo do dia', 12, true);
$pdf->linha('Quantidade de fretes: ' . count($fretes), 10);
$pdf->linha('Peso total transportado: ' . number_format($totalPeso, 3, ',', '.') . ' kg', 10);
$pdf->linha('Valor total dos fretes: ' . moeda($totalValor), 10, true);

$pdf->baixar('relatorio-fretes-' . $data . '.pdf');
