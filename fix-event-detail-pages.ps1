$files = Get-ChildItem php/eventos/*.php
foreach ($f in $files) {
    $text = Get-Content -Raw $f
    $text = [regex]::Replace($text, '</html>[\s\S]*$', '', [System.Text.RegularExpressions.RegexOptions]::Singleline)
    $text = $text -replace '<div class="meta-tags">', '<div id="meta-tags" class="meta-tags">'
    $text = $text -replace '<h1 class="detalle-titulo">', '<h1 id="detalle-titulo" class="detalle-titulo">'
    $text = $text -replace '<p class="detalle-descripcion">', '<p id="detalle-descripcion" class="detalle-descripcion">'
    $tail = @'
    <script src="../../js/eventos.js"></script>
    <script src="../../js/detalle-evento.js"></script>
    <script src="../../js/script.js"></script>
    <script src="../../js/ith.js"></script>
</body>
</html>
'@
    $text = $text.TrimEnd() + "`r`n" + $tail.TrimStart()
    Set-Content -Path $f -Value $text -Encoding utf8
    Write-Host "Fixed $f"
}
