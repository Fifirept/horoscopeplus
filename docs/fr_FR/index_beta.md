# HoroscopePlus (beta)

Plugin Jeedom entièrement fait par IA permettant de récupérer et d'afficher l'horoscope quotidien occidental et chinois, avec widget personnalisé et intégration native dans les designs.

<p align="center"><img width="952" height="375" alt="image" src="https://github.com/user-attachments/assets/e6ffec6c-066a-4521-af52-a8bbb924e78c" /></p>

---

## Fonctionnalités

- Récupération automatique de l'horoscope quotidien depuis "mon-horoscope-du-jour.com"
- Support de l'horoscope occidental (12 signes) et chinois (12 signes)
- Affichage complet ou sélectif sous la forme de tableau : logo, date, thèmes (Humeur, Amour, Argent, Travail, Loisirs, Décans...)
- Widget logo `HoroscopePlus_logo` : pour personnaliser un virtuel qui n'affiche que le logo du signe, et ouvre l'horoscope complet du signe en modale au clic
- Rafraîchissement automatique quotidien (à 07h30 par défaut) ou personnalisé via un assistant cron sur la page de paramétrage du plugin
- Rafraîchissement manuel depuis la page principale de l'équipement ou depuis la tuile (dashboard et design)
- Personnalisation de l'apparence : couleurs du texte et des bordures, tailles de la police et du logo, alignements du texte

---

## Prérequis

- Debian 12 (non testé sur d'autres versions)
- Jeedom 4.5 (non testé sur d'autres versions)
- Accès Internet depuis le serveur Jeedom (pour la mise à jour de l'horoscope)

---

## Installation

1. Installer le plugin depuis le Market Jeedom (Plugins > Gestion des plugins)
2. Activer le plugin
3. Aller dans "Plugins > Organisation > HoroscopePlus"
4. Cliquer sur "Ajouter" pour créer un équipement
5. Choisir le "type" (Occidental ou Chinois) et le "signe"
6. Sauvegarder — les commandes correspondant aux différents thèmes seront automatiquement créées et les informations récupérées
7. Optionnel : Il est possible de masquer les thèmes considérés comme inutiles. Le tableau sera automatiquement ajusté
8. Optionnel : Il est possible de personnaliser les couleurs et tailles de textes

<p align="center"><img width="402" height="397" alt="image" src="https://github.com/user-attachments/assets/49d0bd62-3d2a-457f-990f-315d749e6cbe" /></p>

---

## Configuration du cron

Par défaut, l'horoscope est rafraîchi chaque jour à 07h30. Il est possible de définir manuellement l'heure de synchronisation dans les paramètres du plugin (assistant de configuration en cliquant sur "?").

<p align="center"><img width="690" height="67" alt="image" src="https://github.com/user-attachments/assets/fb276362-a7f0-4375-b499-ddc22022bde7" /></p>

---

## Signes supportés

**Horoscope Occidental :** Bélier, Taureau, Gémeaux, Cancer, Lion, Vierge, Balance, Scorpion, Sagittaire, Capricorne, Verseau, Poissons

**Horoscope Chinois :** Rat, Bœuf, Tigre, Lièvre, Dragon, Serpent, Cheval, Chèvre, Singe, Coq, Chien, Cochon

> ℹ️ Le site "mon-horoscope-du-jour.com" utilise "Lièvre" plutôt que "Lapin"

---

## Widget HoroscopePlus_logo

Ce widget est installé automatiquement avec le plugin dans `/data/customTemplates/dashboard/`.

Il permet de personnaliser un virtuel ou une commande affichée sur un design qui ne représente que le logo, et d'afficher l'horoscope complet sous forme de modale sur simple clic.

### Utilisation sur une commande de design

1. Ajouter la commande `Logo` de l'équipement HoroscopePlus sur un design

   <p align="center"><img width="161" height="271" alt="image" src="https://github.com/user-attachments/assets/388c6fdb-8d21-4740-890e-88576019be2f" /></p>
   <p align="center"><img width="404" height="126" alt="image" src="https://github.com/user-attachments/assets/f12d2870-fba3-478f-a670-6f78bab7aab6" /></p>

2. Dans la configuration de la commande > Affichage > Widget, sélectionner `Customtemp/HoroscopePlus_logo`

   <p align="center"><img width="518" height="167" alt="image" src="https://github.com/user-attachments/assets/a33ba47d-0433-4393-b3c9-249039ae3105" /></p>

3. Le logo s'affiche et ouvre automatiquement le bon horoscope au clic

   <p align="center"><img width="581" height="386" alt="image" src="https://github.com/user-attachments/assets/23f65796-c989-4f7e-8add-c70a9e13e16a" /></p>

### Utilisation dans un virtuel

1. Créer un nouveau virtuel

2. Importer les commandes `Logo` + `date` (optionnel) de chaque signe à visualiser

   <p align="center"><img width="956" height="277" alt="image" src="https://github.com/user-attachments/assets/28910c21-4def-4b8c-896f-5d6c32a2baad" /></p>

3. Dans la configuration de la commande `Logo` > Affichage > Widget, sélectionner `Customtemp/HoroscopePlus_logo`

   <p align="center"><img width="518" height="167" alt="image" src="https://github.com/user-attachments/assets/843f9e3f-f713-4950-852d-1ff88bd471ae" /></p>

4. Optionnel : Afficher le virtuel en mode "tableau"

   <p align="center"><img width="465" height="379" alt="image" src="https://github.com/user-attachments/assets/dc308186-d69a-4b90-a370-e3902a1edba7" /></p>

---

## Licence

Ce plugin est distribué gratuitement sous licence **AGPL v3**.

Source des données horoscope : [mon-horoscope-du-jour.com](https://www.mon-horoscope-du-jour.com)
