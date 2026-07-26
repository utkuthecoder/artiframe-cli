<div align="center">

<img src="https://raw.githubusercontent.com/utkuthecoder/artiframe-cli/refs/heads/main/assets/banner.jpg" alt="ArtiFrame - Open Sourced PHP Framework" width="100%" />

<br />

**Zero-dependency · Native PHP · Multilingual CLI**

<br />

[![npm version](https://img.shields.io/npm/v/@artilingo/artiframe-cli?color=00c88c&style=flat-square)](https://www.npmjs.com/package/@artilingo/artiframe-cli)
[![npm downloads](https://img.shields.io/npm/dm/@artilingo/artiframe-cli?color=58a6ff&style=flat-square)](https://www.npmjs.com/package/@artilingo/artiframe-cli)
[![License: AGPL-3.0](https://img.shields.io/badge/License-AGPL--3.0-bc8cff?style=flat-square)](https://www.gnu.org/licenses/agpl-3.0)
[![PHP: >=8.1](https://img.shields.io/badge/PHP-%3E%3D8.1-777bb4?style=flat-square&logo=php&logoColor=white)](https://www.php.net)

</div>

---

<div align="center">

[🇹🇷 Türkçe](#-türkçe) · [🇬🇧 English](#-english) · [🇩🇪 Deutsch](#-deutsch) · [🇫🇷 Français](#-français) · [🇪🇸 Español](#-español)

</div>

---

## 🇹🇷 Türkçe

**ArtiFrame**, sıfır dış bağımlılık felsefesiyle inşa edilmiş, native PHP için modern bir web geliştirme çerçevesidir. Composer paket karmaşasından arınmış, güvenlik öncelikli ve CLI güdümlü bir ekosistem sunar.

### Özellikler

- 🚫 **Sıfır Bağımlılık** — Composer paketlerine ihtiyaç yok, her satır kendi kodunuz
- 🛡️ **Güvenlik Önce** — XSS, CSRF, SQL Injection koruması varsayılan olarak yerleşik
- ⚡ **Hızlı Kurulum** — Tek komutla eksiksiz proje iskeleti
- 🌍 **5 Dil Desteği** — TR · EN · DE · FR · ES
- 📐 **Katı Kural Seti** — `data-js` mimarisi, iki bootstrapper modeli, tutarlı dizin yapısı

### Kurulum

> PHP 8.1 veya üzeri gereklidir.

```bash
npm install -g @artilingo/artiframe-cli
```

### Kullanım

```bash
# İnteraktif kabuk açar
artiframe

# Yeni proje oluştur
artiframe> new benim-projem

# View oluştur
artiframe> make:view admin/kullanicilar.php

# API endpoint oluştur
artiframe> make:api standart api/auth/giris.php

# Sürüm güncelle
artiframe> version upgrade minor
```

---

## 🇬🇧 English

**ArtiFrame** is a modern web development framework for native PHP, built on a zero external dependency philosophy. It offers a security-first, CLI-driven ecosystem free from Composer package chaos.

### Features

- 🚫 **Zero Dependencies** — No Composer packages needed, every line of code is yours
- 🛡️ **Security First** — XSS, CSRF, SQL Injection protection built-in by default
- ⚡ **Quick Setup** — Complete project skeleton with a single command
- 🌍 **5 Language Support** — TR · EN · DE · FR · ES
- 📐 **Strict Ruleset** — `data-js` architecture, dual bootstrapper model, consistent directory structure

### Installation

> Requires PHP 8.1 or higher.

```bash
npm install -g @artilingo/artiframe-cli
```

### Usage

```bash
# Opens the interactive shell
artiframe

# Create a new project
artiframe> new my-project

# Generate a view
artiframe> make:view admin/users.php

# Generate an API endpoint
artiframe> make:api standart api/auth/login.php

# Update version
artiframe> version upgrade minor
```

---

## 🇩🇪 Deutsch

**ArtiFrame** ist ein modernes Web-Entwicklungs-Framework für natives PHP, das auf einer Philosophie ohne externe Abhängigkeiten aufgebaut ist. Es bietet ein sicherheitsorientiertes, CLI-gesteuertes Ökosystem, frei von Composer-Paketchaos.

### Funktionen

- 🚫 **Null Abhängigkeiten** — Keine Composer-Pakete erforderlich, jede Codezeile gehört Ihnen
- 🛡️ **Sicherheit zuerst** — XSS, CSRF, SQL-Injection-Schutz standardmäßig integriert
- ⚡ **Schnelle Einrichtung** — Vollständiges Projektskelett mit einem einzigen Befehl
- 🌍 **5 Sprachunterstützung** — TR · EN · DE · FR · ES
- 📐 **Striktes Regelwerk** — `data-js`-Architektur, duales Bootstrapper-Modell, konsistente Verzeichnisstruktur

### Installation

> Erfordert PHP 8.1 oder höher.

```bash
npm install -g @artilingo/artiframe-cli
```

### Verwendung

```bash
# Öffnet die interaktive Shell
artiframe

# Neues Projekt erstellen
artiframe> new mein-projekt

# View erstellen
artiframe> make:view admin/benutzer.php

# API-Endpunkt erstellen
artiframe> make:api standart api/auth/anmelden.php

# Version aktualisieren
artiframe> version upgrade minor
```

---

## 🇫🇷 Français

**ArtiFrame** est un framework de développement web moderne pour PHP natif, construit sur une philosophie zéro dépendance externe. Il offre un écosystème axé sur la sécurité et piloté par CLI, sans le chaos des paquets Composer.

### Fonctionnalités

- 🚫 **Zéro Dépendance** — Aucun paquet Composer nécessaire, chaque ligne de code vous appartient
- 🛡️ **La Sécurité d'Abord** — Protection XSS, CSRF, injection SQL intégrée par défaut
- ⚡ **Configuration Rapide** — Squelette de projet complet en une seule commande
- 🌍 **Support 5 Langues** — TR · EN · DE · FR · ES
- 📐 **Règles Strictes** — Architecture `data-js`, modèle double bootstrapper, structure de répertoires cohérente

### Installation

> Nécessite PHP 8.1 ou supérieur.

```bash
npm install -g @artilingo/artiframe-cli
```

### Utilisation

```bash
# Ouvre le shell interactif
artiframe

# Créer un nouveau projet
artiframe> new mon-projet

# Générer une vue
artiframe> make:view admin/utilisateurs.php

# Générer un endpoint API
artiframe> make:api standart api/auth/connexion.php

# Mettre à jour la version
artiframe> version upgrade minor
```

---

## 🇪🇸 Español

**ArtiFrame** es un framework de desarrollo web moderno para PHP nativo, construido sobre una filosofía de cero dependencias externas. Ofrece un ecosistema orientado a la seguridad y guiado por CLI, libre del caos de paquetes Composer.

### Características

- 🚫 **Cero Dependencias** — Sin paquetes Composer, cada línea de código es tuya
- 🛡️ **Seguridad Primero** — Protección XSS, CSRF, inyección SQL integrada por defecto
- ⚡ **Configuración Rápida** — Esqueleto de proyecto completo con un solo comando
- 🌍 **Soporte 5 Idiomas** — TR · EN · DE · FR · ES
- 📐 **Reglas Estrictas** — Arquitectura `data-js`, modelo dual de bootstrapper, estructura de directorios consistente

### Instalación

> Requiere PHP 8.1 o superior.

```bash
npm install -g @artilingo/artiframe-cli
```

### Uso

```bash
# Abre el shell interactivo
artiframe

# Crear un nuevo proyecto
artiframe> new mi-proyecto

# Generar una vista
artiframe> make:view admin/usuarios.php

# Generar un endpoint API
artiframe> make:api standart api/auth/login.php

# Actualizar la versión
artiframe> version upgrade minor
```

---

<div align="center">

**[artiframe.artilingo.com](https://artiframe.artilingo.com)** · **[npmjs.com/package/@artilingo/artiframe-cli](https://www.npmjs.com/package/@artilingo/artiframe-cli)** · **[GitHub](https://github.com/utkuthecoder/artiframe-cli)**

© Artilingo — Licensed under [AGPLv3](https://www.gnu.org/licenses/agpl-3.0)

</div>
