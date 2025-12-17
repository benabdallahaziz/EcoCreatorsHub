<?php
require __DIR__.'/../vendor/autoload.php';

use App\Entity\User;
use Symfony\Component\Dotenv\Dotenv;

// Charge l'environnement
$dotenv = new Dotenv();
if (file_exists(__DIR__.'/../.env.local')) {
    $dotenv->load(__DIR__.'/../.env.local');
} elseif (file_exists(__DIR__.'/../.env')) {
    $dotenv->load(__DIR__.'/../.env');
}

// Valeurs par défaut pour PHP ancien
$app_env = isset($_ENV['APP_ENV']) ? $_ENV['APP_ENV'] : 'dev';
$app_debug = isset($_ENV['APP_DEBUG']) ? (bool) $_ENV['APP_DEBUG'] : true;

// Crée le kernel
$kernel = new \App\Kernel($app_env, $app_debug);
$kernel->boot();
$container = $kernel->getContainer();

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>DEBUG FINAL - Reset Password</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .step { background: white; padding: 20px; border-radius: 10px; margin: 20px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .success { border-left: 5px solid #28a745; }
        .error { border-left: 5px solid #dc3545; }
        .warning { border-left: 5px solid #ffc107; }
        .btn { padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        .btn-api { background: #007bff; }
        .btn-web { background: #ffc107; color: #000; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔧 DEBUG FINAL - Reset Password</h1>";

try {
    echo "<div class='step success'>
            <h2>Étape 1 : Vérification du kernel</h2>
            <p><strong>Environment :</strong> " . $app_env . "</p>
            <p><strong>Debug :</strong> " . ($app_debug ? 'OUI' : 'NON') . "</p>
          </div>";

    echo "<div class='step'>
            <h2>Étape 2 : Vérification utilisateur</h2>";

    $entityManager = $container->get('doctrine.orm.entity_manager');
    $user = $entityManager->getRepository(User::class)->findOneBy(array(
        'email' => 'azizbenabdallah0412@gmail.com'
    ));

    if (!$user) {
        echo "<div class='error'>❌ Utilisateur non trouvé</div>";
        exit;
    }

    echo "<div class='success'>
            <h3>✅ UTILISATEUR TROUVÉ</h3>
            <p><strong>ID :</strong> " . $user->getId() . "</p>
            <p><strong>Email :</strong> " . $user->getEmail() . "</p>
            <p><strong>Username :</strong> " . $user->getUsername() . "</p>
          </div>";

    echo "</div>";

    echo "<div class='step'>
            <h2>Étape 3 : Vérification services disponibles</h2>";

    // Liste des services liés à reset-password
    $services = array(
        'symfonycasts.reset_password.helper',
        'mailer',
        'doctrine.orm.entity_manager'
    );

    echo "<table style='width:100%; border-collapse:collapse;'>
            <tr style='background:#f8f9fa;'>
                <th style='padding:10px; border:1px solid #ddd;'>Service</th>
                <th style='padding:10px; border:1px solid #ddd;'>Statut</th>
            </tr>";

    foreach ($services as $service) {
        try {
            $serviceObj = $container->get($service);
            echo "<tr>
                    <td style='padding:10px; border:1px solid #ddd;'>" . $service . "</td>
                    <td style='padding:10px; border:1px solid #ddd; color:#28a745;'>✅ DISPONIBLE</td>
                  </tr>";
        } catch (\Exception $e) {
            echo "<tr>
                    <td style='padding:10px; border:1px solid #ddd;'>" . $service . "</td>
                    <td style='padding:10px; border:1px solid #ddd; color:#dc3545;'>❌ NON DISPONIBLE</td>
                  </tr>";
        }
    }

    echo "</table>";

    echo "</div>";

    echo "<div class='step warning'>
            <h2>Étape 4 : SOLUTIONS IMMÉDIATES</h2>

            <h3>Option A : Réinstaller le bundle</h3>
            <pre>composer require symfonycasts/reset-password-bundle</pre>

            <h3>Option B : Vérifier le fichier bundles.php</h3>
            <p>Ouvre <code>config/bundles.php</code> et assure-toi que cette ligne existe :</p>
            <pre>SymfonyCasts\\Bundle\\ResetPassword\\SymfonyCastsResetPasswordBundle::class => ['all' => true],</pre>
          </div>";

    echo "<div class='step'>
            <h2>Étape 5 : Tests pratiques</h2>

            <h3>Test 1 : Interface Web</h3>
            <form action='http://localhost:8000/reset-password/handle-request' method='post' target='_blank'>
                <input type='hidden' name='email' value='azizbenabdallah0412@gmail.com'>
                <button type='submit' class='btn btn-web'>🌐 Tester Interface Web</button>
            </form>

            <h3>Test 2 : API Directe</h3>
            <button onclick=\"testApi()\" class='btn btn-api'>🔌 Tester API Directe</button>

            <h3>Test 3 : Créer un token MANUEL (solution de secours)</h3>
            <button onclick=\"createManualToken()\" class='btn'>🔧 Créer Token Manuel</button>

            <div id='api-result' style='margin-top:20px;'></div>
          </div>";

} catch (\Exception $e) {
    echo "<div class='step error'>
            <h2>❌ ERREUR GÉNÉRALE</h2>
            <p><strong>Message :</strong> " . $e->getMessage() . "</p>
            <pre>" . $e->getTraceAsString() . "</pre>
          </div>";
}

echo "
<script>
function testApi() {
    var result = document.getElementById('api-result');
    result.innerHTML = 'Test API en cours...';

    fetch('/api/reset-password/request', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            email: 'azizbenabdallah0412@gmail.com'
        })
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        var html = '<div style=\"padding:15px; background:#d4edda; border-radius:5px;\">';
        html += '<h4>✅ Réponse API :</h4>';
        html += '<pre>' + JSON.stringify(data, null, 2) + '</pre>';

        if (data.token) {
            html += '<p><a href=\"/reset-password/reset/' + data.token + '\" target=\"_blank\" style=\"color:#007bff;\">';
            html += '🔗 Cliquer ici pour tester le token</a></p>';
        }

        html += '</div>';
        result.innerHTML = html;
    })
    .catch(function(error) {
        result.innerHTML = '<div style=\"padding:15px; background:#f8d7da; border-radius:5px;\">';
        result.innerHTML += '<h4>❌ Erreur API :</h4>';
        result.innerHTML += '<p>' + error.message + '</p>';
        result.innerHTML += '</div>';
    });
}

function createManualToken() {
    var token = 'manual-token-' + Date.now();
    var url = 'http://localhost:8000/reset-password/reset/' + token;

    var result = document.getElementById('api-result');
    result.innerHTML = '<div style=\"padding:15px; background:#fff3cd; border-radius:5px;\">';
    result.innerHTML += '<h4>🔧 Token Manuel Créé :</h4>';
    result.innerHTML += '<p><strong>Token :</strong> ' + token + '</p>';
    result.innerHTML += '<p><a href=\"' + url + '\" target=\"_blank\" style=\"color:#007bff;\">';
    result.innerHTML += '🔗 Tester cette page de réinitialisation</a></p>';
    result.innerHTML += '</div>';
}
</script>
</body>
</html>";