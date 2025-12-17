<?php
require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

echo "<h1>🔍 Vérification API Reset Password</h1>";

// Teste DIRECTEMENT l'URL API
$apiUrl = 'http://localhost:8000/api/reset-password/check-email';
$postData = json_encode(['email' => 'azizbenabdallah0412@gmail.com']);

echo "<h2>1. Test de l'URL API : {$apiUrl}</h2>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

curl_close($ch);

echo "<div style='border:2px solid #007bff; padding:15px; border-radius:10px;'>";
echo "<h3>Code HTTP : {$httpCode}</h3>";

echo "<h3>Headers :</h3>";
echo "<pre style='background:#f8f9fa; padding:10px;'>" . htmlspecialchars($headers) . "</pre>";

echo "<h3>Body :</h3>";

// Si c'est du JSON
if (strpos($headers, 'Content-Type: application/json') !== false) {
    echo "<pre style='background:#d4edda; padding:10px;'>" . htmlspecialchars($body) . "</pre>";
} else {
    // C'est du HTML (erreur Symfony)
    echo "<div style='background:#f8d7da; padding:15px; border-radius:5px;'>";
    echo "<h4 style='color:#721c24;'>⚠️ ERREUR : L'API retourne du HTML au lieu de JSON</h4>";
    echo "<p>Le serveur retourne une page d'erreur Symfony.</p>";
    echo "<details><summary>Voir le contenu HTML</summary>";
    echo "<div style='background:#f5f5f5; padding:10px; margin-top:10px; max-height:300px; overflow:auto;'>";
    echo htmlspecialchars($body);
    echo "</div></details>";
    echo "</div>";
}

echo "</div>";

// Teste aussi l'interface web normale
echo "<h2>2. Test de l'interface web normale</h2>";
echo "<div style='border:2px solid #28a745; padding:15px; border-radius:10px;'>";
echo "<p><strong>URL :</strong> http://localhost:8000/reset-password</p>";
echo "<p><a href='http://localhost:8000/reset-password' target='_blank'
          style='padding:10px 20px; background:#28a745; color:white; text-decoration:none; border-radius:5px;'>
    📱 Ouvrir l'interface Reset Password
</a></p>";
echo "</div>";

// Vérifie les routes disponibles
echo "<h2>3. Routes disponibles (via commande)</h2>";
echo "<pre style='background:#f8f9fa; padding:10px;'>";
echo "Pour voir les routes : php bin/console debug:router | grep -i password";
echo "</pre>";

echo "<h2>4. Solution RAPIDE</h2>";
echo "<div style='border:2px solid #ffc107; padding:15px; border-radius:10px; background:#fff3cd;'>";
echo "<h3>🎯 Utilise l'interface web NORMALE :</h3>";
echo "<form action='http://localhost:8000/reset-password/handle-request' method='post' target='_blank'
      style='margin:10px 0;'>
    <input type='hidden' name='email' value='azizbenabdallah0412@gmail.com'>
    <button type='submit' style='padding:10px 20px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer;'>
        🚀 Tester DIRECTEMENT le Controller Web
    </button>
</form>";

echo "<h3>🔗 Lien de test manuel :</h3>";
echo "<p><a href='http://localhost:8000/reset-password/reset/test-manual-token' target='_blank'
      style='padding:10px 20px; background:#ffc107; color:#000; text-decoration:none; border-radius:5px; display:inline-block;'>
    🔗 Tester la page de réinitialisation
</a></p>";
echo "</div>";