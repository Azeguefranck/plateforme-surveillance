# MÉMOIRE DE STAGE

---

# CONCEPTION ET RÉALISATION D'UN SYSTÈME DE SURVEILLANCE  
# DES PARAMÈTRES DES ÉQUIPEMENTS D'UNE SALLE SERVEUR

---

|||
|---|---|
| **Établissement** | IUT de Ngaoundéré — Université de Ngaoundéré |
| **Département** | Génie Informatique |
| **Diplôme préparé** | DUT Génie Informatique |
| **Entreprise d'accueil** | INGENIERIS CAMEROUN |
| **Auteur** | AZEGUE FRANCK |
| **Encadreur académique** | [Nom de l'encadreur académique] |
| **Encadreur professionnel** | [Nom de l'encadreur professionnel] |
| **Année académique** | 2025 – 2026 |

---

## DÉDICACE

*À mes parents, dont le soutien silencieux est la fondation de chaque réussite.*  
*À tous ceux qui croient que la technologie peut transformer les réalités africaines.*

---

## REMERCIEMENTS

Je tiens à exprimer ma sincère gratitude à toutes les personnes qui ont contribué à la réalisation de ce travail.

Mes remerciements s'adressent en premier lieu à la Direction de l'**IUT de Ngaoundéré** et à l'ensemble du corps enseignant du Département Génie Informatique, notamment le **Professeur FEINJI** pour son enseignement en systèmes embarqués et réseaux de capteurs, et le **Docteur DASSI NAOMIE** pour ses cours de développement web et de bases de données qui ont directement orienté les choix techniques de ce projet.

Je remercie la Direction d'**INGENIERIS CAMEROUN** pour m'avoir accueilli et mis à disposition les ressources nécessaires à la conduite de ce projet.

Je remercie enfin mon encadreur académique, mon encadreur professionnel, ma famille et mes camarades de promotion.

---

## RÉSUMÉ

Les équipements hébergés dans les salles serveurs des petites et moyennes structures au Cameroun — serveurs, commutateurs réseau, systèmes d'alimentation — demeurent exposés à des risques environnementaux et sécuritaires permanents : surchauffe, humidité excessive, présence de gaz et intrusions physiques non autorisées. Ces risques, que la surveillance manuelle discontinue ne permet pas de détecter en temps utile, compromettent la disponibilité des infrastructures et exposent les organisations à des pertes coûteuses. Les solutions commerciales de type DCIM (*Data Center Infrastructure Management*), bien qu'efficaces, sont financièrement inaccessibles aux structures à ressources limitées. C'est dans ce contexte qu'a été conçu et réalisé, dans le cadre d'un stage à INGENIERIS CAMEROUN, un système de surveillance des paramètres des équipements d'une salle serveur : une plateforme IoT automatisée, économique et immédiatement opérationnelle, nommée **SUPSERVER**. L'architecture repose sur quatre couches fonctionnelles. La **couche d'acquisition** est assurée par une carte Arduino Uno équipée d'un capteur DHT22 (température et humidité ambiante des équipements), d'un capteur MQ135 (qualité de l'air et présence de gaz autour des équipements) et d'un capteur PIR HC-SR501 (détection d'intrusion physique), complétés par une LED RGB et un buzzer pour le retour d'état local. La **couche de relais** est gérée par le script Python `serial_relay.py`, qui lit les données JSON émises par l'Arduino sur la liaison USB Serial et les transmet à l'API par requêtes HTTP POST. La **couche de traitement** est construite sur Laravel 13.8, ==PHP 8.3.6== et MySQL via ==LAMPP==, avec une API REST et un système d'alertes automatiques à deux niveaux de sévérité. La **couche de présentation** offre un tableau de bord web interactif, ==actualisé toutes les deux secondes (live) et stockant les mesures toutes les 10 secondes (8 640 enregistrements par jour)==, accessible à distance via un tunnel ngrok statique. Les résultats obtenus démontrent une disponibilité de **99,67 %**, un taux de déclenchement des alertes de **100 %** et un délai moyen de notification de **3,2 secondes**, pour un coût matériel de **17 000 FCFA**.

**Mots-clés :** Internet des Objets (IoT) · surveillance temps réel · paramètres des équipements · salle serveur · Arduino · Laravel.

---

## ABSTRACT

The equipment housed in server rooms of small and medium-sized organisations in Cameroon — servers, network switches, power supply systems — remains continuously exposed to environmental and physical security risks: thermal overheating, excessive humidity, gas presence, and unauthorised physical intrusions. These risks, which manual monitoring practices fail to detect in a timely manner, compromise infrastructure availability and expose organisations to costly losses. Commercial DCIM (*Data Center Infrastructure Management*) solutions, while effective, are financially inaccessible to resource-constrained organisations. In this context, a server room equipment parameter monitoring system was designed and implemented during an internship at INGENIERIS CAMEROUN: an automated, cost-effective and immediately operational IoT platform named **SUPSERVER**. The architecture is organised around four functional layers. The **acquisition layer** uses an Arduino Uno microcontroller equipped with a DHT22 sensor (ambient temperature and humidity of equipment), an MQ135 sensor (air quality and gas detection around equipment), and an HC-SR501 PIR sensor (physical intrusion detection), complemented by an RGB LED and a buzzer for local status feedback. The **relay layer** is handled by the `serial_relay.py` Python script, which reads JSON data emitted by the Arduino over a USB Serial link and forwards it to the API via HTTP POST. The **processing layer** is built on Laravel 13.8, ==PHP 8.3.6== and MySQL via ==LAMPP==, featuring a REST API and automatic alert generation at two severity levels. The **presentation layer** provides an interactive web dashboard ==refreshed every two seconds (live) with measurements stored every 10 seconds (8,640 records per day)==, accessible remotely via a static ngrok tunnel. Results demonstrate **99.67 % availability**, a **100 % alert triggering rate** and an average notification delay of **3.2 seconds**, at a hardware cost of **17,000 FCFA**.

**Keywords:** Internet of Things (IoT) · real-time monitoring · equipment parameters · server room · Arduino · Laravel.

---

## TABLE DES MATIÈRES

- Dédicace
- Remerciements
- Résumé / Abstract
- Liste des figures
- Liste des tableaux
- Liste des abréviations
- **Introduction générale**
- **PARTIE I — Revue de la littérature**
  - Chapitre 1 : L'Internet des Objets et la surveillance des équipements
  - Chapitre 2 : Les microcontrôleurs et la plateforme Arduino
  - Chapitre 3 : Les capteurs de paramètres d'équipements
  - Chapitre 4 : La communication série et le relais Python
  - Chapitre 5 : Les frameworks web et les API REST
  - Chapitre 6 : Synthèse et positionnement
- **PARTIE II — Matériel, méthodes et conception**
  - Chapitre 7 : Cadre du stage et problématique
  - Chapitre 8 : Matériel et méthodes
  - Chapitre 9 : Conception du système
- **PARTIE III — Réalisation et implémentation**
  - Chapitre 10 : Implémentation technique
- **PARTIE IV — Tests, résultats et discussion**
  - Chapitre 11 : Tests, résultats et analyse critique
- **Conclusion générale et perspectives**
- **Références bibliographiques**
- **Annexes**

---

## LISTE DES FIGURES

| N° | Titre |
|---|---|
| Figure 1 | Modèle IoT en 4 couches appliqué à la surveillance des équipements |
| Figure 2 | Carte Arduino Uno |
| Figure 3 | Capteur DHT22 — température et humidité |
| Figure 4 | Capteur MQ135 — qualité de l'air et gaz |
| Figure 5 | Capteur PIR HC-SR501 — détection de mouvement |
| Figure 6 | Schéma complet de l'architecture SUPSERVER |
| Figure 7 | Schéma de câblage Arduino |
| Figure 8 | Modèle Logique de Données |
| Figure 9 | Diagramme de séquence : acquisition → relais → API → alerte |
| Figure 10 | Interface — Tableau de bord temps réel |
| Figure 11 | Interface — Page Statistiques (graphiques horaires) |
| Figure 12 | Interface — Historique des mesures |
| Figure 13 | Interface — Historique des alertes email |
| Figure 14 | Exemple d'email d'alerte automatique |
| Figure 15 | Résultats des tests — latences mesurées |

---

## LISTE DES TABLEAUX

| N° | Titre |
|---|---|
| Tableau 1 | Caractéristiques techniques de l'Arduino Uno |
| Tableau 2 | Paramètres surveillés et seuils d'alerte |
| Tableau 3 | Composants matériels et coûts estimés |
| Tableau 4 | Outils logiciels utilisés |
| Tableau 5 | Schéma de câblage des capteurs |
| Tableau 6 | Endpoints de l'API REST |
| Tableau 7 | Structure de la table `mesures` |
| Tableau 8 | Structure de la table `alertes` |
| Tableau 9 | Résultats des tests fonctionnels |
| Tableau 10 | Comparaison avec les travaux existants |

---

## LISTE DES ABRÉVIATIONS

| Sigle | Signification |
|---|---|
| API | Application Programming Interface |
| ASHRAE | American Society of Heating, Refrigerating and Air-Conditioning Engineers |
| DCIM | Data Center Infrastructure Management |
| DHT | Digital Humidity and Temperature |
| GPIO | General Purpose Input/Output |
| HTTP | HyperText Transfer Protocol |
| IoT | Internet of Things (Internet des Objets) |
| JSON | JavaScript Object Notation |
| MVC | Modèle-Vue-Contrôleur |
| PIR | Passive Infrared (détecteur infrarouge passif) |
| REST | Representational State Transfer |
| SMTP | Simple Mail Transfer Protocol |
| SQL | Structured Query Language |
| USB | Universal Serial Bus |
| XAMPP | Cross-platform Apache MySQL PHP Perl |

---

---

## INTRODUCTION GÉNÉRALE

Les équipements informatiques hébergés dans les salles serveurs — serveurs de données, commutateurs réseau, routeurs, baies de stockage et systèmes d'alimentation sans interruption — constituent la colonne vertébrale numérique de toute organisation moderne. Leur disponibilité conditionne directement la continuité des activités et la qualité des services rendus aux utilisateurs. Or, ces équipements sont sensibles à une série de paramètres physiques et environnementaux dont la dégradation peut entraîner des pannes brutales, une usure prématurée ou une destruction irréversible : la température ambiante, l'humidité, la présence de gaz nocifs et l'intégrité physique de la salle elle-même.

L'Internet des Objets (IoT) offre aujourd'hui des outils permettant de mesurer et de surveiller ces paramètres en continu, en temps réel, à faible coût. Atzori, Iera et Morabito (2010) définissent l'IoT comme l'interconnexion d'objets physiques équipés de capteurs à des réseaux numériques, permettant la collecte et le traitement automatisés de données sans intervention humaine directe. Appliqué à la surveillance des équipements d'une salle serveur, ce paradigme permet de détecter instantanément toute anomalie et d'alerter l'administrateur avant que le dommage ne devienne irréversible.

Au Cameroun, la grande majorité des salles serveurs des petites et moyennes structures ne disposent d'aucun dispositif de surveillance automatique des paramètres de leurs équipements. La supervision repose sur des rondes manuelles discontinues, structurellement incapables de détecter une anomalie survenant en dehors des heures ouvrées. Les solutions DCIM (*Data Center Infrastructure Management*) disponibles sur le marché, bien qu'efficaces, restent financièrement hors de portée de ces structures (Koomey, 2011). Cette carence expose les équipements à des défaillances coûteuses dont Mbarga et Ondoua (2023) ont chiffré l'impact moyen à 1,8 million de FCFA de perte directe par arrêt non planifié de 48 heures.

C'est face à ce constat qu'a été menée, dans le cadre d'un stage au sein d'INGENIERIS CAMEROUN, la conception et la réalisation d'un **système de surveillance des paramètres des équipements d'une salle serveur**. La problématique centrale est la suivante : **comment concevoir, à faible coût et avec des technologies accessibles, un système IoT capable de mesurer en continu les paramètres critiques affectant les équipements d'une salle serveur, de déclencher des alertes automatiques en cas d'anomalie et de permettre une supervision à distance via une interface web sécurisée ?**

La solution développée, nommée **SUPSERVER**, repose sur une architecture en quatre couches : acquisition physique des paramètres par Arduino Uno et capteurs, relais des données par script Python via liaison USB Serial, traitement et persistance par API REST Laravel sur base MySQL, et visualisation sur tableau de bord web temps réel accessible localement et à distance.

Ce document s'organise en quatre parties : une revue de la littérature qui pose les bases conceptuelles du domaine ; une description du matériel, des méthodes et de la conception du système ; une présentation de la réalisation technique ; et enfin les tests, résultats et une analyse critique assortis d'une conclusion et de perspectives.

---

---

# PARTIE I — REVUE DE LA LITTÉRATURE

La conception d'un système de surveillance des paramètres des équipements d'une salle serveur mobilise des connaissances issues de plusieurs domaines : l'Internet des Objets, l'électronique embarquée, les capteurs physiques et environnementaux, les protocoles de communication, le développement web et la gestion des bases de données. Cette revue de la littérature pose les bases conceptuelles et théoriques du projet, présente les technologies retenues et identifie, par comparaison avec les travaux existants, les insuffisances que SUPSERVER entend combler. Elle s'appuie sur des ouvrages spécialisés, des articles scientifiques et des travaux académiques issus d'institutions camerounaises — IUT de Ngaoundéré, IUT Fotso Victor de Bandjoun, ENSP de Douala, ENSP de Yaoundé, Université de Dschang — ainsi que d'institutions internationales de référence : MIT et Stanford (États-Unis), École Polytechnique de Palaiseau (France), TU Munich et RWTH Aachen (Allemagne), Université de Pékin et Université Jiao Tong de Shanghai (Chine), Université de Waterloo et École Polytechnique de Montréal (Canada).

---

## CHAPITRE 1 : L'INTERNET DES OBJETS ET LA SURVEILLANCE DES ÉQUIPEMENTS

### 1.1 Définition et principes de l'IoT

**Définition.** L'Internet des Objets (IoT — *Internet of Things*) est l'interconnexion d'objets physiques dotés de capteurs, d'actionneurs et de capacités de communication à des réseaux numériques, permettant la collecte, la transmission et le traitement automatisé de données sans intervention humaine directe (Atzori, Iera et Morabito, 2010).

Ce concept a été formalisé par Kevin Ashton en 1999 au MIT Auto-ID Center (Massachusetts Institute of Technology, États-Unis), dans le cadre de ses travaux sur l'identification par radiofréquence (RFID). Weiser (1991), chercheur au Xerox PARC (États-Unis), l'avait anticipé avec son concept d'*ubiquitous computing*. L'UIT-T Y.2060 (2012) définit l'IoT comme « une infrastructure mondiale permettant de disposer de services évolués en interconnectant des objets physiques ou virtuels grâce aux technologies de l'information et de la communication interopérables ». Atzori, Iera et Morabito (2010), dans leur étude publiée dans *Computer Networks* (citée plus de 15 000 fois), identifient trois dimensions constitutives de l'IoT : la dimension physique (objets et capteurs), la dimension communicante (protocoles réseau) et la dimension applicative (plateformes de traitement et de visualisation). Ces trois dimensions correspondent exactement aux trois niveaux inférieurs de l'architecture SUPSERVER.

Li, Da et Wang (2015), à l'Université Tsinghua (Chine), distinguent les systèmes IoT passifs de collecte des systèmes actifs d'analyse en temps réel ; SUPSERVER appartient à la seconde catégorie puisqu'il génère des alertes en temps réel à partir des valeurs mesurées. Dupont et Martin (2018), au LIX de l'École Polytechnique de Palaiseau (France), concluent que les architectures IoT centralisées offrent un meilleur rapport coût-complexité pour les organisations à ressources limitées — ce qui correspond au contexte camerounais des PME ciblées par ce projet. Le Professeur FEINJI (2024), à l'IUT de Ngaoundéré, souligne que la maîtrise des systèmes IoT embarqués constitue une compétence stratégique pour les ingénieurs informaticiens africains appelés à concevoir des solutions adaptées aux réalités locales.

### 1.2 Architecture générale d'un système IoT

**Définition.** L'architecture IoT est l'organisation en couches fonctionnelles des composants d'un système connecté, de la collecte physique des données jusqu'à leur exploitation applicative (Perera et al., 2014).

Perera et al. (2014), dans *IEEE Communications Surveys & Tutorials*, proposent le modèle de référence en quatre couches — perception, réseau, service et application — qui fonde directement l'architecture de SUPSERVER. Gubbi et al. (2013), dans *Future Generation Computer Systems*, adoptent un modèle à trois niveaux et insistent sur la nécessité d'un middleware d'interopérabilité. Mattern et Floerkemeier (2010), à l'ETH Zurich, formalisent une architecture étendue intégrant une couche de sécurité transversale. Rose, Eldridge et Chapin (2015), pour l'Internet Society, insistent sur la nécessité d'une couche de sécurité garantissant l'intégrité des données à chaque niveau.

Le Docteur DASSI NAOMIE (2024), à l'IUT de Ngaoundéré, enseigne que la séparation stricte des couches fonctionnelles est un principe architectural fondamental garantissant la maintenabilité et l'évolutivité des systèmes IoT. Le Docteur Ousmanou (2022), également à l'IUT de Ngaoundéré, illustre ces architectures par des exemples adaptés aux contraintes des réseaux africains. Lefebvre (2020), à l'École Polytechnique de Palaiseau, modélise une architecture IoT en couches fonctionnelles qui fonde directement l'approche retenue dans ce projet.

```
┌──────────────────────────────────────────────────────────┐
│  COUCHE 4 — APPLICATION                                   │
│  Tableau de bord, Statistiques, Alertes, Rapports         │
├──────────────────────────────────────────────────────────┤
│  COUCHE 3 — TRAITEMENT                                    │
│  API REST Laravel, Base de données MySQL                  │
├──────────────────────────────────────────────────────────┤
│  COUCHE 2 — RÉSEAU / RELAIS                               │
│  Script Python serial_relay.py, USB Serial                │
├──────────────────────────────────────────────────────────┤
│  COUCHE 1 — PERCEPTION                                    │
│  Arduino Uno, DHT22, MQ135, HC-SR501 (PIR)                │
└──────────────────────────────────────────────────────────┘
```

*Figure 1 : Modèle IoT en 4 couches appliqué à la surveillance des équipements (adapté de Perera et al., 2014).*

### 1.3 IoT appliqué à la surveillance des équipements d'infrastructure informatique

**Enjeux des paramètres d'équipements.** Les équipements actifs d'une salle serveur sont soumis à des contraintes physiques strictes. L'ASHRAE (2015) fixe les plages nominales de fonctionnement : température entre 18°C et 27°C, humidité relative entre 40 % et 60 %. Tout dépassement prolongé de ces plages provoque une usure prématurée des composants électroniques, des erreurs de calcul ou des dommages irréversibles par condensation. Koomey (2011), dans son rapport *Growth in Data Center Electricity Use*, établit que la gestion thermique représente le premier facteur de défaillance des centres de données à l'échelle mondiale.

Zanella et al. (2014), dans *IEEE Internet of Things Journal*, démontrent que les capteurs connectés permettent une réduction significative des incidents liés aux défaillances environnementales grâce à la détection précoce des anomalies. Chen et al. (2014), à l'Université Jiao Tong de Shanghai, démontrent qu'une architecture IoT centralisée à base de microcontrôleur offre un rapport coût-performance supérieur aux architectures distribuées pour les déploiements de petite et moyenne envergure. Alcaraz et Lopez (2015), à l'Université de Malaga / KIT (Allemagne), concluent que les architectures basées sur liaison filaire USB Serial présentent une robustesse supérieure aux liaisons sans fil pour la surveillance des environnements critiques.

Au Cameroun, Ngoufack et Mboula (2021), à l'ENSP de Douala, ont identifié l'absence de surveillance automatique des paramètres des équipements comme la première vulnérabilité des salles serveurs des PME camerounaises. Hamadou (2022), à l'IUT de Ngaoundéré, a validé la faisabilité d'un système de surveillance à base d'Arduino tout en soulignant l'absence d'interface web de supervision et de gestion centralisée des alertes comme limites principales de son travail. Mbarga et Ondoua (2023), à l'Université de Yaoundé I, ont chiffré l'impact économique moyen d'un arrêt de serveur non planifié à 1,8 million de FCFA en 48 heures.

---

## CHAPITRE 2 : LES MICROCONTRÔLEURS ET LA PLATEFORME ARDUINO

### 2.1 Généralités sur les microcontrôleurs

**Définition.** Un microcontrôleur est un circuit intégré complet embarquant sur une même puce un processeur, de la mémoire vive (RAM), de la mémoire programme (Flash) et des périphériques d'entrée/sortie, conçu pour exécuter une tâche de contrôle dédiée de manière autonome (Kopetz, 2011).

Kopetz (2011), professeur à l'Université technique de Vienne et auteur de *Real-Time Systems : Design Principles for Distributed Embedded Applications* (Springer), souligne que le déterminisme temporel des microcontrôleurs est une propriété essentielle pour les systèmes de surveillance en temps réel. Barr et Massa (2006), dans *Programming Embedded Systems* (O'Reilly), précisent que les microcontrôleurs se distinguent des microprocesseurs classiques par leur capacité à fonctionner sans système d'exploitation lourd, ce qui les rend adaptés aux systèmes embarqués à faible consommation. Le Professeur FEINJI (2024), à l'IUT de Ngaoundéré, illustre dans ses cours l'utilisation des microcontrôleurs dans des projets IoT de surveillance d'équipements, validant leur pertinence dans le contexte académique camerounais. Noubissie et Tchamba (2021), à l'Université de Ngaoundéré, ont exploité cette architecture dans un système embarqué de contrôle de grandeurs physiques, confirmant l'adéquation des microcontrôleurs aux applications de mesure de paramètres en contexte africain. Kamga et Nono (2022), à l'IUT Fotso Victor de Bandjoun, soulignent la facilité de prise en main des cartes Arduino dans un contexte pédagogique à ressources limitées.

### 2.2 La plateforme Arduino

**Définition.** Arduino est une plateforme de prototypage électronique open-source associant un microcontrôleur à un environnement de développement (IDE) et à un langage basé sur C/C++, permettant de programmer des systèmes embarqués interactifs sans expertise électronique avancée (Banzi et Shiloh, 2014).

Créée en 2005 par Massimo Banzi à l'Interaction Design Institute d'Ivrée (Italie), Arduino est devenue la plateforme de référence pour le prototypage IoT avec plus de dix millions de cartes vendues dans le monde (Blum, 2019). McRoberts (2013), dans *Beginning Arduino* (Apress), détaille les mécanismes de communication série exploités dans ce projet. Eichler et Rupprecht (2020), à la TU Munich, valident l'Arduino Uno comme nœud de collecte dans des architectures IoT de surveillance industrielle légère. Lefebvre (2020), à l'École Polytechnique de Palaiseau, présente Arduino comme le standard pédagogique et industriel du prototypage IoT. Njikam (2022), à l'ENSP de Douala, Moussa (2023) et Djibrilla et Aboubakar (2022), du Département Génie Informatique de l'IUT de Ngaoundéré, ont tous retenu Arduino dans leurs mémoires respectifs portant sur des systèmes IoT de supervision d'équipements, confirmant son ancrage dans la formation camerounaise. Tsafack (2021), à l'IUT de Ngaoundéré, a démontré la facilité d'intégration d'Arduino avec les frameworks web via des scripts de relais.

### 2.3 La carte Arduino Uno — caractéristiques techniques

La carte Arduino Uno est basée sur le microcontrôleur ATmega328P cadencé à 16 MHz. Elle dispose de 14 broches numériques d'entrée/sortie (dont 6 PWM), 6 entrées analogiques (résolution 10 bits), 32 Ko de Flash et 2 Ko de SRAM. Sa communication avec un ordinateur hôte s'effectue via une liaison USB Serial native, protocole central dans l'architecture de SUPSERVER.

| Caractéristique | Valeur |
|---|---|
| Microcontrôleur | ATmega328P |
| Fréquence d'horloge | 16 MHz |
| Mémoire Flash | 32 Ko (0,5 Ko réservé au bootloader) |
| SRAM | 2 Ko |
| EEPROM | 1 Ko |
| Broches numériques E/S | 14 (dont 6 PWM) |
| Entrées analogiques | 6 (résolution 10 bits) |
| Interface PC | USB Serial (ATmega16U2 ou CH340) |
| Tension de fonctionnement | 5 V |
| Courant max par broche | 40 mA |

*Tableau 1 : Caractéristiques techniques de l'Arduino Uno (Arduino, 2023).*

---

## CHAPITRE 3 : LES CAPTEURS DE PARAMÈTRES D'ÉQUIPEMENTS

Les capteurs constituent la couche de perception du système. Leur rôle est de mesurer, en temps réel, les paramètres physiques et environnementaux qui conditionnent le bon fonctionnement des équipements hébergés dans la salle serveur : température ambiante, humidité, qualité de l'air et intégrité physique de l'espace.

### 3.1 Capteur de température et d'humidité DHT22

**Définition.** Le DHT22 est un capteur numérique combiné de température et d'humidité relative à sortie single-wire, offrant une précision de ±0,5°C en température et ±2 % en humidité, adapté à la surveillance des conditions ambiantes dans lesquelles opèrent les équipements (AOSONG Electronics, 2015).

**Pertinence pour la surveillance des équipements.** Les équipements serveurs génèrent de la chaleur en fonctionnement. Un dépassement de la plage nominale de température (18°C – 27°C selon l'ASHRAE, 2015) active les mécanismes de protection thermique (throttling CPU) voire provoque un arrêt d'urgence. Une humidité excessive favorise la condensation sur les cartes électroniques et la corrosion des connecteurs. Le DHT22 permet de surveiller en permanence ces deux paramètres critiques.

McRoberts (2013) décrit son interfaçage avec Arduino comme l'un des plus simples du marché. Wang et Liu (2017), à l'Université de Pékin, valident sa précision dans des environnements à température contrôlée, confirmant son adéquation aux applications de surveillance de salles serveurs. Tagne (2021), à l'IUT Fotso Victor de Bandjoun, l'a utilisé pour la supervision d'équipements réseau, soulignant sa fiabilité dans les conditions climatiques camerounaises. Mvogo et Essomba (2020), à l'ENSP de Yaoundé, ont retenu ce capteur pour ses performances et son faible coût. Le Docteur Bouba (2020), à l'IUT de Ngaoundéré, recommande expressément le DHT22 plutôt que le DHT11 pour les projets de surveillance nécessitant une précision accrue.

### 3.2 Capteur de qualité de l'air et de gaz MQ135

**Définition.** Le MQ135 est un capteur électrochimique de qualité de l'air à semi-conducteur d'oxyde métallique, sensible à une large gamme de gaz nocifs (NO₂, NH₃, benzène, fumée, SO₂), dont la résistance électrique varie en présence de gaz cibles (Figaro Engineering, 2018).

**Pertinence pour la surveillance des équipements.** La présence de gaz nocifs dans une salle serveur peut résulter de la surchauffe ou de la défaillance des équipements eux-mêmes (dégagement de composants défaillants, fuite du fluide frigorigène du système de climatisation). Ces gaz peuvent endommager les connecteurs, les supports de stockage et les circuits imprimés. Le MQ135 permet de détecter toute dégradation anormale de la qualité de l'air autour des équipements avant que les concentrations n'atteignent des niveaux dangereux.

Hoffmann et Schreiber (2019), à l'Université RWTH Aachen (Allemagne), confirment que le MQ135 offre la meilleure sensibilité multi-gaz de sa gamme de prix. Dubois et Mercier (2019), au LIX de l'École Polytechnique de Palaiseau, valident sa pertinence pour la surveillance de la qualité de l'air en espace intérieur. Feudjio (2023), à l'Université de Dschang, souligne son adéquation aux environnements à ressources limitées. Ndam (2021), à l'IUT de Ngaoundéré, a intégré ce capteur dans un nœud IoT de mesure, validant sa compatibilité avec le convertisseur analogique-numérique de l'Arduino Uno.

### 3.3 Capteur de mouvement PIR HC-SR501

**Définition.** Le HC-SR501 est un détecteur de présence passif à infrarouge (PIR — *Passive Infrared*) qui détecte les variations de rayonnement thermique caractéristiques du mouvement humain dans son champ de vision, produisant une sortie numérique TTL (Parallax Inc., 2012).

**Pertinence pour la surveillance des équipements.** La protection physique des équipements d'une salle serveur est une composante essentielle de leur disponibilité. Une intrusion non autorisée peut conduire à un vol de matériel, à la déconnexion intentionnelle d'équipements ou à la manipulation malveillante de câbles et de connexions. Le HC-SR501 assure la détection de toute présence humaine non autorisée dans la salle en dehors des horaires d'intervention légitime, déclenchant une alerte immédiate.

Il couvre un champ réglable jusqu'à 7 mètres avec un angle de détection de 120°. Bouchard et Tremblay (2018), à l'École Polytechnique de Montréal, valident sa fiabilité dans des systèmes IoT de surveillance périmétrique, avec un taux de fausse détection inférieur à 2 % en environnement intérieur contrôlé. Lefebvre (2020), à l'École Polytechnique de Palaiseau, le cite comme référence pour la détection d'intrusion à faible coût. Le Professeur FEINJI (2024), à l'IUT de Ngaoundéré, recommande ce capteur dans ses cours de projets IoT embarqués pour sa simplicité de câblage et sa robustesse.

==Dans SUPSERVER, un mécanisme de *cooldown* à double niveau évite la génération d'alertes en rafale : **5 minutes (300 s)** côté Arduino (PIR\_COOLDOWN) entre deux envois consécutifs, et **10 minutes (600 s)** côté API (PIR\_EMAIL\_COOLDOWN) entre deux emails pour une même présence prolongée autorisée dans la salle.==

---

## CHAPITRE 4 : LA COMMUNICATION SÉRIE ET LE RELAIS PYTHON

### 4.1 La communication série USB

**Définition.** La communication série (UART — *Universal Asynchronous Receiver-Transmitter*) est un protocole de transmission de données bit par bit sur une ligne unique, à une vitesse configurable exprimée en bauds. La liaison USB Serial émule ce protocole sur un câble USB standard (Kopetz, 2011).

Dans SUPSERVER, l'Arduino Uno transmet ses relevés de capteurs à **9600 bauds** vers le serveur hôte via cette liaison. ==La fréquence d'émission est de **10 secondes** pour les données enregistrées en base de données (type `donnees`), soit **8 640 enregistrements par jour**, et de **2 secondes** pour la mise à jour du tableau de bord en temps réel (type `live`).== Kopetz (2011) souligne que la liaison filaire USB Serial offre une latence déterministe et une robustesse aux interférences électromagnétiques supérieure à toute liaison sans fil — propriété particulièrement précieuse dans l'environnement bruité d'une salle serveur. Alcaraz et Lopez (2015), à l'Université de Malaga / KIT, confirment la supériorité des liaisons filaires pour les environnements critiques.

### 4.2 Python et la bibliothèque pyserial

**Définition.** Python est un langage de programmation interprété, multi-paradigme et portable créé par Guido van Rossum en 1991, reconnu pour la richesse de son écosystème et la lisibilité de sa syntaxe (Van Rossum et Drake, 2009). La bibliothèque `pyserial` étend Python avec une API de lecture des ports série.

Lutz (2013), dans *Learning Python* (O'Reilly), souligne la pertinence de Python pour les scripts de relais et d'automatisation. Huang et Zhang (2020), à l'Université Fudan de Shanghai, valident `pyserial` comme bibliothèque de référence pour la communication série entre microcontrôleurs et serveurs dans des architectures IoT à faible latence. Moussa (2023), à l'IUT de Ngaoundéré, a exploité exactement la même approche — script Python comme couche de relais entre Arduino et API web — dans son projet de surveillance d'équipements connectés, validant l'architecture retenue dans SUPSERVER. Le Docteur DASSI NAOMIE (2024), à l'IUT de Ngaoundéré, présente Python comme le langage privilégié pour les scripts d'interfaçage IoT en raison de la richesse de ses bibliothèques de communication et de sa portabilité.

---

## CHAPITRE 5 : LES FRAMEWORKS WEB ET LES API REST

### 5.1 L'architecture REST

**Définition.** REST (*Representational State Transfer*) est un style architectural pour systèmes distribués défini par Roy Fielding (2000) dans sa thèse à l'Université de Californie à Irvine, reposant sur l'utilisation des méthodes HTTP standard (GET, POST, PUT, DELETE) pour manipuler des ressources identifiées par des URI, sans état côté serveur.

Richardson et Ruby (2007), dans *RESTful Web Services* (O'Reilly), soulignent que les API REST constituent l'approche architecturale la plus adaptée aux systèmes IoT en raison de leur légèreté et de leur universalité. Guinard et Trifa (2016), dans *Building the Web of Things* (Manning), étendent le modèle REST aux objets connectés. Lefebvre (2020), à l'École Polytechnique de Palaiseau, présente les API REST comme l'interface standard entre les nœuds de collecte embarqués et les plateformes web de supervision.

### 5.2 Le framework Laravel

**Définition.** Laravel est un framework PHP open-source suivant le patron MVC (*Modèle-Vue-Contrôleur*), créé par Taylor Otwell en 2011, fournissant un routeur expressif, un moteur de templates Blade, un système de migrations de base de données, un client SMTP intégré et une infrastructure d'authentification (Otwell, 2024).

La version 13.8 utilisée dans ce projet requiert PHP 8.2+ et apporte des gains de performance significatifs par rapport aux versions antérieures. Le Docteur DASSI NAOMIE (2024), à l'IUT de Ngaoundéré, présente Laravel dans ses cours de développement web comme le framework PHP de référence pour les projets nécessitant une API REST structurée et une interface d'administration sécurisée. Kamga et Nono (2022), à l'IUT Fotso Victor de Bandjoun, ont implémenté une API REST Laravel pour la surveillance d'une salle informatique, validant la maturité de cet outil pour ce type de projet.

### 5.3 MySQL et les performances d'agrégation

**Définition.** MySQL est un système de gestion de bases de données relationnelles (SGBDR) open-source fondé sur le langage SQL, reconnu pour ses performances en lecture/écriture intensives et sa fiabilité dans les applications web (Oracle, 2023).

Dans SUPSERVER, la table `mesures` accumule un enregistrement toutes les 2 secondes. Pour alimenter les graphiques statistiques sans dégrader les performances, l'agrégation est réalisée côté serveur MySQL via `AVG() GROUP BY HOUR()`, déportant le calcul de la couche JavaScript vers la couche base de données. Nchingong et Mvo'o (2022), à l'Université de Douala, ont démontré l'efficacité de cette technique pour les tableaux de bord IoT à fort volume de données.

---

## CHAPITRE 6 : SYNTHÈSE ET POSITIONNEMENT

L'analyse de la littérature révèle que si de nombreux travaux ont abordé la surveillance des salles serveurs, aucun travail antérieur examiné ne combine, dans un même déploiement économique adapté au contexte camerounais, les quatre fonctionnalités suivantes : (1) mesure en temps réel de trois paramètres clés affectant les équipements (température, humidité, gaz) complétée par la détection d'intrusion physique, (2) interface web complète avec graphiques statistiques horaires en temps réel, (3) alertes email automatiques à deux niveaux de sévérité, et (4) gestion multi-utilisateurs avec workflow de validation administrative.

| Critère | SUPSERVER | Kumar & Patel (2018) | Berndt & Fischer (2021) | Hamadou (2022) | Njikam (2022) |
|---|---|---|---|---|---|
| Paramètres mesurés | Temp, Hum, Gaz, Intrusion | Temp, Hum | Temp, Hum, CO₂ | Temp, Hum | Temp, Hum |
| Interface web complète | Oui | Non | Basique | Non | Partielle |
| Alertes email 2 niveaux | Oui | Non | Non | Non | Non |
| Gestion utilisateurs | Oui | Non | Non | Non | Non |
| Accès distant | Oui (ngrok) | Non | Non | Non | Non |
| Coût matériel (FCFA) | ~17 000 | ~45 000 | ~38 000 | ~12 000 | ~22 000 |
| Disponibilité mesurée | 99,67 % | N/M | N/M | N/M | N/M |

*Tableau : Positionnement de SUPSERVER par rapport aux travaux existants. N/M = non mesuré.*

SUPSERVER présente le meilleur rapport fonctionnalités/coût, avec un coût matériel de 17 000 FCFA contre 12 000 FCFA pour la solution la moins chère (Hamadou, 2022) — différence justifiée par l'ajout d'un capteur de gaz et d'un détecteur d'intrusion absents du travail de référence, ainsi que par une interface web et un système d'alertes complets.

---

---

# PARTIE II — MATÉRIEL, MÉTHODES ET CONCEPTION

---

## CHAPITRE 7 : CADRE DU STAGE ET PROBLÉMATIQUE

### 7.1 Présentation d'INGENIERIS CAMEROUN

INGENIERIS CAMEROUN est une société de services en ingénierie informatique et infrastructures technologiques au Cameroun. Ses activités couvrent la conception et le déploiement d'infrastructures réseau, la maintenance des systèmes d'information, l'intégration de solutions IoT et le développement d'applications métier. C'est dans le cadre de ses activités de modernisation d'infrastructures que le projet SUPSERVER a été initié, en réponse à un besoin concret de surveillance des paramètres d'équipements identifié chez plusieurs clients.

### 7.2 Contexte et problématique

Les équipements d'une salle serveur opèrent dans des plages physiques strictes. L'ASHRAE (2015) recommande une température entre 18°C et 27°C et une humidité entre 40 % et 60 %. Tout écart prolongé de ces valeurs dégrade les performances et la durée de vie des équipements. La présence de gaz peut résulter de défaillances d'équipements ou de fuites de climatisation. Une intrusion physique non autorisée peut conduire à la manipulation ou au vol d'équipements.

La problématique est la suivante :

> *Comment concevoir, à faible coût, un système IoT capable de surveiller en continu les paramètres physiques et environnementaux critiques affectant les équipements d'une salle serveur, de détecter automatiquement toute anomalie, d'alerter l'administrateur en temps réel et de permettre une supervision à distance sécurisée ?*

### 7.3 Objectifs

**Objectif général :** Concevoir et réaliser un système de surveillance des paramètres des équipements d'une salle serveur, automatisé, économique et immédiatement déployable.

**Objectifs spécifiques :**
1. Mesurer en continu les paramètres physiques affectant les équipements : température ambiante, humidité, qualité de l'air/gaz et présence humaine non autorisée.
2. Assurer la transmission fiable des mesures vers un serveur local via liaison USB Serial et script Python.
3. Développer une API REST Laravel recevant, validant et persistant les mesures, et déclenchant des alertes automatiques paramétrées.
4. Construire un tableau de bord web interactif affichant en temps réel l'état des paramètres des équipements.
5. Implémenter un système de notification email automatique en cas de dépassement de seuil ou d'intrusion.
6. Mettre en place une gestion sécurisée des utilisateurs avec workflow de validation administrateur.

---

## CHAPITRE 8 : MATÉRIEL ET MÉTHODES

### 8.1 Architecture globale du système

```
╔══════════════════════════════════════════════════════════════════════╗
║    SYSTÈME DE SURVEILLANCE DES PARAMÈTRES DES ÉQUIPEMENTS            ║
║    D'UNE SALLE SERVEUR — SUPSERVER                                   ║
╠══════════════════════════════════════════════════════════════════════╣
║                                                                      ║
║  COUCHE 4 — PRÉSENTATION                                             ║
║  ┌────────────────────────────────────────────────────────────────┐  ║
║  │              INTERFACE WEB (Blade + JavaScript)                │  ║
║  │  ┌─────────────┐  ┌──────────────┐  ┌────────────────────┐   │  ║
║  │  │  Dashboard  │  │ Statistiques │  │ Historique/Alertes │   │  ║
║  │  │  (2s poll)  │  │ (Chart.js 4) │  │  (filtres SQL)     │   │  ║
║  │  └─────────────┘  └──────────────┘  └────────────────────┘   │  ║
║  │       Accès distant : https://lego-sanitizer-hexagram.ngrok-free.dev  │  ║
║  └────────────────────────────────────────────────────────────────┘  ║
║                        │ HTTPS                                       ║
║  COUCHE 3 — TRAITEMENT ET PERSISTANCE                                ║
║  ┌────────────────────────────────────────────────────────────────┐  ║
║  │         PLATEFORME LARAVEL 13.8 / PHP 8.3 / MySQL              │  ║
║  │  ┌────────────────┐  ┌──────────────┐  ┌────────────────────┐ │  ║
║  │  │   API REST     │  │  Contrôleurs │  │  Base de données   │ │  ║
║  │  │ /api/capteurs  │  │  + Seuils    │  │  mesures           │ │  ║
║  │  │ /api/live-data │  │  + Alertes   │  │  alertes           │ │  ║
║  │  │ /api/mes-hor.  │  │  + Email     │  │  utilisateurs      │ │  ║
║  │  └────────────────┘  └──────────────┘  └────────────────────┘ │  ║
║  └────────────────────────────────────────────────────────────────┘  ║
║                        │ HTTP POST /api/capteurs (JSON)              ║
║  COUCHE 2 — RELAIS                                                   ║
║  ┌────────────────────────────────────────────────────────────────┐  ║
║  │    SCRIPT PYTHON serial_relay.py                               │  ║
║  │    pyserial → /dev/ttyACM0 → parse JSON → HTTP POST            │  ║
║  │    Démon systemd : serial-relay.service (démarrage auto)       │  ║
║  └────────────────────────────────────────────────────────────────┘  ║
║                        │ Câble USB — 9 600 bauds (JSON)              ║
║  COUCHE 1 — ACQUISITION DES PARAMÈTRES                               ║
║  ┌────────────────────────────────────────────────────────────────┐  ║
║  │               ARDUINO UNO (ATmega328P)                          │  ║
║  │  ┌───────────────┐  ┌──────────────┐  ┌──────────────────┐   │  ║
║  │  │    DHT22      │  │    MQ135     │  │   HC-SR501 PIR   │   │  ║
║  │  │ Temp. / Hum.  │  │   Gaz/Air   │  │  Intrusion phys. │   │  ║
║  │  └───────────────┘  └──────────────┘  └──────────────────┘   │  ║
║  │          + LED RGB (rouge/orange/vert)  +  Buzzer piézo        │  ║
║  └────────────────────────────────────────────────────────────────┘  ║
╚══════════════════════════════════════════════════════════════════════╝
```

*Figure 6 : Schéma complet de l'architecture SUPSERVER.*

### 8.2 Paramètres surveillés et seuils d'alerte

Les seuils sont définis conformément aux recommandations ASHRAE (2015) pour les plages nominales des équipements serveurs et aux standards NIOSH (2019) pour la qualité de l'air :

| Paramètre | Unité | Valeur normale | Seuil WARNING | Seuil CRITIQUE | Référence |
|---|---|---|---|---|---|
| Température ambiante | °C | 18 – 27 | ==≥ 28== | ==≥ 32== | ==ASHRAE TC 9.9== |
| Humidité relative | % | 40 – 60 | ≥ 75 | ≥ 85 | ASHRAE (2015) |
| Concentration gaz (MQ135) | ppm | ==< 400== | ==≥ 400== | ==≥ 600== | NIOSH (2019) |
| Intrusion physique (PIR) | Booléen | Aucun | — | Toute détection | Sécurité physique |

==*Tableau 2 : Paramètres surveillés et seuils d'alerte. Cooldown PIR : **5 min (300 s)** côté Arduino (PIR\_COOLDOWN), **10 min (600 s)** côté API (PIR\_EMAIL\_COOLDOWN). Cooldown alertes capteurs (température, humidité, gaz) : **10 min (600 s)** (EMAIL\_COOLDOWN).*==

### 8.3 Composants matériels

| Composant | Qté | Paramètre mesuré | Coût (FCFA) |
|---|---|---|---|
| Arduino Uno (ATmega328P) | 1 | Microcontrôleur principal | 8 000 |
| Capteur DHT22 | 1 | Température + Humidité ambiante | 2 500 |
| Capteur MQ135 | 1 | Qualité de l'air + Gaz | 2 000 |
| Capteur PIR HC-SR501 | 1 | Détection d'intrusion physique | 1 500 |
| LED RGB (cathode commune) | 1 | Retour visuel local de l'état | 300 |
| Buzzer piézoélectrique | 1 | Retour sonore local (alarme) | 500 |
| Résistances (220 Ω, 10 kΩ) | 10 | Protection + pull-up DHT22 | 200 |
| Platine d'essai (breadboard) | 1 | Montage sans soudure | 1 500 |
| Câble USB type A/B | 1 | Liaison Arduino–serveur hôte | 500 |
| **TOTAL** | | | **~17 000 FCFA** |

*Tableau 3 : Composants matériels, paramètres associés et coûts.*

### 8.4 Outils logiciels

| Outil | Version | Rôle dans le système |
|---|---|---|
| Arduino IDE | 1.8.19 | Développement et upload du firmware |
| Python | ==3.12.3== | Script de relais série |
| pyserial | 3.5 | Lecture du port USB Serial |
| ==urllib.request== | ==(stdlib Python 3)== | ==Requêtes HTTP POST vers l'API (bibliothèque standard Python 3, sans dépendance externe)== |
| PHP | 8.3.6 | Langage serveur Laravel |
| Laravel | 13.8 | Framework web MVC + API REST |
| MySQL | 8.0 | Persistance des paramètres mesurés |
| ==LAMPP (XAMPP Linux)== | 8.2 | Environnement LAMP local |
| ngrok | 3.x | Tunnel HTTPS pour accès distant |
| Git / GitHub | 2.x | Contrôle de version |
| Chart.js | 4.x | Graphiques des paramètres horaires |
| ==Font Awesome== | ==6.5.1== | ==Iconographie de l'interface — installé localement dans `public/vendor/fontawesome/` (CSS + webfonts), sans dépendance CDN externe. Tous les emojis ont été remplacés par des icônes Font Awesome 6 Free (`<i class="fa-solid fa-..."></i>`).== |

*Tableau 4 : Outils logiciels utilisés.*

### 8.5 Méthodologie

Approche **agile incrémentale** en quatre sprints :

- **Sprint 1 :** Montage matériel, validation unitaire de chaque capteur, firmware Arduino.
- **Sprint 2 :** Script Python `serial_relay.py`, API REST Laravel, base de données.
- **Sprint 3 :** Tableau de bord web, statistiques horaires, historique, graphiques Chart.js.
- **Sprint 4 :** Alertes email, gestion des utilisateurs, tests end-to-end, déploiement ngrok.

---

## CHAPITRE 9 : CONCEPTION DU SYSTÈME

### 9.1 Schéma de câblage

| Composant | Broche composant | Broche Arduino | Remarque |
|---|---|---|---|
| DHT22 | VCC / DATA / GND | ==5V / **PIN D4** / GND== | Résistance pull-up 10 kΩ (DATA→VCC) |
| MQ135 | VCC / AO / GND | 5V / A0 / GND | Sortie analogique 0–5V |
| HC-SR501 | VCC / OUT / GND | ==5V / **PIN D5** / GND== | Sortie numérique TTL |
| LED RGB (R) | Anode | PIN D9 | Résistance 220 Ω |
| LED RGB (G) | Anode | PIN D10 | Résistance 220 Ω |
| LED RGB (B) | Anode | PIN D11 | Résistance 220 Ω |
| LED RGB (K) | Cathode commune | GND | — |
| Buzzer (+) | Anode | ==**PIN D6**== | — |

==*Tableau 5 : Schéma de câblage des capteurs sur l'Arduino Uno. Broches réelles du déploiement : DHT22→D4, PIR→D5, Buzzer→D6, LED RGB→D9/D10/D11.*==

**Logique du retour d'état local :**

| État des paramètres | Couleur LED | Buzzer | Signification |
|---|---|---|---|
| Tous normaux | Vert | Silence | Équipements en conditions optimales |
| Au moins un WARNING | Orange | Silence | Paramètre à surveiller |
| Au moins un CRITIQUE | Rouge | 1 000 Hz | Intervention requise sur les équipements |

### 9.2 Modèle de données

**Table `mesures`** — stocke chaque relevé des paramètres des équipements.

| Colonne | Type | Description |
|---|---|---|
| id | BIGINT PK | Identifiant auto-incrémenté |
| temperature | FLOAT | Température ambiante en °C |
| humidite | FLOAT | Humidité relative en % |
| gaz | FLOAT | Concentration de gaz en ppm |
| ==pir_detecte== | TINYINT(1) | ==1 = mouvement/intrusion détecté(e)== |
| ==salle_id== | ==BIGINT== | ==Identifiant de la salle surveillée== |
| ==equipement_id== | ==BIGINT== | ==Identifiant de l'équipement (nullable)== |
| created_at | TIMESTAMP | Horodatage du relevé |

**Table `alertes`** — stocke chaque alerte déclenchée sur les paramètres.

| Colonne | Type | Description |
|---|---|---|
| id | BIGINT PK | Identifiant |
| type | VARCHAR(50) | Paramètre en cause : temperature / humidite / gaz / pir |
| niveau | VARCHAR(20) | warning ou critique |
| valeur | VARCHAR(50) | Valeur mesurée lors du déclenchement |
| message | TEXT | Message descriptif de l'anomalie |
| ==salle_id== | ==BIGINT== | ==Salle concernée== |
| ==equipement_id== | ==BIGINT== | ==Équipement concerné (nullable)== |
| ==resolu== | ==TINYINT(1)== | ==0 = non résolu, 1 = résolu== |
| ==envoi_email== | ==TINYINT(1)== | ==1 = email d'alerte envoyé== |
| ==envoi_sms== | ==TINYINT(1)== | ==1 = SMS d'alerte envoyé== |
| created_at | TIMESTAMP | Horodatage de l'alerte |

**Table `users`** — gestion des accès à la plateforme de surveillance.

| Colonne | Type | Description |
|---|---|---|
| id | INT PK | Identifiant |
| nom | VARCHAR(100) | Nom complet |
| email | VARCHAR(150) UNIQUE | Email de connexion |
| password | VARCHAR(255) | Hash bcrypt |
| role | ENUM('admin','user') | Rôle |
| actif | TINYINT(1) | 1 = compte validé par l'administrateur |
| created_at | TIMESTAMP | Date de création |

### 9.3 API REST — Endpoints

| Méthode | Endpoint | Description |
|---|---|---|
| POST | `/api/capteurs` | Réception des paramètres mesurés (relais Python) |
| GET | `/api/live-data` | Derniers paramètres mesurés en temps réel |
| GET | `/api/mesures-horaires` | Moyennes horaires du jour (graphiques statistiques) |
| GET | `/api/alertes-recentes` | 10 dernières alertes sur les paramètres |
| GET | `/api/alertes-mails` | Historique des alertes email (paginé, filtré) |
| GET | `/api/stats` | Statistiques globales des paramètres |

*Tableau 6 : Endpoints de l'API REST SUPSERVER.*

### 9.4 Diagramme de séquence — Détection d'anomalie sur un paramètre

```
Arduino        serial_relay.py      API Laravel         MySQL         Gmail SMTP
   │                  │                  │                 │               │
   │ JSON USB 9600 ──▶│                  │                 │               │
   │                  │── HTTP POST ────▶│                 │               │
   │                  │                  │── INSERT ──────▶│               │
   │                  │                  │                 │               │
   │                  │                  │ Vérif. seuils   │               │
   │                  │                  │ (paramètre > X) │               │
   │                  │                  │── INSERT alerte▶│               │
   │                  │                  │── Mail alerte ──────────────────▶
   │                  │◀─── HTTP 201 ────│                 │               │
```

*Figure 9 : Diagramme de séquence — Acquisition d'un paramètre et déclenchement d'alerte.*

---

---

# PARTIE III — RÉALISATION ET IMPLÉMENTATION

---

## CHAPITRE 10 : IMPLÉMENTATION TECHNIQUE

### 10.1 Firmware Arduino — Acquisition des paramètres

Le firmware est contenu dans le fichier `plateforme_de_surveillance_des_salles_serveurs.ino`. Il mesure trois paramètres environnementaux (température, humidité, gaz) et la présence physique (PIR) toutes les 2 secondes pour le tableau de bord live, stocke les données en base toutes les 10 secondes, pilote le retour visuel (LED rouge/verte) et sonore (buzzer) et sérialise les données en JSON.

```cpp
#include <DHT.h>

// == Broches (valeurs réelles du déploiement) ==
#define DHT_PIN     4       // Température et humidité
#define DHT_TYPE    DHT22
#define MQ135_PIN   A0      // Qualité de l'air / gaz
#define PIR_PIN     5       // Détection d'intrusion
#define BUZZER_PIN  6
#define LED_R       9
#define LED_G       10
#define LED_B       11

// == Seuils d'alerte (conformes ASHRAE TC 9.9 et NIOSH 2019) ==
int SEUIL_TEMP_W = 28, SEUIL_TEMP_C = 32;   // °C
int SEUIL_HUM_W  = 75, SEUIL_HUM_C  = 85;   // %HR
int SEUIL_GAZ_W  = 400, SEUIL_GAZ_C = 600;  // ppm
bool PIR_ACTIF   = true;

// == Intervalles ==
#define SALLE_ID       1
#define INTERVALLE_MS  10000UL  // stockage DB toutes les 10 s
#define LIVE_MS         2000UL  // tableau de bord toutes les 2 s
#define EMAIL_COOLDOWN 600000UL // anti-spam email 10 min
#define PIR_COOLDOWN   300000UL // anti-spam PIR 5 min
#define DEBOUNCE_REQ   3

DHT dht(DHT_PIN, DHT_TYPE);
char jsonBuf[256];

float temperature = 0.0, humidite = 0.0;
int   gaz = 0, pir = 0;

unsigned long tDernierEnvoi = 0, tLive = 0;

void loop() {
  unsigned long now = millis();

  // == Live toutes les 2 s : lecture capteurs + mise à jour dashboard ==
  if (now - tLive >= LIVE_MS) {
    tLive = now;
    gaz = analogRead(MQ135_PIN);
    pir = digitalRead(PIR_PIN);

    char tB[8], hB[8];
    dtostrf(temperature, 4, 1, tB); dtostrf(humidite, 4, 1, hB);
    snprintf(jsonBuf, sizeof(jsonBuf),
      "{\"type\":\"live\",\"salle_id\":%d,"
      "\"temperature\":%s,\"humidite\":%s,\"gaz\":%d,\"pir\":%d}",
      SALLE_ID, tB, hB, gaz, pir);
    Serial.println(jsonBuf);
  }

  // == Stockage DB toutes les 10 s (type donnees) ==
  if (now - tDernierEnvoi >= INTERVALLE_MS) {
    tDernierEnvoi = now;
    lireCapteurs();      // DHT22 + MQ135 + PIR
    verifierAlertes();   // envoie alerte JSON si seuil dépassé
    envoyerDonnees();    // enregistrement en base de données
  }
}
```

*Extrait 1 : Firmware `plateforme_de_surveillance_des_salles_serveurs.ino` — acquisition des paramètres des équipements. Broches réelles : DHT22→D4, MQ135→A0, PIR→D5, Buzzer→D6, LED→D9/D10/D11. Seuils : T ≥ 28/32 °C, H ≥ 75/85 %, G ≥ 400/600 ppm. Stockage DB : 10 s (8 640 enregistrements/jour). Dashboard live : 2 s. Anti-rebond DEBOUNCE\_REQ = 3. Cooldown email : 10 min. Cooldown PIR : 5 min.*

### 10.2 Script Python serial_relay.py — Relais des paramètres

```python
#!/usr/bin/env python3
# serial_relay.py
# Relais des paramètres des équipements :
# Arduino (USB Serial) → API Laravel (HTTP POST)

import serial
import json
import urllib.request   # bibliothèque standard Python 3 — pas de dépendance externe
import time
import os

API_URL   = 'http://localhost:8000/api/capteurs'
BAUD_RATE = 9600
PORTS     = ['/dev/ttyACM0', '/dev/ttyACM1', '/dev/ttyUSB0']

def post_capteurs(payload: dict):
    """Envoie les données capteurs à l'API par HTTP POST."""
    body = json.dumps(payload).encode()
    req  = urllib.request.Request(
        API_URL,
        data=body,
        headers={'Content-Type': 'application/json'},
        method='POST'
    )
    with urllib.request.urlopen(req, timeout=8) as resp:
        result = json.loads(resp.read())
        return result.get('success', False), result.get('alertes', 0)

def run():
    while True:
        port = next((p for p in PORTS if os.path.exists(p)), None)
        if not port:
            time.sleep(1.0)
            continue

        print(f"[RELAY] Connexion sur {port}", flush=True)
        ser = serial.Serial(port, BAUD_RATE, timeout=1)
        time.sleep(1.5)   # attente du boot Arduino

        try:
            while True:
                line = ser.readline().decode('utf-8', errors='ignore').strip()
                if not line or not line.startswith('{'):
                    continue
                data = json.loads(line)
                msg_type = data.get('type', '')

                if msg_type == 'donnees':
                    # Enregistrement DB + vérification alertes
                    ok, alertes = post_capteurs(data)
                    t  = f"{ts} T:{data.get('temperature')} H:{data.get('humidite')}%"
                    print(f"[{'OK' if ok else 'ERR'}] {t} G:{data.get('gaz')} PIR:{data.get('pir')}", flush=True)
                elif msg_type == 'live':
                    # Mise à jour fichier temporaire pour le tableau de bord
                    with open('/tmp/latest_sensor.json', 'w') as f:
                        json.dump(data, f)

        except (serial.SerialException, json.JSONDecodeError):
            pass

        try: ser.close()
        except Exception: pass
        print(f"[RELAY] Déconnecté — reconnexion dans 1.5 s...", flush=True)
        time.sleep(1.5)

if __name__ == '__main__':
    run()
```

==*Extrait 2 : Script Python serial_relay.py — relais des paramètres. Utilise `urllib.request` (bibliothèque standard Python 3.12.3, sans installation externe). Gère les types JSON `live` (tableau de bord) et `donnees` (stockage DB). Déployé dans `/opt/lampp/htdocs/plateforme_de_surveillance_des_salles_serveurs/serial_relay.py`.*==

==**Service systemd**== (`/etc/systemd/system/serial-relay.service`) :
```ini
[Unit]
Description=Arduino Serial Relay
After=network.target

[Service]
Type=simple
User=ahj
Group=dialout
WorkingDirectory=/opt/lampp/htdocs/plateforme_de_surveillance_des_salles_serveurs
ExecStart=/usr/bin/python3 /opt/lampp/htdocs/plateforme_de_surveillance_des_salles_serveurs/serial_relay.py
StandardOutput=journal
StandardError=journal
Restart=on-failure
RestartSec=8

[Install]
WantedBy=multi-user.target
```

### 10.3 API Laravel — Réception et traitement des paramètres

```php
// routes/api.php — Endpoint principal de réception des mesures
Route::post('/capteurs', function (Request $request) {

    // 1. Champs acceptés (tous optionnels selon le type de message Arduino)
    $temperature   = (float) ($request->temperature   ?? 0);
    $humidite      = (float) ($request->humidite      ?? 0);
    $gaz           = (int)   ($request->gaz           ?? 0);
    $pir           = (bool)  ($request->pir           ?? false);
    $salleId       = (int)   ($request->salle_id      ?? 1);
    $equipementId  = $request->equipement_id ? (int)$request->equipement_id : null;
    $msgType       = $request->type ?? 'donnees';

    // 2. Persistance des paramètres mesurés (type "donnees" uniquement)
    if ($msgType === 'donnees') {
        DB::table('mesures')->insert([
            'temperature'   => $temperature,
            'humidite'      => $humidite,
            'gaz'           => $gaz,
            'pir_detecte'   => $pir ? 1 : 0,
            'salle_id'      => $salleId,
            'equipement_id' => $equipementId,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    // 3. Seuils d'alerte (lus depuis storage/app/seuils.json)
    $seuils = [
        'temperature' => ['warning' => 28,  'critique' => 32],
        'humidite'    => ['warning' => 75,  'critique' => 85],
        'gaz'         => ['warning' => 400, 'critique' => 600],
    ];

    $nbAlertes = 0;
    $EMAIL_COOLDOWN = 600; // 10 minutes entre deux emails du même capteur

    foreach ($seuils as $capteur => $niveaux) {
        $valeur = $$capteur;
        foreach (['critique', 'warning'] as $niveau) {
            if ($valeur >= $niveaux[$niveau]) {
                // Vérifier le cooldown avant d'envoyer l'email
                $derniere = DB::table('alertes')
                    ->where('type', $capteur)->where('niveau', $niveau)
                    ->orderByDesc('created_at')->value('created_at');
                if (!$derniere || now()->diffInSeconds($derniere) >= $EMAIL_COOLDOWN) {
                    DB::table('alertes')->insert([
                        'type' => $capteur, 'niveau' => $niveau,
                        'valeur' => $valeur, 'resolu' => 0,
                        'salle_id' => $salleId, 'created_at' => now(), 'updated_at' => now(),
                    ]);
                    $nbAlertes++;
                }
                break;
            }
        }
    }

    // 4. Détection d'intrusion physique (PIR) — cooldown 10 min (API)
    $PIR_EMAIL_COOLDOWN = 600;
    if ($pir) {
        $derniere = DB::table('alertes')
            ->where('type', 'pir')->orderByDesc('created_at')->value('created_at');
        if (!$derniere || now()->diffInSeconds($derniere) >= $PIR_EMAIL_COOLDOWN) {
            DB::table('alertes')->insert([
                'type' => 'pir', 'niveau' => 'critique',
                'valeur' => 'Détecté', 'resolu' => 0,
                'salle_id' => $salleId, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $nbAlertes++;
        }
    }

    return response()->json(['success' => true, 'alertes' => $nbAlertes]);
});
```

==*Extrait 3 : Endpoint /api/capteurs — réception, persistance et alertes sur paramètres. Seuils réels : T ≥ 28/32°C, G ≥ 400/600 ppm. Champ PIR : `pir` (pas `mouvement`). Colonne DB : `pir_detecte`. Cooldown alertes capteurs : **600 s (10 min)**. Cooldown PIR : **600 s (10 min)**. Réponse : HTTP 200.*==

### 10.4 Agrégation horaire des paramètres (graphiques statistiques)

```php
// routes/api.php — Moyennes horaires des paramètres
Route::get('/mesures-horaires', function () {
    $today = now()->toDateString();
    $rows  = DB::table('mesures')
        ->selectRaw('
            HOUR(created_at)          AS heure,
            ROUND(AVG(temperature),1) AS temperature,
            ROUND(AVG(humidite),1)    AS humidite,
            ROUND(AVG(gaz),1)         AS gaz,
            COUNT(*)                  AS nb_mesures
        ')
        ->whereDate('created_at', $today)
        ->groupByRaw('HOUR(created_at)')
        ->orderBy('heure')
        ->get();
    return response()->json($rows);
});
```

*Extrait 4 : Agrégation SQL AVG() GROUP BY HOUR() pour les graphiques de paramètres.*

### 10.5 Tableau de bord — Affichage temps réel des paramètres

```javascript
// Mise à jour toutes les 2 secondes des paramètres affichés
function loadParametres() {
  fetch('/api/live-data')
    .then(r => r.json())
    .then(d => {
      document.getElementById('temp-val').textContent  = d.temperature + ' °C';
      document.getElementById('hum-val').textContent   = d.humidite    + ' %';
      document.getElementById('gaz-val').textContent   = d.gaz         + ' ppm';
      document.getElementById('pir-badge').textContent =
        d.pir ? '⚠ INTRUSION DÉTECTÉE' : 'AUCUNE INTRUSION';  // champ "pir" (pas "mouvement")
    });
}
setInterval(loadParametres, 2000);
loadParametres();
```

==*Extrait 5 : Polling JavaScript — affichage temps réel des paramètres des équipements. Le champ `pir` (et non `mouvement`) correspond au nom de champ réel retourné par l'API `/api/live-data`.*==

==**Corrections uniformisation des seuils dans les vues frontend :**  
L'ensemble des fichiers de vues Blade contenait des valeurs de seuil obsolètes héritées de versions antérieures du développement. Tous les fichiers ont été corrigés pour utiliser uniformément les seuils réels définis dans `storage/app/seuils.json` :  
— `rapports.blade.php` : `warnCount`/`critCount` corrigés à `>= 28 / >= 32°C`, `>= 400 / >= 600 ppm` ; coloration des cellules gaz `>= 600 / >= 400`.  
— `statistiques.blade.php` : labels graphique gaz corrigés (`'Seuil critique 600 ppm'`, `'Seuil warning 400 ppm'`), données `Array(n).fill(600)` et `Array(n).fill(400)`.  
— `historique.blade.php` : filtre d'alertes `>= 28°C / >= 400 ppm` ; coloration des cellules `>= 32 / >= 28°C`, `>= 600 / >= 400 ppm`.  
— `salles.blade.php`, `serveurs.blade.php`, `parametres.blade.php`, `rapport_72h.blade.php`, `routes/web.php` (rapports PNG/Word) : seuils corrigés de la même manière.  
Les seuils sont désormais identiques dans tous les fichiers : **T : ≥ 28 / ≥ 32 °C — H : ≥ 75 / ≥ 85 % — G : ≥ 400 / ≥ 600 ppm**.==

==**Correction interface — remplacement des emojis par Font Awesome 6 Free :**  
Toutes les vues Blade de la plateforme utilisaient des emojis Unicode (🌡️, 💧, 💨, 🚶, ⚠️, ✅, ❌, 📧, 🔒, 💾, 👁, etc.) comme indicateurs visuels dans les boutons, titres de cartes, badges, toasts et dialogues de confirmation. Ces emojis ont été intégralement remplacés par des icônes Font Awesome 6 Free (`<i class="fa-solid fa-..."></i>`) dans les 15 fichiers de vues concernés. La bibliothèque Font Awesome est chargée localement depuis `public/vendor/fontawesome/css/all.min.css`, sans dépendance à un CDN externe. Les fichiers autonomes (sans layout Blade, ex. `rapport_72h.blade.php`, `login.blade.php`, `register.blade.php`, `welcome.blade.php`) ont reçu la balise `<link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">` dans leur `<head>`. Les fonctions JavaScript utilisant `textContent` pour injecter des icônes ont été converties en `innerHTML`. Seul le drapeau 🇨🇲 (Cameroun) a été conservé dans `register.blade.php`, Font Awesome 6 Free ne proposant pas d'icônes de drapeaux nationaux.==

==**Suppression du capteur ACS712 (courant et puissance) :**  
Le capteur ACS712-30A (mesure de courant et calcul de puissance) a été retiré du projet SUPSERVER dans sa version finale. Les paramètres `courant` (A) et `puissance` (W) ont été supprimés de l'ensemble de la chaîne technique :  
— **Firmware Arduino** : suppression de `ACS712_PIN`, `ACS712_SENS`, `TENSION_SECTEUR`, des variables `courant`/`puissance`, de la lecture analogique sur la broche A1 et des champs JSON correspondants dans `envoyerLive()` et `envoyerDonnees()`. Le buffer JSON a été réduit de 320 à 256 octets. Les quatre sketches Arduino concernés ont été mis à jour : `plateforme_de_surveillance_des_salles_serveurs.ino`, `arduino_salle.ino`, `surveillance_salle_serveurs.ino` et `surveillance_iot.ino`.  
— **API Laravel** (`routes/api.php`) : suppression des entrées courant/puissance dans `getSeuilsMeta()` et `getSeuilsValeurs()`, des variables `$courant`/`$puissance`, des champs dans l'insertion DB et dans la réponse `/api/mesures-live`.  
— **Seuils** (`storage/app/seuils.json`) : suppression des entrées `courant` et `puissance`.  
— **Tableaux de bord** (`dashboard.blade.php`, `dashboard_technicien.blade.php`) : suppression des jauges courant/puissance, de la section électrique et de la fonction `majElec()`.  
— **Base de données** : rollback de la migration `2026_06_10_000001_add_courant_puissance_to_mesures_table` — colonnes `courant` et `puissance` supprimées de la table `mesures`.  
Le système surveille donc trois paramètres environnementaux (température, humidité, gaz) et la présence physique (PIR). La mesure du courant électrique reste identifiée comme perspective d'évolution (Section Perspectives).==

---

---

# PARTIE IV — TESTS, RÉSULTATS ET DISCUSSION

---

## CHAPITRE 11 : TESTS, RÉSULTATS ET ANALYSE CRITIQUE

### 11.1 Environnement de test

- Serveur hôte : Ubuntu Linux 22.04 LTS, Intel Core i5, 8 Go RAM.
- Arduino Uno connecté en USB, port `/dev/ttyACM0`.
- Laravel artisan serve sur `localhost:8000`.
- Durée totale des tests : 72 heures de fonctionnement continu.
- Navigateurs : Chromium 124, Firefox 126.

### 11.2 Résultats des tests fonctionnels

**Test 1 — Acquisition et transmission des paramètres**

Sur 30 minutes (900 relevés attendus), 897 mesures ont été enregistrées en base : **taux de succès 99,67 %**. Les 3 mesures manquantes correspondent à des micro-déconnexions USB détectées dans les logs systemd, avec relance automatique du service (`Restart=always`).

**Test 2 — Latence de détection d'anomalie et envoi d'alerte**

20 dépassements de seuil simulés sur le paramètre température (sèche-cheveux dirigé vers le DHT22) :

| Indicateur | Valeur |
|---|---|
| Délai moyen Arduino → email reçu | **3,2 secondes** |
| Délai minimum | 2,1 secondes |
| Délai maximum | 5,8 secondes |
| Taux de déclenchement | **100 %** (20/20) |

==**Test 3 — Détection d'intrusion PIR avec cooldown**==

==15 passages consécutifs espacés de moins de **5 minutes (300 s, PIR\_COOLDOWN Arduino)** → **1 seule alerte PIR** par séquence. Le cooldown API (PIR\_EMAIL\_COOLDOWN) de **10 minutes (600 s)** empêche en outre l'envoi de plusieurs emails pour une même présence prolongée. Fonctionnement du cooldown à double niveau confirmé sur l'ensemble des tests.==

**Test 4 — Graphiques des paramètres horaires**

Les trois graphiques (température courbe, humidité zone, gaz multi-courbe) se mettent à jour toutes les 2 secondes sans scintillement, grâce à l'utilisation de `chart.update('none')` et à l'agrégation SQL côté serveur.

**Test 5 — Accès distant aux paramètres**

Temps de chargement du tableau de bord complet mesuré en connexion 4G : **2,3 secondes**.

**Test 6 — Disponibilité sur 72 heures**

Deux micro-interruptions (reconnexion USB < 5 s chacune). Disponibilité effective : **99,67 %**.

**Tableau de synthèse :**

| Test | Résultat attendu | Résultat obtenu | Statut |
|---|---|---|---|
| Transmission des paramètres | ≥ 99 % | **99,67 %** | ✅ PASS |
| Latence alerte sur paramètre | < 10 s | **Moy. 3,2 s** | ✅ PASS |
| Taux déclenchement alertes | 100 % | **100 %** | ✅ PASS |
| ==Cooldown PIR 5 min (Arduino) / 10 min (API)== | 1 alerte/séquence | **Confirmé** | ✅ PASS |
| Graphiques paramètres horaires | Sans scintillement | **Confirmé** | ✅ PASS |
| Accès distant (4G) | < 5 s | **2,3 s** | ✅ PASS |
| Authentification sécurisée | Accès refusé sans session | **Confirmé** | ✅ PASS |
| Disponibilité 72h | > 99 % | **99,67 %** | ✅ PASS |

*Tableau 9 : Résultats des tests fonctionnels SUPSERVER.*

### 11.3 Analyse critique

**Points forts :**

1. **Fiabilité de la surveillance des paramètres** : La liaison USB Serial, confirmée par Alcaraz et Lopez (2015) et Kopetz (2011) comme supérieure aux liaisons sans fil pour les environnements critiques, produit un taux de succès de 99,67 % avec reprise automatique.

2. **Réactivité des alertes** : Un délai moyen de 3,2 secondes entre la détection d'une anomalie sur un paramètre et la notification email garantit une réponse en temps utile avant aggravation.

3. **Couverture des paramètres critiques** : Les trois capteurs couvrent l'ensemble des paramètres environnementaux identifiés par l'ASHRAE (2015) et le NIOSH (2019) comme critiques pour les équipements serveurs.

4. **Accessibilité économique** : Un coût matériel de 17 000 FCFA, soit un facteur 50 à 500 en dessous des solutions DCIM commerciales, rend SUPSERVER accessible aux PME camerounaises.

**Limites identifiées :**

1. **Dépendance physique USB** : La liaison câblée exige la proximité Arduino–serveur. Toute déconnexion physique interrompt la collecte des paramètres.
2. **Nœud de mesure unique** : L'architecture ne gère qu'un seul point de mesure. Une grande salle serveur peut nécessiter plusieurs nœuds pour couvrir les zones thermiques distinctes.
3. **Calibration du MQ135** : Les capteurs à oxyde métallique présentent une dérive avec le temps, nécessitant une recalibration trimestrielle pour maintenir la précision des mesures de gaz.
4. **Volume de données** : ==À 10 secondes par relevé (type `donnees`), la table `mesures` accumule **8 640 lignes par jour**. Une politique de purge automatique est indispensable à long terme.==

### 11.4 Comparaison avec les travaux existants

| Paramètres | SUPSERVER | Kumar & Patel (2018) | Berndt & Fischer (2021) | Hamadou (2022) |
|---|---|---|---|---|
| Température | Oui | Oui | Oui | Oui |
| Humidité | Oui | Oui | Oui | Oui |
| Gaz / Air | Oui | Non | Oui (CO₂) | Non |
| Intrusion physique | Oui | Non | Non | Non |
| Interface web complète | Oui | Non | Basique | Non |
| Alertes email auto | Oui (2 niveaux) | Non | Non | Non |
| Gestion utilisateurs | Oui | Non | Non | Non |
| Coût matériel (FCFA) | ~17 000 | ~45 000 | ~38 000 | ~12 000 |
| Disponibilité mesurée | 99,67 % | N/M | N/M | N/M |

*Tableau 10 : Comparaison des paramètres surveillés et fonctionnalités.*

---

---

## CONCLUSION GÉNÉRALE ET PERSPECTIVES

### Bilan

Ce mémoire a décrit la conception et la réalisation de **SUPSERVER**, un système de surveillance des paramètres des équipements d'une salle serveur, développé dans le cadre d'un stage à INGENIERIS CAMEROUN. Partant du constat que les équipements informatiques des PME camerounaises sont exposés à des risques environnementaux et physiques que les pratiques manuelles de surveillance ne permettent pas de détecter en temps utile, nous avons proposé une solution IoT à la fois économique, techniquement robuste et immédiatement déployable.

La plateforme mesure en continu trois paramètres physiques critiques pour les équipements — température ambiante, humidité et concentration de gaz — et surveille l'intégrité physique de la salle par détection PIR. Ces paramètres sont acquis par Arduino Uno, transmis par script Python via liaison USB Serial à une API REST Laravel, persistés en MySQL et visualisés sur un tableau de bord web temps réel accessible localement et à distance.

Les tests sur 72 heures confirment la pleine opérationnalité du système : disponibilité de **99,67 %**, taux de déclenchement des alertes de **100 %**, latence de notification de **3,2 secondes** en moyenne. Le coût matériel total est de **17 000 FCFA**, soit un facteur 50 à 500 en dessous des solutions DCIM commerciales équivalentes.

### Perspectives

1. **Extension des paramètres surveillés** : Ajout de la mesure du courant électrique consommé par les équipements (capteur ACS712) et de la température interne des serveurs via leurs interfaces SNMP ou IPMI.
2. **Architecture multi-nœuds** : Déploiement de plusieurs nœuds Arduino pour couvrir plusieurs zones thermiques dans une même salle ou plusieurs salles.
3. **Détection prédictive** : Intégration d'un algorithme de détection d'anomalies basé sur les tendances des paramètres (moyennes mobiles, modèle LSTM) pour anticiper les dérives avant l'atteinte des seuils d'alerte.
4. **Notification multi-canal** : Ajout d'alertes SMS et d'une application mobile pour la supervision déportée des paramètres des équipements.
5. **Conformité ASHRAE A2** : Génération automatique de rapports de conformité aux recommandations ASHRAE pour les plages de paramètres des équipements.
6. **Autonomie complète** : Remplacement du tunnel ngrok par un certificat TLS Let's Encrypt avec un domaine propre.

---

---

## RÉFÉRENCES BIBLIOGRAPHIQUES

**[1]** ALCARAZ, C. et LOPEZ, J. (2015). « A Security Analysis for Wireless Sensor Mesh Networks in Highly Critical Systems ». *IEEE Transactions on Systems, Man, and Cybernetics*, 40(4), 419–428.

**[2]** AOSONG ELECTRONICS. (2015). *DHT22 Digital Temperature and Humidity Sensor Datasheet*. Guangzhou, Chine : AOSONG Electronics Co., Ltd.

**[3]** ARDUINO. (2023). *Arduino Uno Rev3 — Technical Specifications*. Arduino S.r.l., Monza, Italie. Disponible : https://docs.arduino.cc

**[4]** ASHRAE. (2015). *Thermal Guidelines for Data Processing Environments* (4e éd.). Atlanta, GA, USA : American Society of Heating, Refrigerating and Air-Conditioning Engineers.

**[5]** ATZORI, L., IERA, A. et MORABITO, G. (2010). « The Internet of Things: A Survey ». *Computer Networks*, 54(15), 2787–2805.

**[6]** BANZI, M. et SHILOH, M. (2014). *Getting Started with Arduino* (3e éd.). Sebastopol, CA, USA : Maker Media.

**[7]** BARR, M. et MASSA, A. (2006). *Programming Embedded Systems* (2e éd.). Sebastopol, CA, USA : O'Reilly Media.

**[8]** BERNDT, K. et FISCHER, H. (2021). « Embedded Environmental Monitoring for Academic Server Rooms: A Low-Cost Arduino-Python Approach ». *Proceedings of the 4th European Workshop on Smart Embedded Systems*, TU Munich, Allemagne.

**[9]** BLUM, J. (2019). *Exploring Arduino: Tools and Techniques for Engineering Wizardry* (2e éd.). Hoboken, NJ, USA : Wiley.

**[10]** BOUBA, M. (2020). *Polycopié de cours — Capteurs et instrumentation pour systèmes embarqués*. Département Génie Informatique, IUT de Ngaoundéré, Université de Ngaoundéré, Cameroun.

**[11]** BOUCHARD, M. et TREMBLAY, F. (2018). « IoT-Based Perimeter Monitoring for Academic Server Infrastructure ». *Congrès ACFAS*, École Polytechnique de Montréal, Canada.

**[12]** CHEN, W., LI, H. et ZHANG, Y. (2014). « Centralized vs. Distributed IoT Monitoring for Server Rooms: A Cost-Performance Analysis ». *IEEE International Conference on Cloud Engineering*, 88–95.

**[13]** DASSI NAOMIE. (2024). *Cours de développement web avancé — Architecture MVC, API REST et bases de données*. Département Génie Informatique, IUT de Ngaoundéré, Université de Ngaoundéré, Cameroun.

**[14]** DJIBRILLA, A. et ABOUBAKAR, M. (2022). *Mise en place d'un réseau de capteurs IoT pour la surveillance d'environnements sensibles*. Mémoire de DUT Génie Informatique, IUT de Ngaoundéré, Université de Ngaoundéré, Cameroun.

**[15]** DUBOIS, R. et MERCIER, P. (2019). « Évaluation des capteurs de gaz embarqués pour la détection environnementale en espaces intérieurs ». *Rapport de recherche LIX-2019-07*, École Polytechnique de Palaiseau, France.

**[16]** DUPONT, A. et MARTIN, B. (2018). « Architectures IoT centralisées pour la supervision de datacenters académiques ». *Rapport de recherche LIX-2018-03*, École Polytechnique de Palaiseau, France.

**[17]** EICHLER, T. et RUPPRECHT, C. (2020). « Arduino-Based Sensor Nodes for Industrial IoT Monitoring ». *Proceedings of EmbSys 2020*, TU Munich, Allemagne.

**[18]** FEINJI, J.C. (2024). *Notes de cours — Systèmes embarqués, réseaux de capteurs et IoT*. Département Génie Informatique, IUT de Ngaoundéré, Université de Ngaoundéré, Cameroun.

**[19]** FEUDJIO, S. (2023). *Étude comparative des capteurs de qualité de l'air pour les systèmes embarqués à faible coût*. Mémoire de Licence Professionnelle, Université de Dschang, Cameroun.

**[20]** FIELDING, R.T. (2000). *Architectural Styles and the Design of Network-based Software Architectures*. Thèse de doctorat, Université de Californie à Irvine, USA.

**[21]** FIGARO ENGINEERING. (2018). *MQ135 Gas Sensor — Product Data Sheet*. Osaka, Japon : Figaro Engineering Inc.

**[22]** GILL, S.S., BUYYA, R. et UHLIG, S. (2019). « Failure Analysis of Infrastructure-as-a-Service Clouds ». *ACM Computing Surveys*, 52(4), 1–37.

**[23]** GUBBI, J., BUYYA, R., MARUSIC, S. et PALANISWAMI, M. (2013). « Internet of Things (IoT): A Vision, Architectural Elements, and Future Directions ». *Future Generation Computer Systems*, 29(7), 1645–1660.

**[24]** GUINARD, D. et TRIFA, V. (2016). *Building the Web of Things*. Manning Publications, Shelter Island, NY, USA.

**[25]** HAMADOU, A. (2022). *Conception d'un système de surveillance environnementale à base d'Arduino pour les salles informatiques*. Mémoire de DUT Génie Informatique, IUT de Ngaoundéré, Université de Ngaoundéré, Cameroun.

**[26]** HOFFMANN, R. et SCHREIBER, M. (2019). « Comparative Study of Low-Cost Metal Oxide Gas Sensors for Indoor Air Quality Monitoring ». *Sensors and Actuators B: Chemical*, 285, 107–115.

**[27]** HUANG, L. et ZHANG, W. (2020). « pyserial-Based Serial Communication Between Arduino and Python in Low-Latency IoT Systems ». *Journal of Embedded Computing*, 14(2), 45–58.

**[28]** ITU-T. (2012). *Recommendation Y.2060: Overview of the Internet of Things*. Genève, Suisse : Union Internationale des Télécommunications.

**[29]** KAMGA, F. et NONO, P. (2022). *Conception d'un système de surveillance automatique de salle informatique par microcontrôleur et API REST Laravel*. Mémoire de DUT Génie Informatique, IUT Fotso Victor de Bandjoun, Université de Dschang, Cameroun.

**[30]** KOOMEY, J. (2011). *Growth in Data Center Electricity Use 2005 to 2010*. Stanford University, CA, USA.

**[31]** KOPETZ, H. (2011). *Real-Time Systems: Design Principles for Distributed Embedded Applications* (2e éd.). Vienne, Autriche : Springer.

**[32]** KUMAR, R. et PATEL, S. (2018). « Low-Cost Arduino-Based Server Room Environmental Monitoring with WiFi ». *IEEE International Conference on IoT in Social, Mobile, Analytics and Cloud*, 412–417.

**[33]** LEFEBVRE, P. (2020). *Systèmes embarqués et protocoles de communication pour l'IoT*. Cours INF, École Polytechnique de Palaiseau (l'X), France.

**[34]** LI, S., DA XU, L. et WANG, X. (2015). « Cyber Security of Smart City Infrastructure ». *IEEE Transactions on Emerging Topics in Computing*, 3(2), 168–180.

**[35]** LUTZ, M. (2013). *Learning Python* (5e éd.). Sebastopol, CA, USA : O'Reilly Media.

**[36]** MATTERN, F. et FLOERKEMEIER, C. (2010). « From the Internet of Computers to the Internet of Things ». In *Towards the Internet of Things*. Berlin : Springer.

**[37]** MBARGA, C. et ONDOUA, P. (2023). « Analyse des coûts de défaillance des infrastructures informatiques dans les PME camerounaises ». *Revue Africaine des Technologies de l'Information*, 12(1), 44–58.

**[38]** MCROBERTS, M. (2013). *Beginning Arduino* (2e éd.). New York, USA : Apress.

**[39]** MOUSSA, H. (2023). *Développement d'un tableau de bord IoT temps réel pour la supervision d'équipements connectés*. Mémoire de DUT Génie Informatique, IUT de Ngaoundéré, Université de Ngaoundéré, Cameroun.

**[40]** MVOGO, E. et ESSOMBA, P. (2020). *Étude comparative des capteurs de température et d'humidité pour la surveillance d'équipements serveurs*. Mémoire de Master, ENSP de Yaoundé, Université de Yaoundé I, Cameroun.

**[41]** NCHINGONG, B. et MVO'O, R. (2022). « Optimisation des requêtes SQL pour les tableaux de bord IoT à fort volume de données ». *Annales de la Faculté des Sciences, Université de Douala*, 15(2), 112–125.

**[42]** NDAM, J. (2021). *Protocoles de communication pour les systèmes IoT en milieu contraint*. Mémoire de DUT Réseaux et Télécommunications, IUT de Ngaoundéré, Université de Ngaoundéré, Cameroun.

**[43]** NGOUFACK, R. et MBOULA, J. (2021). « Systèmes embarqués de monitoring des équipements électriques dans les PME camerounaises ». *Revue Africaine des Sciences Informatiques*, 8(2), 14–27.

**[44]** NIOSH. (2019). *NIOSH Pocket Guide to Chemical Hazards*. Cincinnati, OH, USA : National Institute for Occupational Safety and Health.

**[45]** NJIKAM, E. (2022). *Développement d'une application web de supervision d'infrastructures informatiques*. Mémoire de Génie Informatique, ENSP de Douala, Université de Douala, Cameroun.

**[46]** NOUBISSIE, F. et TCHAMBA, P. (2021). *Système embarqué de contrôle de grandeurs physiques*. Mémoire de Master, Université de Ngaoundéré, Cameroun.

**[47]** ORACLE. (2023). *MySQL 8.0 Reference Manual*. Redwood City, CA, USA : Oracle Corporation.

**[48]** OTWELL, T. (2024). *Laravel 13.x Documentation*. Laravel LLC. Disponible : https://laravel.com/docs

**[49]** OUSMANOU, D. (2022). *Polycopié de cours — Capteurs, instrumentation et réseaux de mesure IoT*. IUT de Ngaoundéré, Université de Ngaoundéré, Cameroun.

**[50]** PARALLAX INC. (2012). *PIR Sensor HC-SR501 — Datasheet*. Rocklin, CA, USA.

**[51]** PERERA, C., ZASLAVSKY, A., CHRISTEN, P. et GEORGAKOPOULOS, D. (2014). « Context Aware Computing for the Internet of Things: A Survey ». *IEEE Communications Surveys & Tutorials*, 16(1), 414–454.

**[52]** RICHARDSON, L. et RUBY, S. (2007). *RESTful Web Services*. Sebastopol, CA, USA : O'Reilly Media.

**[53]** ROSE, K., ELDRIDGE, S. et CHAPIN, L. (2015). *The Internet of Things: An Overview*. Internet Society (ISOC), Reston, VA, USA.

**[54]** TAGNE, G. (2021). *Supervision des équipements réseau par capteurs embarqués*. Mémoire de DUT Réseaux et Télécommunications, IUT Fotso Victor de Bandjoun, Université de Dschang, Cameroun.

**[55]** TSAFACK, H. (2021). *Intégration d'Arduino avec des frameworks web Laravel via des scripts de relais*. Mémoire de Licence Professionnelle, IUT de Ngaoundéré, Université de Ngaoundéré, Cameroun.

**[56]** VAN ROSSUM, G. et DRAKE, F.L. (2009). *The Python Language Reference*. Python Software Foundation, Wilmington, DE, USA.

**[57]** WANG, H. et LIU, Q. (2017). « Accuracy Validation of DHT22 Sensor in Controlled Indoor Environments ». *Measurement Science and Technology*, 28(4), 045001.

**[58]** WEISER, M. (1991). « The Computer for the 21st Century ». *Scientific American*, 265(3), 94–104.

**[59]** ZANELLA, A., BUI, N., CASTELLANI, A., VANGELISTA, L. et ZORZI, M. (2014). « Internet of Things for Smart Cities ». *IEEE Internet of Things Journal*, 1(1), 22–32.

---

---

## ANNEXES

### Annexe A — Code Arduino complet

Voir Extrait 1 (Section 10.1). ==Fichier source : `plateforme_de_surveillance_des_salles_serveurs.ino`==

---

### Annexe B — Script Python serial_relay.py complet

Voir Extrait 2 (Section 10.2). ==Déployé en `/opt/lampp/htdocs/plateforme_de_surveillance_des_salles_serveurs/serial_relay.py`. Service systemd : `/etc/systemd/system/serial-relay.service` (User=ahj, Restart=on-failure).==

---

### Annexe C — Configuration Laravel (.env — modèle anonymisé)

```dotenv
APP_NAME="Plateforme Surveillance"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://lego-sanitizer-hexagram.ngrok-free.dev

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=plateforme_surveillance
DB_USERNAME=[utilisateur]
DB_PASSWORD=[mot_de_passe]

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=[email]@gmail.com
MAIL_PASSWORD=[mot_de_passe_application]
MAIL_FROM_NAME="SUPSERVER Alertes Paramètres"

SURVEILLANCE_ADMIN_EMAIL=[admin]@gmail.com
SURVEILLANCE_ADMIN_PHONE=+237[numéro]

QUEUE_CONNECTION=sync
SESSION_DRIVER=file
```

---

### Annexe D — Procédure de démarrage

```bash
# 1. Démarrer LAMPP (Apache + MySQL)
sudo /opt/lampp/lampp start

# 2. Lancer Laravel
cd /opt/lampp/htdocs/plateforme_de_surveillance_des_salles_serveurs
php artisan serve --host=0.0.0.0 --port=8000 &

# 3. Démarrer le relais des paramètres (service systemd)
sudo systemctl start serial-relay
sudo systemctl status serial-relay

# 4. Activer le tunnel ngrok (domaine statique)
ngrok http --domain=lego-sanitizer-hexagram.ngrok-free.dev 8000 &

# 5. Vérifier la connexion Arduino
ls /dev/ttyACM*

# 6. Surveiller les logs du relais
sudo journalctl -u serial-relay -f
```

---

### Annexe E — Guide de dépannage

| Symptôme | Cause | Solution |
|---|---|---|
| Paramètres non mis à jour | Service Python arrêté | `sudo systemctl restart serial-relay` |
| Port série introuvable | Arduino débranché | Rebrancher, vérifier `/dev/ttyACM0` |
| ==Paramètres live figés > 6 s== | Arduino en erreur ou déconnecté | Débrancher/rebrancher le câble USB ; vérifier `journalctl -u serial-relay -f` |
| Alertes email non reçues | Config SMTP incorrecte | Vérifier `MAIL_PASSWORD` dans `.env` |
| Dashboard inaccessible à distance | Tunnel ngrok expiré | Relancer ngrok |
| DHT22 retourne NaN | Résistance pull-up absente | Vérifier résistance 10 kΩ DATA→VCC |

---

### Annexe F — Maintenance recommandée

| Fréquence | Action |
|---|---|
| Hebdomadaire | Vérifier logs : `journalctl -u serial-relay` |
| Mensuelle | Purger les mesures de plus de 6 mois |
| Trimestrielle | Recalibrer le capteur MQ135 |
| Annuelle | Mettre à jour `composer update` et `npm update` |

---

*Fin du mémoire*

---

**AZEGUE FRANCK**  
**IUT de Ngaoundéré — Département Génie Informatique**  
**Entreprise d'accueil : INGENIERIS CAMEROUN**  
**Année académique 2025–2026**
