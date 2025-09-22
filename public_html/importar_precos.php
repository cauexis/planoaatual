<?php
// importar_precos.php (com conversão de moeda corrigida)
include 'config/db.php';
echo "<h1>Iniciando Importação da Tabela de Preços...</h1>";

$json_file = 'tabela_precos.json';
if (!file_exists($json_file)) { die("ERRO: Arquivo '{$json_file}' não encontrado."); }

$json_data = file_get_contents($json_file);
$faixas = json_decode($json_data, true);
if ($faixas === null) { die("ERRO: Formato do JSON inválido."); }

try {
    $stmt = $conn->prepare(
        "INSERT INTO faixas_de_preco (plano_id, codigo_tabela_preco, idade_minima, idade_maxima, valor_plano, tipo_relacao_dependencia, valor_net, codigo_grupo_contrato) 
         VALUES (:plano_id, :codigo_tabela_preco, :idade_minima, :idade_maxima, :valor_plano, :tipo_relacao_dependencia, :valor_net, :codigo_grupo_contrato)"
    );

    $count = 0;
    foreach ($faixas as $faixa) {
        $codigo_plano_json = $faixa['CODIGO_PLANO'];
        $find_plan_stmt = $conn->prepare("SELECT id FROM planos WHERE codigo_plano = :codigo_plano");
        $find_plan_stmt->execute(['codigo_plano' => $codigo_plano_json]);
        $plano_db = $find_plan_stmt->fetch();

        if ($plano_db) {
            $plano_id_interno = $plano_db['id'];

            // --- LÓGICA DE CONVERSÃO CORRIGIDA ---
            // 1. Remove os pontos de milhar (ex: "1.341,57" -> "1341,57")
            $valor_plano_str = str_replace('.', '', $faixa['VALOR_PLANO']);
            // 2. Troca a vírgula decimal por ponto e converte para número (ex: "1341,57" -> 1341.57)
            $valor_plano = (float) str_replace(',', '.', $valor_plano_str);

            // Faz o mesmo para o VALOR_NET
            $valor_net_str = str_replace('.', '', $faixa['VALOR_NET']);
            $valor_net = (float) str_replace(',', '.', $valor_net_str);
            // --- FIM DA CORREÇÃO ---

            $stmt->execute([
                ':plano_id' => $plano_id_interno,
                ':codigo_tabela_preco' => $faixa['CODIGO_TABELA_PRECO'],
                ':idade_minima' => $faixa['IDADE_MINIMA'],
                ':idade_maxima' => $faixa['IDADE_MAXIMA'],
                ':valor_plano' => $valor_plano,
                ':tipo_relacao_dependencia' => $faixa['TIPO_RELACAO_DEPENDENCIA'],
                ':valor_net' => $valor_net,
                ':codigo_grupo_contrato' => $faixa['CODIGO_GRUPO_CONTRATO']
            ]);
            $count++;
            echo "<p>Faixa de preço para o plano código {$codigo_plano_json} (Idade {$faixa['IDADE_MINIMA']}-{$faixa['IDADE_MAXIMA']}) importada com valor R$ " . number_format($valor_plano, 2, ',', '.') . "</p>";
        } else {
            echo "<p style='color:red;'>AVISO: Plano com código {$codigo_plano_json} não encontrado. Faixa ignorada.</p>";
        }
    }
    echo "<h2>Importação finalizada! {$count} faixas de preço foram adicionadas.</h2>";

} catch(PDOException $e) {
    die("ERRO DURANTE A IMPORTAÇÃO: " . $e->getMessage());
}
?>