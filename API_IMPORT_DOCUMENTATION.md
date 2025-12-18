# API d'Importation - Documentation

## Vue d'ensemble

L'API d'importation permet d'importer automatiquement des techniques et des astuces écologiques à partir d'un fichier JSON ou PDF. Le système vérifie automatiquement les doublons et n'importe que les données nouvelles.

## Endpoints

### 1. Importer un fichier (POST)

**URL**: `POST /api/import/pdf`

**Authentification**: Requiert le rôle `ROLE_ADMIN`

**Paramètres**:
- `file` (FormData, obligatoire) : Le fichier à importer (PDF, JSON ou TXT)

**Formats supportés**:
- `application/pdf`
- `application/json`
- `text/plain`

**Limite de taille**: 10 MB maximum

**Réponse en cas de succès (200 OK)**:
```json
{
  "success": true,
  "message": "Importation terminée avec succès",
  "techniques_added": 3,
  "techniques_skipped": 1,
  "tips_added": 4,
  "tips_skipped": 2,
  "errors": []
}
```

**Réponse en cas d'erreur (400/500)**:
```json
{
  "success": false,
  "message": "Erreur lors de l'importation: ...",
  "techniques_added": 0,
  "techniques_skipped": 0,
  "tips_added": 0,
  "tips_skipped": 0,
  "errors": ["Technique: Nom - Message d'erreur"]
}
```

### 2. Obtenir un exemple de format (GET)

**URL**: `GET /api/import/example`

**Authentification**: Aucune requise

**Réponse**: Retourne un exemple complet du format JSON accepté

## Format JSON attendu

```json
{
  "techniques": [
    {
      "name": "Nom de la technique",
      "description": "Description détaillée",
      "category": "Art Recyclé|Upcycling|Art Naturel|Art Écologique|Art Durable|Art Zéro Déchet",
      "difficulty": "Facile|Moyen|Difficile",
      "materials": "Liste des matériaux nécessaires",
      "steps": "Étapes numérotées pour réaliser la technique",
      "images": ["image1.jpg", "image2.jpg"]  // optionnel
    }
  ],
  "tips": [
    {
      "title": "Titre de l'astuce",
      "content": "Contenu détaillé de l'astuce",
      "category": "Art Recyclé|Upcycling|Art Naturel|Art Écologique|Art Durable|Art Zéro Déchet",
      "image": ["image.jpg"]  // optionnel
    }
  ]
}
```

## Catégories disponibles

- **Art Recyclé** : Créations à partir de matériaux recyclés
- **Upcycling** : Transformation de matériaux existants
- **Art Naturel** : Création avec des matériaux naturels
- **Art Écologique** : Art respectueux de l'environnement
- **Art Durable** : Art avec certification écologique
- **Art Zéro Déchet** : Créations minimisant les déchets

## Niveaux de difficulté

- **Facile** : Pour les débutants
- **Moyen** : Nécessite quelques compétences
- **Difficile** : Pour les artistes expérimentés

## Mécanisme de détection des doublons

### Pour les techniques:
- Les techniques sont considérées comme doublons si elles ont **le même nom** (insensible à la casse)
- Les doublons ne sont **pas importés** pour éviter les données en double

### Pour les astuces:
- Les astuces sont considérées comme doublons si:
  1. Elles ont **le même titre** exact, OU
  2. Elles commencent par **le même contenu** (premiers 100 caractères)
- Les doublons ne sont **pas importés**

## Processus d'importation

1. **Validation du fichier**
   - Vérification du type MIME
   - Vérification de la taille (max 10 MB)

2. **Parsing du fichier**
   - Si JSON : parsing direct
   - Si PDF/TXT : extraction du contenu (à implémenter)

3. **Import des techniques**
   - Pour chaque technique :
     - Vérifier si elle existe (par nom)
     - Si absent : créer et persister
     - Si présent : incrémenter les doublons skippés

4. **Import des astuces**
   - Pour chaque astuce :
     - Vérifier si elle existe (par titre ou contenu initial)
     - Si absent : créer, associer à l'utilisateur, auto-approuver, persister
     - Si présent : incrémenter les doublons skippés

5. **Flush en base de données**
   - Toutes les entités validées sont sauvegardées

## Exemples d'utilisation

### Avec cURL

```bash
# Importer un fichier JSON
curl -X POST http://localhost:8000/api/import/pdf \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "file=@import-example.json"

# Obtenir l'exemple de format
curl http://localhost:8000/api/import/example
```

### Avec JavaScript/Fetch

```javascript
const fileInput = document.querySelector('input[type="file"]');
const file = fileInput.files[0];

const formData = new FormData();
formData.append('file', file);

fetch('/api/import/pdf', {
  method: 'POST',
  body: formData,
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN'
  }
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error(error));
```

### Avec Python/Requests

```python
import requests

file = open('import-example.json', 'rb')
files = {'file': file}
headers = {'Authorization': 'Bearer YOUR_TOKEN'}

response = requests.post('http://localhost:8000/api/import/pdf', 
                        files=files, 
                        headers=headers)
print(response.json())
```

## Gestion des erreurs

| Code | Message | Cause |
|------|---------|-------|
| 400 | Aucun fichier fourni | Le paramètre `file` est manquant |
| 400 | Type de fichier non supporté | Format différent de PDF/JSON |
| 400 | Le fichier est trop volumineux | Fichier > 10 MB |
| 401 | Unauthorized | Authentification manquante ou invalide |
| 403 | Forbidden | Rôle ROLE_ADMIN requis |
| 500 | Erreur lors de l'importation | Erreur serveur |

## Notes importantes

- ⚠️ Seuls les **administrateurs** peuvent importer
- ✅ Les astuces importées sont **auto-approuvées**
- ✅ Les techniques et astuces **sans doublon** sont automatiquement importées
- 📊 Le réponse inclut un résumé complet : ajouts, doublons détectés, erreurs
- 📝 Tous les **doublons non importés** sont comptabilisés séparément
