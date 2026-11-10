<?php
/**
 * Gerador de PDF minimalista (texto, A4, Helvetica) em PHP puro.
 * Nao depende de bibliotecas externas, entao funciona direto no XAMPP.
 */
class SimplePDF
{
    private const LARGURA = 595.28;
    private const ALTURA  = 841.89;
    private const MARGEM  = 42.0;

    /** @var string[] conteudo (operadores PDF) de cada pagina */
    private array $paginas = [];
    private string $atual = '';
    private float $y;

    public function __construct()
    {
        $this->novaPagina();
    }

    public function novaPagina(): void
    {
        if ($this->atual !== '') {
            $this->paginas[] = $this->atual;
        }
        $this->atual = '';
        $this->y = self::ALTURA - self::MARGEM;
    }

    /** Escreve uma linha de texto (com quebra automatica de linha longa). */
    public function linha(string $texto, int $tamanho = 10, bool $negrito = false, float $recuo = 0): void
    {
        $largura = self::LARGURA - 2 * self::MARGEM - $recuo;
        $maxCaracteres = max(10, (int) floor($largura / ($tamanho * 0.5)));
        $partes = explode("\n", wordwrap($texto, $maxCaracteres, "\n", true));
        foreach ($partes as $parte) {
            $this->garantirEspaco($tamanho * 1.45);
            $this->texto($parte, self::MARGEM + $recuo, $this->y - $tamanho, $tamanho, $negrito);
            $this->y -= $tamanho * 1.45;
        }
    }

    public function espaco(float $pontos = 8): void
    {
        $this->y -= $pontos;
    }

    /** Linha horizontal separadora. */
    public function separador(): void
    {
        $this->garantirEspaco(10);
        $y = $this->y - 4;
        $this->atual .= sprintf("0.7 w 0.7 G %.2f %.2f m %.2f %.2f l S\n",
            self::MARGEM, $y, self::LARGURA - self::MARGEM, $y);
        $this->y -= 10;
    }

    /** Garante que cabe um bloco de $pontos de altura; senao abre nova pagina. */
    public function garantirEspaco(float $pontos): void
    {
        if ($this->y - $pontos < self::MARGEM + 20) {
            $this->novaPagina();
        }
    }

    private function texto(string $s, float $x, float $y, int $tamanho, bool $negrito): void
    {
        $this->atual .= sprintf("BT /%s %d Tf %.2f %.2f Td (%s) Tj ET\n",
            $negrito ? 'F2' : 'F1', $tamanho, $x, $y, $this->escapar($s));
    }

    /** UTF-8 -> Windows-1252 (acentos do portugues) e escape de ( ) \ */
    private function escapar(string $s): string
    {
        $s = mb_convert_encoding($s, 'Windows-1252', 'UTF-8');
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $s);
    }

    /** Gera o binario do PDF. */
    public function gerar(): string
    {
        $paginas = $this->paginas;
        $paginas[] = $this->atual;
        $total = count($paginas);

        // Rodape com numeracao em todas as paginas
        foreach ($paginas as $i => &$conteudo) {
            $conteudo .= sprintf("BT /F1 8 Tf %.2f %.2f Td (%s) Tj ET\n",
                self::MARGEM, 24, $this->escapar('Pagina ' . ($i + 1) . ' de ' . $total));
        }
        unset($conteudo);

        // Objetos: 1 catalogo, 2 paginas, 3 e 4 fontes, depois (conteudo, pagina) por pagina
        $objetos = [];
        $filhos = [];
        for ($i = 0; $i < $total; $i++) {
            $idConteudo = 5 + $i * 2;
            $idPagina = $idConteudo + 1;
            $filhos[] = "$idPagina 0 R";
            $objetos[$idConteudo] = '<< /Length ' . strlen($paginas[$i]) . " >>\nstream\n" . $paginas[$i] . 'endstream';
            $objetos[$idPagina] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2f %.2f] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents %d 0 R >>',
                self::LARGURA, self::ALTURA, $idConteudo
            );
        }
        $objetos[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objetos[2] = '<< /Type /Pages /Kids [' . implode(' ', $filhos) . "] /Count $total >>";
        $objetos[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objetos[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';
        ksort($objetos);

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objetos as $id => $corpo) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "$id 0 obj\n$corpo\nendobj\n";
        }
        $xref = strlen($pdf);
        $n = count($objetos) + 1;
        $pdf .= "xref\n0 $n\n0000000000 65535 f \n";
        foreach ($offsets as $off) {
            $pdf .= sprintf("%010d 00000 n \n", $off);
        }
        $pdf .= "trailer\n<< /Size $n /Root 1 0 R >>\nstartxref\n$xref\n%%EOF";
        return $pdf;
    }

    /** Envia o PDF para download no navegador. */
    public function baixar(string $nomeArquivo): never
    {
        $conteudo = $this->gerar();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');
        header('Content-Length: ' . strlen($conteudo));
        echo $conteudo;
        exit;
    }
}
