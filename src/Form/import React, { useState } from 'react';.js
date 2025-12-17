import React, { useState } from 'react';
import { Heart, Brain, Users, TrendingUp, Target, DollarSign, Globe, ChevronLeft, ChevronRight, Zap, Shield, Award, BarChart3, Lightbulb, CheckCircle, AlertCircle, Smartphone } from 'lucide-react';

const MoodSyncPresentation = () => {
  const [currentSlide, setCurrentSlide] = useState(0);

  const slides = [
    // Slide 1 - Introduction / Accroche
    {
      title: "",
      content: (
        <div className="flex flex-col items-center justify-center h-full space-y-10">
          <div className="relative">
            <div className="absolute inset-0 bg-gradient-to-r from-pink-400 to-blue-400 blur-3xl opacity-30 animate-pulse"></div>
            <div className="relative flex items-center justify-center">
              <Heart className="w-32 h-32 text-pink-500 animate-pulse" strokeWidth={1.5} />
              <Brain className="w-20 h-20 text-blue-500 absolute -bottom-3 -right-3" />
            </div>
          </div>
          
          <div className="text-center space-y-6">
            <h1 className="text-8xl font-black bg-gradient-to-r from-pink-600 via-purple-600 to-blue-600 bg-clip-text text-transparent">
              MoodSync
            </h1>
            <div className="bg-gradient-to-r from-pink-100 to-blue-100 px-8 py-4 rounded-2xl inline-block">
              <p className="text-3xl text-gray-800 font-semibold italic">
                "Ton équilibre numérique commence ici"
              </p>
            </div>
            <div className="h-1 w-64 bg-gradient-to-r from-pink-500 to-blue-500 mx-auto rounded-full"></div>
          </div>

          <div className="grid grid-cols-7 gap-2 text-xs w-full max-w-5xl">
            {[
              { name: 'Fadi Saidi', color: 'pink' },
              { name: 'Feryel Lassili', color: 'blue' },
              { name: 'Yossra Manai', color: 'purple' },
              { name: 'Ahlem Guesmi', color: 'green' },
              { name: 'Nadine Sanekly', color: 'orange' },
              { name: 'Rached Hadj Amor', color: 'teal' },
              { name: 'M. Zayen', color: 'indigo' }
            ].map((member, i) => (
              <div key={i} className={`bg-gradient-to-br from-${member.color}-50 to-${member.color}-100 p-3 rounded-xl shadow-md hover:shadow-lg transition`}>
                <Users className={`w-6 h-6 text-${member.color}-600 mx-auto mb-1`} />
                <p className="text-gray-700 font-medium text-center leading-tight">{member.name}</p>
              </div>
            ))}
          </div>

          <div className="bg-gradient-to-r from-pink-500 to-blue-500 px-6 py-3 rounded-full shadow-xl">
            <p className="text-white font-bold text-lg">🇹🇳 Innovation 100% Tunisienne</p>
          </div>
        </div>
      )
    },

    // Slide 2 - Problématique
    {
      title: "Problématique : Une Crise Silencieuse",
      content: (
        <div className="space-y-8">
          <div className="bg-gradient-to-r from-red-50 to-orange-50 border-l-4 border-red-500 p-6 rounded-r-2xl shadow-lg">
            <h3 className="text-3xl font-bold text-red-700 mb-3 flex items-center">
              <AlertCircle className="w-8 h-8 mr-3" />
              Le Paradoxe de l'Hyperconnexion
            </h3>
            <p className="text-xl text-gray-700 leading-relaxed">
              Plus nous sommes connectés, plus nous nous sentons isolés et anxieux. En Tunisie, la santé mentale numérique est une crise invisible qui touche des millions de personnes.
            </p>
          </div>

          <div className="grid grid-cols-3 gap-6">
            <div className="bg-white p-8 rounded-2xl shadow-xl border-l-4 border-pink-500 hover:scale-105 transition">
              <div className="text-7xl font-black text-pink-600 mb-4">75%</div>
              <p className="text-gray-800 font-bold text-xl mb-2">de pénétration internet</p>
              <p className="text-gray-600">9 millions de Tunisiens connectés quotidiennement</p>
            </div>
            <div className="bg-white p-8 rounded-2xl shadow-xl border-l-4 border-blue-500 hover:scale-105 transition">
              <div className="text-7xl font-black text-blue-600 mb-4">51%</div>
              <p className="text-gray-800 font-bold text-xl mb-2">plus de 3h/jour en ligne</p>
              <p className="text-gray-600">Usage intensif des réseaux sociaux</p>
            </div>
            <div className="bg-white p-8 rounded-2xl shadow-xl border-l-4 border-purple-500 hover:scale-105 transition">
              <div className="text-7xl font-black text-purple-600 mb-4">130</div>
              <p className="text-gray-800 font-bold text-xl mb-2">psychiatres / 12M</p>
              <p className="text-gray-600">Accès très limité à la santé mentale</p>
            </div>
          </div>

          <div className="grid grid-cols-2 gap-6">
            <div className="bg-gradient-to-br from-gray-800 to-gray-900 p-6 rounded-2xl text-white shadow-xl">
              <h4 className="font-bold text-2xl mb-4 flex items-center">
                <Zap className="w-6 h-6 mr-2 text-yellow-400" />
                Conséquences Alarmantes
              </h4>
              <ul className="space-y-3 text-lg">
                <li>• Anxiété et dépression en hausse chez les 15-35 ans</li>
                <li>• Comparaison sociale toxique (FOMO)</li>
                <li>• Troubles du sommeil et de concentration</li>
                <li>• Isolement malgré l'hyperconnexion</li>
              </ul>
            </div>
            <div className="bg-gradient-to-br from-red-500 to-red-600 p-6 rounded-2xl text-white shadow-xl">
              <h4 className="font-bold text-2xl mb-4 flex items-center">
                <Shield className="w-6 h-6 mr-2" />
                Barrières d'Accès
              </h4>
              <ul className="space-y-3 text-lg">
                <li>• Stigmatisation de la santé mentale</li>
                <li>• Coût élevé des consultations (80-150 DT)</li>
                <li>• Manque de professionnels qualifiés</li>
                <li>• Pas de solutions digitales locales</li>
              </ul>
            </div>
          </div>
        </div>
      )
    },

    // Slide 3 - Notre Solution
    {
      title: "Notre Solution : MoodSync",
      content: (
        <div className="space-y-6">
          <div className="bg-gradient-to-r from-pink-600 via-purple-600 to-blue-600 p-8 rounded-3xl text-white shadow-2xl">
            <div className="flex items-center justify-between">
              <div className="flex-1">
                <h3 className="text-4xl font-black mb-4">L'IA au service de vos émotions</h3>
                <p className="text-2xl font-light">Première plateforme tunisienne d'intelligence émotionnelle</p>
              </div>
              <div className="flex space-x-4">
                <Brain className="w-24 h-24 opacity-80" />
                <Heart className="w-24 h-24 opacity-80" />
              </div>
            </div>
          </div>

          <div className="grid grid-cols-2 gap-6">
            <div className="bg-gradient-to-br from-pink-50 to-pink-100 p-6 rounded-2xl border-2 border-pink-300 shadow-lg">
              <div className="flex items-center mb-4">
                <div className="w-16 h-16 bg-pink-500 rounded-2xl flex items-center justify-center mr-4">
                  <span className="text-3xl">🎯</span>
                </div>
                <div>
                  <h4 className="font-bold text-2xl text-gray-800">QUOI ?</h4>
                  <p className="text-gray-600">Nature de la solution</p>
                </div>
              </div>
              <p className="text-gray-700 text-lg leading-relaxed">
                Application mobile intelligente qui analyse vos émotions en temps réel via IA (reconnaissance faciale + analyse textuelle) et propose des exercices personnalisés de bien-être.
              </p>
            </div>

            <div className="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-2xl border-2 border-blue-300 shadow-lg">
              <div className="flex items-center mb-4">
                <div className="w-16 h-16 bg-blue-500 rounded-2xl flex items-center justify-center mr-4">
                  <span className="text-3xl">👥</span>
                </div>
                <div>
                  <h4 className="font-bold text-2xl text-gray-800">À QUI ?</h4>
                  <p className="text-gray-600">Cible prioritaire</p>
                </div>
              </div>
              <p className="text-gray-700 text-lg leading-relaxed">
                Jeunes tunisiens de 15-35 ans, hyperconnectés, urbains, qui souffrent d'anxiété numérique et cherchent un équilibre émotionnel sans le coût élevé d'un psy.
              </p>
            </div>

            <div className="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-2xl border-2 border-purple-300 shadow-lg">
              <div className="flex items-center mb-4">
                <div className="w-16 h-16 bg-purple-500 rounded-2xl flex items-center justify-center mr-4">
                  <span className="text-3xl">💡</span>
                </div>
                <div>
                  <h4 className="font-bold text-2xl text-gray-800">POURQUOI ?</h4>
                  <p className="text-gray-600">Besoin et utilité</p>
                </div>
              </div>
              <p className="text-gray-700 text-lg leading-relaxed">
                Transformer l'usage numérique en opportunité de croissance personnelle. Rendre le soutien psychologique accessible 24/7 à un prix abordable, adapté à la culture tunisienne.
              </p>
            </div>

            <div className="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-2xl border-2 border-green-300 shadow-lg">
              <div className="flex items-center mb-4">
                <div className="w-16 h-16 bg-green-500 rounded-2xl flex items-center justify-center mr-4">
                  <span className="text-3xl">⚙️</span>
                </div>
                <div>
                  <h4 className="font-bold text-2xl text-gray-800">COMMENT ?</h4>
                  <p className="text-gray-600">Mode d'utilisation</p>
                </div>
              </div>
              <p className="text-gray-700 text-lg leading-relaxed">
                1) Ouvrez l'app → 2) L'IA analyse votre humeur (selfie ou texte) → 3) Recevez exercices personnalisés → 4) Suivez votre évolution dans le temps.
              </p>
            </div>
          </div>
        </div>
      )
    },

    // Slide 4 - Proposition de Valeur
    {
      title: "Proposition de Valeur : Pourquoi MoodSync ?",
      content: (
        <div className="space-y-6">
          <div className="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-500 p-6 rounded-r-2xl">
            <h3 className="text-2xl font-bold text-gray-800 mb-2">Notre Promesse Unique</h3>
            <p className="text-xl text-gray-700">"L'application qui comprend vraiment vos émotions et parle votre langue"</p>
          </div>

          <div className="grid grid-cols-3 gap-5">
            <div className="bg-white p-6 rounded-xl shadow-lg border-t-4 border-pink-500">
              <Award className="w-12 h-12 text-pink-500 mb-4" />
              <h4 className="font-bold text-xl text-gray-800 mb-3">Bénéfice Client #1</h4>
              <p className="text-gray-700 mb-3">Compréhension immédiate de votre état émotionnel</p>
              <div className="bg-pink-50 p-3 rounded-lg">
                <p className="text-sm text-gray-600">→ Analyse IA en temps réel (facial + texte)</p>
              </div>
            </div>

            <div className="bg-white p-6 rounded-xl shadow-lg border-t-4 border-blue-500">
              <DollarSign className="w-12 h-12 text-blue-500 mb-4" />
              <h4 className="font-bold text-xl text-gray-800 mb-3">Bénéfice Client #2</h4>
              <p className="text-gray-700 mb-3">Accessible 24/7 à prix abordable</p>
              <div className="bg-blue-50 p-3 rounded-lg">
                <p className="text-sm text-gray-600">→ 5-10 DT/mois vs 80-150 DT/séance psy</p>
              </div>
            </div>

            <div className="bg-white p-6 rounded-xl shadow-lg border-t-4 border-purple-500">
              <Globe className="w-12 h-12 text-purple-500 mb-4" />
              <h4 className="font-bold text-xl text-gray-800 mb-3">Bénéfice Client #3</h4>
              <p className="text-gray-700 mb-3">Adapté à la culture tunisienne</p>
              <div className="bg-purple-50 p-3 rounded-lg">
                <p className="text-sm text-gray-600">→ Arabe, français, dialecte + contexte local</p>
              </div>
            </div>
          </div>

          <div className="overflow-hidden rounded-2xl shadow-2xl">
            <table className="w-full">
              <thead className="bg-gradient-to-r from-pink-600 to-blue-600 text-white">
                <tr>
                  <th className="p-5 text-left text-lg">Critère</th>
                  <th className="p-5 text-left text-lg">Apps Internationales</th>
                  <th className="p-5 text-left text-lg">MoodSync ✨</th>
                </tr>
              </thead>
              <tbody className="bg-white">
                <tr className="border-b hover:bg-gray-50">
                  <td className="p-5 font-bold">Analyse</td>
                  <td className="p-5 text-gray-600">Temps d'écran passif</td>
                  <td className="p-5 text-green-700 font-bold flex items-center">
                    <CheckCircle className="w-5 h-5 mr-2" />
                    Émotions en temps réel (IA)
                  </td>
                </tr>
                <tr className="border-b hover:bg-gray-50">
                  <td className="p-5 font-bold">Langue</td>
                  <td className="p-5 text-gray-600">Anglais / Français</td>
                  <td className="p-5 text-green-700 font-bold flex items-center">
                    <CheckCircle className="w-5 h-5 mr-2" />
                    Arabe + Dialecte tunisien
                  </td>
                </tr>
                <tr className="border-b hover:bg-gray-50">
                  <td className="p-5 font-bold">Approche</td>
                  <td className="p-5 text-gray-600">Blocage / Punitive</td>
                  <td className="p-5 text-green-700 font-bold flex items-center">
                    <CheckCircle className="w-5 h-5 mr-2" />
                    Constructive / Amélioration
                  </td>
                </tr>
                <tr className="hover:bg-gray-50">
                  <td className="p-5 font-bold">Prix</td>
                  <td className="p-5 text-gray-600">10-15€/mois (35-50 DT)</td>
                  <td className="p-5 text-green-700 font-bold flex items-center">
                    <CheckCircle className="w-5 h-5 mr-2" />
                    5-10 DT/mois (-70%)
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      )
    },

    // Slide 5 - Analyse SWOT + TOWS
    {
      title: "Analyse Stratégique : SWOT & TOWS",
      content: (
        <div className="space-y-5">
          <div className="grid grid-cols-2 gap-5">
            <div className="bg-gradient-to-br from-green-50 to-green-100 p-5 rounded-xl border-2 border-green-400 shadow-lg">
              <h3 className="text-xl font-bold text-green-800 mb-3 flex items-center">
                <Award className="w-6 h-6 mr-2" />
                Forces (S)
              </h3>
              <ul className="space-y-2 text-sm">
                <li className="flex items-start">
                  <CheckCircle className="w-4 h-4 text-green-600 mr-2 mt-0.5 flex-shrink-0" />
                  <span>IA culturelle tunisienne unique</span>
                </li>
                <li className="flex items-start">
                  <CheckCircle className="w-4 h-4 text-green-600 mr-2 mt-0.5 flex-shrink-0" />
                  <span>Équipe multidisciplinaire solide</span>
                </li>
                <li className="flex items-start">
                  <CheckCircle className="w-4 h-4 text-green-600 mr-2 mt-0.5 flex-shrink-0" />
                  <span>First-mover advantage local</span>
                </li>
              </ul>
            </div>

            <div className="bg-gradient-to-br from-red-50 to-red-100 p-5 rounded-xl border-2 border-red-400 shadow-lg">
              <h3 className="text-xl font-bold text-red-800 mb-3 flex items-center">
                <AlertCircle className="w-6 h-6 mr-2" />
                Faiblesses (W)
              </h3>
              <ul className="space-y-2 text-sm">
                <li className="flex items-start">
                  <span className="text-red-600 mr-2">•</span>
                  <span>Budget marketing limité</span>
                </li>
                <li className="flex items-start">
                  <span className="text-red-600 mr-2">•</span>
                  <span>Marque jeune à construire</span>
                </li>
                <li className="flex items-start">
                  <span className="text-red-600 mr-2">•</span>
                  <span>Dépendance infrastructure IA</span>
                </li>
              </ul>
            </div>

            <div className="bg-gradient-to-br from-blue-50 to-blue-100 p-5 rounded-xl border-2 border-blue-400 shadow-lg">
              <h3 className="text-xl font-bold text-blue-800 mb-3 flex items-center">
                <TrendingUp className="w-6 h-6 mr-2" />
                Opportunités (O)
              </h3>
              <ul className="space-y-2 text-sm">
                <li className="flex items-start">
                  <span className="text-blue-600 mr-2">→</span>
                  <span>Marché bien-être en explosion (+35% CAGR)</span>
                </li>
                <li className="flex items-start">
                  <span className="text-blue-600 mr-2">→</span>
                  <span>Partenariats opérateurs (Orange, Ooredoo)</span>
                </li>
                <li className="flex items-start">
                  <span className="text-blue-600 mr-2">→</span>
                  <span>Demande B2B entreprises (QVT)</span>
                </li>
              </ul>
            </div>

            <div className="bg-gradient-to-br from-purple-50 to-purple-100 p-5 rounded-xl border-2 border-purple-400 shadow-lg">
              <h3 className="text-xl font-bold text-purple-800 mb-3 flex items-center">
                <Shield className="w-6 h-6 mr-2" />
                Menaces (T)
              </h3>
              <ul className="space-y-2 text-sm">
                <li className="flex items-start">
                  <span className="text-purple-600 mr-2">!</span>
                  <span>Concurrence internationale (Calm, Headspace)</span>
                </li>
                <li className="flex items-start">
                  <span className="text-purple-600 mr-2">!</span>
                  <span>Réglementation GDPR données sensibles</span>
                </li>
                <li className="flex items-start">
                  <span className="text-purple-600 mr-2">!</span>
                  <span>Scepticisme culturel envers IA santé</span>
                </li>
              </ul>
            </div>
          </div>

          <div className="bg-gradient-to-r from-indigo-50 to-purple-50 p-5 rounded-xl border-2 border-indigo-400">
            <h3 className="text-xl font-bold text-gray-800 mb-4 text-center">Stratégie TOWS : Plan d'Action</h3>
            <div className="grid grid-cols-2 gap-4 text-sm">
              <div className="bg-white p-4 rounded-lg shadow">
                <h4 className="font-bold text-green-700 mb-2">SO - Offensive</h4>
                <p className="text-gray-700">Capitaliser sur l'IA locale pour capturer le marché bien-être en croissance</p>
              </div>
              <div className="bg-white p-4 rounded-lg shadow">
                <h4 className="font-bold text-purple-700 mb-2">ST - Défensive</h4>
                <p className="text-gray-700">Différenciation culturelle forte face aux géants internationaux</p>
              </div>
              <div className="bg-white p-4 rounded-lg shadow">
                <h4 className="font-bold text-blue-700 mb-2">WO - Renforcement</h4>
                <p className="text-gray-700">Partenariats opérateurs pour compenser budget marketing limité</p>
              </div>
              <div className="bg-white p-4 rounded-lg shadow">
                <h4 className="font-bold text-red-700 mb-2">WT - Survie</h4>
                <p className="text-gray-700">Excellence UX/UI + Compliance GDPR dès la conception</p>
              </div>
            </div>
          </div>
        </div>
      )
    },

    // Slide 6 - Marché & Cible
    {
      title: "Marché & Segmentation Cible",
      content: (
        <div className="space-y-6">
          <div className="bg-gradient-to-r from-cyan-50 to-blue-50 border-l-4 border-cyan-500 p-5 rounded-r-2xl">
            <h3 className="text-xl font-bold text-gray-800 mb-2">Segmentation Multi-Critères</h3>
            <p className="text-gray-700">Analyse démographique, comportementale et psychographique du marché tunisien</p>
          </div>

          <div className="grid grid-cols-3 gap-5">
            <div className="bg-white p-5 rounded-xl shadow-lg border-2 border-pink-300">
              <div className="w-14 h-14 bg-pink-500 rounded-full flex items-center justify-center mx-auto mb-3">
                <Users className="w-8 h-8 text-white" />
              </div>
              <h4 className="font-bold text-center text-gray-800 mb-4">Démographique</h4>
              <div className="space-y-2 text-sm">
                <div className="bg-pink-50 p-3 rounded-lg">
                  <p className="font-bold text-pink-700">15-24 ans (Gen Z)</p>
                  <p className="text-gray-600">Natifs digitaux</p>
                </div>
                <div className="bg-pink-50 p-3 rounded-lg">
                  <p className="font-bold text-pink-700">25-35 ans (Millennials)</p>
                  <p className="text-gray-600">Actifs professionnels</p>
                </div>
                <div className="bg-pink-50 p-3 rounded-lg">
                  <p className="font-bold text-pink-700">35+ ans</p>
                  <p className="text-gray-600">Adopters progressifs</p>
                </div>
              </div>
            </div>

            <div className="bg-white p-5 rounded-xl shadow-lg border-2 border-blue-300">
              <div className="w-14 h-14 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-3">
                <Smartphone className="w-8 h-8 text-white" />
              </div>
              <h4 className="font-bold text-center text-gray-800 mb-4">Comportemental</h4>
              <div className="space-y-2 text-sm">
                <div className="bg-blue-50 p-3 rounded-lg">
                  <p className="font-bold text-blue-700">Hyperconnectés</p>
                  <p className="text-gray-600">+5h/jour en ligne</p>
                </div>
                <div className="bg-blue-50 p-3 rounded-lg">
                  <p className="font-bold text-blue-700">Modérés</p>
                  <p className="text-gray-600">2-4h/jour</p>
                </div>
                <div className="bg-blue-50 p-3 rounded-lg">
                  <p className="font-bold text-blue-700">Débutants</p>
                  <p className="text-gray-600">Adoption récente</p>
                </div>
              </div>
            </div>

            <div className="bg-white p-5 rounded-xl shadow-lg border-2 border-purple-300">
              <div className="w-14 h-14 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-3">
                <Brain className="w-8 h-8 text-white" />
              </div>
              <h4 className="font-bold text-center text-gray-800 mb-4">Psychographique</h4>
              <div className="space-y-2 text-sm">
                <div className="bg-purple-50 p-3 rounded-lg">
                  <p className="font-bold text-purple-700">Profil Stress</p>
                  <p className="text-gray-600">Charge mentale élevée</p>
                </div>
                <div className="bg-purple-50 p-3 rounded-lg">
                  <p className="font-bold text-purple-700">Profil Anxiété</p>
                  <p className="text-gray-600">Rumination chronique</p>
                </div>
                <div className="bg-purple-50 p-3 rounded-lg">
                  <p className="font-bold text-purple-700">Profil Burn-out</p>
                  <p className="text-gray-600">Épuisement professionnel</p>
                </div>
              </div>
            </div>
          </div>

          <div className="bg-gradient-to-r from-pink-600 via-purple-600 to-blue-600 p-8 rounded-2xl text-white shadow-2xl">
            <div className="flex items-center justify-between">
              <div className="flex-1">
                <Target className="w-10 h-10 mb-3" />
                <h3 className="text-3xl font-bold mb-4">Segment Cible Prioritaire</h3>
                <div className="grid grid-cols-2 gap-4 text-lg">
                  <div><span className="font-light">Âge:</span> <span className="font-bold">18-35 ans</span></div>
                  <div><span className="font-light">Géo:</span> <span className="font-bold">Tunis, Sfax, Sousse</span></div>
                  <div><span className="font-light">Profil:</span> <span className="font-bold">Actifs réseaux sociaux</span></div>
                  <div><span className="font-light">Tech:</span> <span className="font-bold">Early adopters</span></div>
                </div>
              </div>
              <div className="text-center border-l-2 border-white/30 pl-8">
                <p className="text-lg mb-2">Taille Marché</p>
                <p className="text-6xl font-black">3M</p>
                <p className="text-sm mt-2 bg-white/20 px-4 py-2 rounded-full">TAM: 420M DT</p>
              </div>
            </div>
          </div>
        </div>
      )
    },

    // Slide 7 - Marketing & Prix
    {
      title: "Stratégie Marketing & Tarification",
      content: (
        <div className="space-y-6">
          <div className="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-500 p-5 rounded-r-2xl">
            <h3 className="text-2xl font-bold text-gray-800 mb-2">Positionnement</h3>
            <p className="text-xl text-gray-700 italic">"L'application qui comprend vraiment vos émotions"</p>
          </div>

          <div className="grid grid-cols-3 gap-4">
            <div className="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-300 hover:shadow-xl transition">
              <div className="text-center mb-4">
                <span className="bg-gray-100 px-4 py-2 rounded-full text-sm font-bold">FREEMIUM</span>
              </div>
              <h3 className="text-3xl font-bold text-center mb-2">Gratuit</h3>
              <div className="text-center mb-6">
                <span className="text-5xl font-black">0 DT</span>
              </div>
              <ul className="space-y-2 text-sm mb-6">
                <li className="flex items-start">
                  <CheckCircle className="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span>Journal d'humeur</span>
                </li>
                <li className="flex items-start">
                  <CheckCircle className="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span>Stats basiques (7 jours)</span>
                </li>
                <li className="flex items-start">
                  <CheckCircle className="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span>3 exercices/jour</span>
                </li>
              </ul>
              <button className="w-full bg-gray-200 text-gray-700 py-3 rounded-xl font-bold">Gratuit</button>
            </div>

            <div className="bg-gradient-to-br from-pink-500 to-purple-600 rounded-2xl shadow-2xl p-6 border-2 border-pink-400 transform scale-105">
              <div className="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-yellow-400 px-4 py-1 rounded-full">
                <p className="text-xs font-black">POPULAIRE</p>
              </div>
              <div className="text-center mb-4 mt-2">
                <span className="bg-white/20 px-4 py-2 rounded-full text-sm font-bold text-white">PREMIUM</span>
              </div>
              <h3 className="text-3xl font-bold text-center mb-2 text-white">Premium</h3>
              <div className="text-center mb-6 text-white">
                <span className="text-5xl font-black">5 DT</span>
                <span className="text-xl">/mois</span>
              </div>
              <ul className="space-y-2 text-sm mb-6 text-white">
                <li className="flex items-start">
                  <CheckCircle className="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" />
                  <span>Analyse IA émotionnelle</span>
                </li>
                <li className="flex items-start">
                  <CheckCircle className="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" />
                  <span>Exercices illimités</span>
                </li>
                <li className="flex items-start">
                  <CheckCircle className="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" />
                  <span>Suivi complet</span>
                </li>
                <li className="flex items-start">
                  <CheckCircle className="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" />
                  <span>Insights prédictifs</span>
                </li>
              </ul>
              <button className="w-full bg-white text-pink-600 py-3 rounded-xl font-bold">Essai 14j</button>
            </div>

            <div className="bg-white rounded-2xl shadow-lg p-6 border-2 border-purple-300 hover:shadow-xl transition">
              <div className="text-center mb-4">
                <span className="bg-purple-100 px-4 py-2 rounded-full text-sm font-bold text-purple-700">PREMIUM+</span>
              </div>
              <h3 className="text-3xl font-bold text-center mb-2">Premium+</h3>
              <div className="text-center mb-6">
                <span className="text-5xl font-black text-purple-600">10 DT</span>
                <span className="text-xl">/mois</span>
              </div>
              <ul className="space-y-2 text-sm mb-6">
                <li className="flex items-start">
                  <CheckCircle className="w-4 h-4 text-purple-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span>Tout Premium +</span>
                </li>
                <li className="flex items-start">
                  <CheckCircle className="w-4 h-4 text-purple-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span>2 consult. psy/mois</span>
                </li>
                <li className="flex items-start">
                  <CheckCircle className="w-4 h-4 text-purple-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span>Support 24/7</span>
                </li>
                <li className="flex items-start">
                  <CheckCircle className="w-4 h-4 text-purple-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span>Programmes spécialisés</span>
                </li>
              </ul>
              <button className="w-full bg-gradient-to-r from-purple-500 to-purple-600 text-white py-3 rounded-xl font-bold">Démarrer</button>
            </div>
          </div>

          <div className="bg-gradient-to-br from-blue-50 to-blue-100 p-5 rounded-xl border border-blue-300">
            <h4 className="font-bold text-lg mb-3">📢 Communication - Spot Publicitaire 10 secondes</h4>
            <div className="grid grid-cols-4 gap-3">
              <div className="bg-white p-4 rounded-lg text-center">
                <div className="text-3xl mb-2">😰</div>
                <p className="text-sm font-bold">Scroll infini</p>
                <p className="text-xs text-gray-600">Stress visible</p>
              </div>
              <div className="bg-white p-4 rounded-lg text-center">
                <div className="text-3xl mb-2">🧠</div>
                <p className="text-sm font-bold">MoodSync analyse</p>
                <p className="text-xs text-gray-600">IA en action</p>
              </div>
              <div className="bg-white p-4 rounded-lg text-center">
                <div className="text-3xl mb-2">🧘</div>
                <p className="text-sm font-bold">Exercice respiration</p>
                <p className="text-xs text-gray-600">Apaisement</p>
              </div>
              <div className="bg-white p-4 rounded-lg text-center">
                <div className="text-3xl mb-2">😊</div>
                <p className="text-sm font-bold">"Ton équilibre"</p>
                <p className="text-xs text-gray-600">Slogan final</p>
              </div>
            </div>
            <p className="text-center mt-4 text-gray-700 font-medium">Canaux: TikTok, Instagram, Partenariats opérateurs, Influenceurs bien-être</p>
          </div>
        </div>
      )
    },

    // Slide 8 - Concurrence
    {
      title: "Analyse Concurrentielle & Différenciation",
      content: (
        <div className="space-y-6">
          <div className="bg-gradient-to-r from-purple-50 to-pink-50 border-l-4 border-purple-500 p-5 rounded-r-2xl">
            <h3 className="text-xl font-bold text-gray-800 mb-2">Paysage Concurrentiel</h3>
            <p className="text-gray-700">MoodSync se positionne comme l'alternative tunisienne intelligente et accessible</p>
          </div>

          <div className="space-y-4">
            <div className="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
              <div className="flex items-center justify-between mb-3">
                <h4 className="font-bold text-xl text-gray-800">Calm / Headspace</h4>
                <span className="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-bold">Apps Médit. Int.</span>
              </div>
              <div className="grid grid-cols-3 gap-4">
                <div>
                  <p className="text-green-600 font-semibold mb-2">✓ Forces</p>
                  <p className="text-sm text-gray-700">Contenu riche, brand reconnu</p>
                </div>
                <div>
                  <p className="text-red-600 font-semibold mb-2">✗ Faiblesses</p>
                  <p className="text-sm text-gray-700">Prix élevé, pas adapté Tunisie</p>
                </div>
                <div>
                  <p className="text-blue-600 font-semibold mb-2">→ Notre avantage</p>
                  <p className="text-sm text-gray-700">Prix local + culture tunisienne</p>
                </div>
              </div>
            </div>

            <div className="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500">
              <div className="flex items-center justify-between mb-3">
                <h4 className="font-bold text-xl text-gray-800">Apps de Blocage (Freedom, etc.)</h4>
                <span className="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm font-bold">Digital Detox</span>
              </div>
              <div className="grid grid-cols-3 gap-4">
                <div>
                  <p className="text-green-600 font-semibold mb-2">✓ Forces</p>
                  <p className="text-sm text-gray-700">Simples, efficaces à court terme</p>
                </div>
                <div>
                  <p className="text-red-600 font-semibold mb-2">✗ Faiblesses</p>
                  <p className="text-sm text-gray-700">Approche punitive, pas durable</p>
                </div>
                <div>
                  <p className="text-blue-600 font-semibold mb-2">→ Notre avantage</p>
                  <p className="text-sm text-gray-700">Constructive, éducative, IA</p>
                </div>
              </div>
            </div>

            <div className="bg-white p-6 rounded-xl shadow-lg border-l-4 border-pink-500">
              <div className="flex items-center justify-between mb-3">
                <h4 className="font-bold text-xl text-gray-800">Consultation Psychologues en ligne</h4>
                <span className="bg-pink-100 text-pink-700 px-3 py-1 rounded-full text-sm font-bold">Télé-psy</span>
              </div>
              <div className="grid grid-cols-3 gap-4">
                <div>
                  <p className="text-green-600 font-semibold mb-2">✓ Forces</p>
                  <p className="text-sm text-gray-700">Expertise humaine qualifiée</p>
                </div>
                <div>
                  <p className="text-red-600 font-semibold mb-2">✗ Faiblesses</p>
                  <p className="text-sm text-gray-700">Très coûteux (80-150 DT/séance)</p>
                </div>
                <div>
                  <p className="text-blue-600 font-semibold mb-2">→ Notre avantage</p>
                  <p className="text-sm text-gray-700">24/7, accessible, + option psy</p>
                </div>
              </div>
            </div>
          </div>

          <div className="bg-gradient-to-r from-yellow-100 to-orange-100 p-6 rounded-2xl border-2 border-yellow-400">
            <h3 className="text-2xl font-bold text-gray-800 text-center mb-3">Notre Différenciation Unique</h3>
            <p className="text-xl text-center text-gray-700">
              <span className="font-bold text-pink-600">Technologie IA</span> + 
              <span className="font-bold text-blue-600"> Culture tunisienne</span> + 
              <span className="font-bold text-purple-600"> Prix accessible</span> = 
              <span className="font-bold text-green-600"> Innovation sociale</span>
            </p>
          </div>
        </div>
      )
    },

    // Slide 9 - Business Model
    {
      title: "Business Model & Rentabilité",
      content: (
        <div className="space-y-6">
          <div className="grid grid-cols-3 gap-5">
            <div className="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border-2 border-green-400 shadow-lg">
              <DollarSign className="w-12 h-12 text-green-600 mb-3" />
              <h3 className="font-bold text-xl text-gray-800 mb-4">Sources de Revenus</h3>
              <div className="space-y-3">
                <div className="bg-white p-3 rounded-lg">
                  <p className="font-bold text-green-700">80% Abonnements</p>
                  <p className="text-sm text-gray-600">Premium: 5 DT | Premium+: 10 DT</p>
                </div>
                <div className="bg-white p-3 rounded-lg">
                  <p className="font-bold text-green-700">15% B2B Entreprises</p>
                  <p className="text-sm text-gray-600">Forfaits QVT: 150-300 DT/mois</p>
                </div>
                <div className="bg-white p-3 rounded-lg">
                  <p className="font-bold text-green-700">5% Psychologues</p>
                  <p className="text-sm text-gray-600">Commission 20% / consultation</p>
                </div>
              </div>
            </div>

            <div className="bg-gradient-to-br from-red-50 to-red-100 p-6 rounded-xl border-2 border-red-400 shadow-lg">
              <BarChart3 className="w-12 h-12 text-red-600 mb-3" />
              <h3 className="font-bold text-xl text-gray-800 mb-4">Charges Mensuelles</h3>
              <div className="space-y-3">
                <div className="bg-white p-3 rounded-lg">
                  <p className="font-bold text-red-700">Fixes: 19 450 DT</p>
                  <p className="text-sm text-gray-600">Salaires, infrastructure, loyer</p>
                </div>
                <div className="bg-white p-3 rounded-lg">
                  <p className="font-bold text-red-700">Variables: 4 500 DT</p>
                  <p className="text-sm text-gray-600">Marketing, serveurs, support</p>
                </div>
                <div className="bg-white p-3 rounded-lg">
                  <p className="font-bold text-gray-700">Total: 23 950 DT/mois</p>
                  <p className="text-sm text-gray-600">Coûts opérationnels totaux</p>
                </div>
              </div>
            </div>

            <div className="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border-2 border-blue-400 shadow-lg">
              <TrendingUp className="w-12 h-12 text-blue-600 mb-3" />
              <h3 className="font-bold text-xl text-gray-800 mb-4">Rentabilité</h3>
              <div className="space-y-3">
                <div className="bg-white p-3 rounded-lg">
                  <p className="font-bold text-blue-700">Seuil de rentabilité</p>
                  <p className="text-3xl font-black text-blue-600">3 890</p>
                  <p className="text-sm text-gray-600">utilisateurs payants minimum</p>
                </div>
                <div className="bg-white p-3 rounded-lg">
                  <p className="font-bold text-blue-700">Point mort</p>
                  <p className="text-xl font-bold text-blue-600">Mois 8</p>
                  <p className="text-sm text-gray-600">Projection conservatrice</p>
                </div>
              </div>
            </div>
          </div>

          <div className="bg-gradient-to-r from-green-600 to-blue-600 p-8 rounded-2xl text-white shadow-2xl">
            <h3 className="text-3xl font-bold mb-6 text-center">Objectifs 12 Mois</h3>
            <div className="grid grid-cols-3 gap-8">
              <div className="text-center">
                <p className="text-6xl font-black mb-2">5 000</p>
                <p className="text-xl">Abonnés payants</p>
              </div>
              <div className="text-center">
                <p className="text-6xl font-black mb-2">35 000</p>
                <p className="text-xl">DT/mois revenus</p>
              </div>
              <div className="text-center">
                <p className="text-6xl font-black mb-2">15 000</p>
                <p className="text-xl">Utilisateurs gratuits</p>
              </div>
            </div>
          </div>

          <div className="grid grid-cols-2 gap-5">
            <div className="bg-white p-5 rounded-xl shadow-lg border-2 border-purple-300">
              <h4 className="font-bold text-lg text-gray-800 mb-3">💰 CA prévisionnel Année 1</h4>
              <p className="text-4xl font-black text-purple-600 mb-2">420 000 DT</p>
              <p className="text-gray-600">Basé sur croissance progressive de 400 abonnés/mois</p>
            </div>
            <div className="bg-white p-5 rounded-xl shadow-lg border-2 border-green-300">
              <h4 className="font-bold text-lg text-gray-800 mb-3">📈 Marge nette Année 1</h4>
              <p className="text-4xl font-black text-green-600 mb-2">18%</p>
              <p className="text-gray-600">Après réinvestissement croissance et R&D</p>
            </div>
          </div>
        </div>
      )
    },

    // Slide 10 - Conclusion & ODD
    {
      title: "Impact & Call to Action",
      content: (
        <div className="space-y-8">
          <div className="bg-gradient-to-r from-pink-50 to-blue-50 border-l-4 border-pink-500 p-6 rounded-r-2xl">
            <h3 className="text-3xl font-bold text-gray-800 mb-3">Pourquoi MoodSync Maintenant ?</h3>
            <p className="text-xl text-gray-700 leading-relaxed">
              Parce qu'une génération entière souffre en silence derrière l'écran. MoodSync transforme cette crise en opportunité de croissance personnelle et collective.
            </p>
          </div>

          <div className="grid grid-cols-4 gap-5">
            <div className="bg-white p-6 rounded-xl shadow-lg border-t-4 border-red-500">
              <Globe className="w-12 h-12 text-red-500 mb-4" />
              <h4 className="font-bold text-xl text-gray-800 mb-2">ODD 3</h4>
              <p className="text-gray-700 font-semibold">Bonne santé et bien-être</p>
              <p className="text-sm text-gray-600 mt-2">Santé mentale accessible à tous</p>
            </div>
            <div className="bg-white p-6 rounded-xl shadow-lg border-t-4 border-blue-500">
              <Globe className="w-12 h-12 text-blue-500 mb-4" />
              <h4 className="font-bold text-xl text-gray-800 mb-2">ODD 4</h4>
              <p className="text-gray-700 font-semibold">Éducation de qualité</p>
              <p className="text-sm text-gray-600 mt-2">Éducation numérique responsable</p>
            </div>
            <div className="bg-white p-6 rounded-xl shadow-lg border-t-4 border-green-500">
              <Globe className="w-12 h-12 text-green-500 mb-4" />
              <h4 className="font-bold text-xl text-gray-800 mb-2">ODD 8</h4>
              <p className="text-gray-700 font-semibold">Travail décent</p>
              <p className="text-sm text-gray-600 mt-2">Bien-être au travail (QVT)</p>
            </div>
            <div className="bg-white p-6 rounded-xl shadow-lg border-t-4 border-purple-500">
              <Globe className="w-12 h-12 text-purple-500 mb-4" />
              <h4 className="font-bold text-xl text-gray-800 mb-2">ODD 10</h4>
              <p className="text-gray-700 font-semibold">Réduction des inégalités</p>
              <p className="text-sm text-gray-600 mt-2">Accès égal santé mentale</p>
            </div>
          </div>

          <div className="bg-gradient-to-r from-pink-600 via-purple-600 to-blue-600 p-10 rounded-3xl text-white text-center shadow-2xl">
            <h3 className="text-5xl font-black mb-6">Rejoignez la Révolution du Bien-Être Numérique</h3>
            <p className="text-2xl mb-8 font-light">MoodSync n'est pas qu'une app - c'est un mouvement national pour la santé mentale</p>
            <div className="flex items-center justify-center space-x-8 mb-8">
              <Heart className="w-16 h-16 animate-pulse" />
              <Brain className="w-16 h-16" />
              <Users className="w-16 h-16" />
            </div>
            <div className="grid grid-cols-2 gap-6 max-w-2xl mx-auto">
              <div className="bg-white/20 backdrop-blur-sm p-5 rounded-xl">
                <p className="text-lg font-semibold mb-2">📧 Contact</p>
                <p className="text-xl">contact@moodsync.tn</p>
              </div>
              <div className="bg-white/20 backdrop-blur-sm p-5 rounded-xl">
                <p className="text-lg font-semibold mb-2">🌐 Site Web</p>
                <p className="text-xl">www.moodsync.tn</p>
              </div>
            </div>
          </div>

          <div className="text-center">
            <p className="text-3xl font-bold text-gray-800 mb-3">Ensemble, construisons l'équilibre numérique de demain 🇹🇳</p>
            <p className="text-xl text-gray-600">Merci de votre attention</p>
          </div>
        </div>
      )
    }
  ];

  const nextSlide = () => {
    setCurrentSlide((prev) => (prev + 1) % slides.length);
  };

  const prevSlide = () => {
    setCurrentSlide((prev) => (prev - 1 + slides.length) % slides.length);
  };

  React.useEffect(() => {
    const handleKeyDown = (e) => {
      if (e.key === 'ArrowRight') nextSlide();
      if (e.key === 'ArrowLeft') prevSlide();
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, []);

  return (
    <div className="min-h-screen bg-gradient-to-br from-pink-50 via-blue-50 to-purple-50 p-6">
      <div className="max-w-7xl mx-auto">
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center space-x-3">
            <Heart className="w-10 h-10 text-pink-500" />
            <Brain className="w-8 h-8 text-blue-500" />
            <span className="text-2xl font-bold text-gray-800">MoodSync</span>
          </div>
          <div className="text-gray-600 font-medium">
            Slide {currentSlide + 1} / {slides.length}
          </div>
        </div>

        <div className="bg-white rounded-3xl shadow-2xl p-10 min-h-[650px] relative">
          {slides[currentSlide].title && (
            <h2 className="text-4xl font-bold text-gray-800 mb-8 pb-4 border-b-4 border-gradient-to-r from-pink-500 to-blue-500">
              {slides[currentSlide].title}
            </h2>
          )}
          <div className="text-gray-700">
            {slides[currentSlide].content}
          </div>
        </div>

        <div className="flex items-center justify-between mt-6">
          <button
            onClick={prevSlide}
            disabled={currentSlide === 0}
            className="flex items-center space-x-2 px-6 py-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition disabled:opacity-50 disabled:cursor-not-allowed font-medium"
          >
            <ChevronLeft className="w-5 h-5" />
            <span>Précédent</span>
          </button>
          
          <div className="flex space-x-2">
            {slides.map((_, index) => (
              <button
                key={index}
                onClick={() => setCurrentSlide(index)}
                className={`h-3 rounded-full transition-all ${
                  index === currentSlide
                    ? 'bg-gradient-to-r from-pink-                    index === currentSlide
                    ? 'bg-gradient-to-r from-pink-500 to-blue-500 w-12'
                    : 'bg-gray-300 w-3 hover:bg-gray-400'
                }`}
              >
                <span className="sr-only">Slide {index + 1}</span>
              </button>
            ))}
          </div>
          
          <button
            onClick={nextSlide}
            disabled={currentSlide === slides.length - 1}
            className="flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-pink-500 to-blue-500 text-white rounded-xl shadow-lg hover:shadow-xl transition disabled:opacity-50 disabled:cursor-not-allowed font-medium"
          >
            <span>Suivant</span>
            <ChevronRight className="w-5 h-5" />
          </button>
        </div>

        <div className="mt-8 text-center text-gray-500 text-sm">
          <p>Utilisez les flèches du clavier ← → ou les boutons pour naviguer</p>
          <p className="mt-2">Présentation Innovation & Entrepreneuriat - MoodSync</p>
        </div>
      </div>
    </div>
  );
};

export default MoodSyncPresentation;