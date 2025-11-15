<?php
// config.php - Conexão compatível com Neon + fallback para clientes antigos

$databaseUrl = getenv('DATABASE_URL');

// helper: adiciona o param options=endpoint%3D... à URL (URL-encode do '=')
function addEndpointOptionToUrl($url, $endpoint_id) {
    // Se já houver query, junta com &, senão com ?
    $sep = (strpos($url, '?') === false) ? '?' : '&';
    // options=endpoint%3D<endpoint_id>   (endpoint%3D === "endpoint=" urlencoded)
    return $url . $sep . 'options=endpoint%3D' . rawurlencode($endpoint_id);
}

// Se não houver variable env (modo local), você pode definir aqui como fallback (opcional)
if (!$databaseUrl) {
    // REMOVER / SUBSTITUIR por variáveis de ambiente para não vazar senha
    $databaseUrl = 'postgresql://neondb_owner:npg_iFdzG1RAQcw6@ep-bold-unit-admbc0is-pooler.c-2.us-east-1.aws.neon.tech/neondb?sslmode=require&channel_binding=require';
}

$parts = parse_url($databaseUrl);
if ($parts === false || !isset($parts['host'])) {
    die("DATABASE_URL inválida ou incompleta. Verifique a variável de ambiente.");
}

$host = $parts['host'];
$port = isset($parts['port']) ? $parts['port'] : 5432;
$user = isset($parts['user']) ? $parts['user'] : null;
$password = isset($parts['pass']) ? $parts['pass'] : null;
$dbname = isset($parts['path']) ? ltrim($parts['path'], '/') : null;

// Derive endpoint id (parte antes de "-pooler" se existir; caso contrário, a primeira label do host)
if (strpos($host, '-pooler') !== false) {
    $endpoint_id = substr($host, 0, strpos($host, '-pooler'));
} else {
    $endpoint_id = explode('.', $host)[0];
}

// Tenta PDO normal (ideal no Render, quando libpq tem SNI)
$dsn_pdo = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";

try {
    $pdo = new PDO($dsn_pdo, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    // sucesso
    // echo "Conectado via PDO normalmente.";
    return; // ou continue com $pdo disponível globalmente
} catch (PDOException $e) {
    $msg = $e->getMessage();

    // Se o erro for de host inválido/truncado, pare e alerte
    if (stripos($msg, 'could not translate host name') !== false || stripos($host, '...') !== false) {
        die("Erro DNS/host: verifique se DATABASE_URL contém o host completo (sem '...') e sem quebras de linha. Host atual: '$host'");
    }

    // Se for erro de endpoint/SNI, tenta fallback com options=endpoint%3D...
    if (stripos($msg, 'Endpoint ID is not specified') !== false
        || stripos($msg, 'SNI') !== false
        || stripos($msg, 'inconsistent project name') !== false) {

        // Tentar conectar via pg_connect usando a URL com options (isso usa libpq URL style)
        $url_with_options = addEndpointOptionToUrl($databaseUrl, $endpoint_id);

        // pg_connect aceita URL-style ou connection strings; usamos a URL completa
        $conn = @pg_connect($url_with_options);

        if ($conn) {
            // sucesso com fallback
            // echo "Conectado via pg_connect com options endpoint (fallback para cliente antigo).";
            return;
        } else {
            // busca erro do pg
            $pg_err = pg_last_error();
            die("Fallback com 'options=endpoint' falhou: " . ($pg_err ? $pg_err : $msg));
        }
    }

    // Outro erro: mostrar mensagem
    die("Erro na conexão com o banco de dados (PDO): " . $msg);
}
?>