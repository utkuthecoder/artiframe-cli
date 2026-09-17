<div align="center">

<img src="https://raw.githubusercontent.com/utkuthecoder/artiframe-cli/refs/heads/main/assets/banner.jpg" alt="ArtiFrame - Open Sourced PHP Framework" width="100%" />

<br />

**Zero-dependency · Native PHP · DevOps Studio · DB Designer · Live Server**

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

**ArtiFrame 3.0**, native PHP için sadece bir framework değil, eksiksiz bir **geliştirme ekosistemidir**. Sıfır dış bağımlılık felsefesine sadık kalarak, artık içinde DevOps Studio (GUI), DB Studio (Global ER Tasarımcısı) ve Server Studio (Canlı Log Monitörü) gibi muazzam arayüzler barındırır.

### Yeni v3.0 Özellikleri (The Studio Update)

- 🎨 **DevOps Studio** — `artiframe devops` komutuyla açılan yerleşik masaüstü GUI. API Testleri (Inspector), Cron Tasarımcısı, Supervisor Yönetimi ve Env Optimize işlemlerini görsel olarak yönetin!
- 🗄️ **DB Studio** — Sürükle bırak ile veritabanı şemalarınızı tasarlayın ve `db:onproject` ile doğrudan projenize entegre edin.
- 🌐 **Server Studio** — `artiframe serve` ile projenizi anında yayına alın ve HTTP isteklerini canlı log (Gateway logger) üzerinden analiz edin.
- 📦 **Workspace Mimarisi** — Projeleriniz artık global ve izole bir workspace dizininde (`C:\ArtiFrame` veya `~/ArtiFrame`) güvenle yaşar.
- 🚀 **Semantik Sürüm ve Eklenti Yönetimi** — `version upgrade` ve `add <package>` gibi yerleşik paket/sürüm yöneticileriyle Composer olmadan hayatınıza devam edin.
- 🚫 **Sıfır Bağımlılık** — Güvenlik (XSS, CSRF) ve mimari altyapı hala native PHP.

### Kurulum

> Node.js ve PHP 8.1+ gereklidir.

```bash
npm install -g @artilingo/artiframe-cli
```
*(Kurulum sonrasında işletim sisteminize uygun global Workspace dizini otomatik olarak oluşturulacaktır.)*

### Kullanım

```bash
# Grafik Arayüzlü Kontrol Merkezini Aç (YENİ!)
artiframe devops

# İnteraktif CLI Kabuğunu Aç
artiframe

# Yeni proje oluştur
artiframe> new benim-projem

# Veritabanını SQL olarak dışa aktar (Veri + Şema)
artiframe> db:export within

# View veya API oluştur
artiframe> make:view admin/kullanicilar.php
artiframe> make:api standart api/auth/giris.php
```

---

## 🇬🇧 English

**ArtiFrame 3.0** is no longer just a framework for native PHP—it's a complete **development ecosystem**. Staying true to its zero external dependency philosophy, it now ships with massive graphical interfaces including DevOps Studio (GUI), DB Studio (Global ER Designer), and Server Studio (Live Log Monitor).

### New in v3.0 (The Studio Update)

- 🎨 **DevOps Studio** — Built-in desktop GUI launched via `artiframe devops`. Manage API Inspections, Cron jobs, Supervisor configs, and Env optimization visually!
- 🗄️ **DB Studio** — Drag and drop database schema designer. Sync schemas directly into your active project via `db:onproject`.
- 🌐 **Server Studio** — Launch your project instantly with `artiframe serve` and monitor HTTP requests live via the gateway logger.
- 📦 **Global Workspace** — Projects are now securely sandboxed in a global workspace directory (`C:\ArtiFrame` or `~/ArtiFrame`).
- 🚀 **Version & Package Manager** — Built-in `version upgrade` and `add <package>` commands to survive without Composer.
- 🚫 **Zero Dependencies** — Security (XSS, CSRF) and architectural foundations are still purely native PHP.

### Installation

> Requires Node.js and PHP 8.1+.

```bash
npm install -g @artilingo/artiframe-cli
```
*(Your global Workspace directory will be automatically generated post-installation.)*

### Usage

```bash
# Launch the Graphical Control Center (NEW!)
artiframe devops

# Open interactive CLI shell
artiframe

# Create a new project
artiframe> new my-project

# Export database to SQL (Data + Schema)
artiframe> db:export within

# Generate a view or API
artiframe> make:view admin/users.php
artiframe> make:api standart api/auth/login.php
```

---

## 🇩🇪 Deutsch

**ArtiFrame 3.0** ist nicht mehr nur ein Framework für natives PHP – es ist ein komplettes **Entwicklungsökosystem**. Es bleibt seiner Philosophie (Null externe Abhängigkeiten) treu und wird nun mit grafischen Schnittstellen wie DevOps Studio (GUI), DB Studio (Global ER Designer) und Server Studio (Live Log Monitor) ausgeliefert.

### Neu in v3.0 (Das Studio-Update)

- 🎨 **DevOps Studio** — Integrierte Desktop-GUI, die über `artiframe devops` gestartet wird (API-Inspektor, Cron-Designer, Env-Optimierung).
- 🗄️ **DB Studio** — Drag & Drop Datenbank-Schema-Designer.
- 🌐 **Server Studio** — Überwachen Sie HTTP-Anfragen live via `artiframe serve`.
- 📦 **Global Workspace** — Projekte leben nun sicher in einem globalen Verzeichnis (`C:\ArtiFrame` oder `~/ArtiFrame`).

### Installation & Verwendung
```bash
npm install -g @artilingo/artiframe-cli

# GUI Starten
artiframe devops
```

---

## 🇫🇷 Français

**ArtiFrame 3.0** n'est plus seulement un framework, c'est un **écosystème de développement** complet. Il est désormais livré avec des interfaces graphiques massives : DevOps Studio (GUI), DB Studio (Global ER Designer) et Server Studio (Live Log Monitor).

### Nouveautés v3.0 (La mise à jour Studio)

- 🎨 **DevOps Studio** — GUI bureau intégrée lancée via `artiframe devops`.
- 🗄️ **DB Studio** — Concepteur de schéma de base de données par glisser-déposer.
- 🌐 **Server Studio** — Serveur HTTP intégré avec journalisation en direct (`artiframe serve`).
- 📦 **Espace de Travail** — Les projets sont désormais isolés dans un répertoire global (`C:\ArtiFrame` ou `~/ArtiFrame`).

### Installation & Utilisation
```bash
npm install -g @artilingo/artiframe-cli

# Lancer la GUI
artiframe devops
```

---

## 🇪🇸 Español

**ArtiFrame 3.0** ya no es solo un framework, es un **ecosistema de desarrollo** completo. Ahora incluye interfaces gráficas masivas: DevOps Studio (GUI), DB Studio (Global ER Designer) y Server Studio (Live Log Monitor).

### Novedades en v3.0 (La actualización Studio)

- 🎨 **DevOps Studio** — GUI de escritorio integrada lanzada mediante `artiframe devops`.
- 🗄️ **DB Studio** — Diseñador visual de esquemas de bases de datos.
- 🌐 **Server Studio** — Servidor HTTP integrado con monitor en vivo (`artiframe serve`).
- 📦 **Espacio de Trabajo** — Los proyectos ahora residen en un directorio global (`C:\ArtiFrame` o `~/ArtiFrame`).

### Instalación y Uso
```bash
npm install -g @artilingo/artiframe-cli

# Iniciar la GUI
artiframe devops
```

---

<div align="center">

**[artiframe.artilingo.com](https://artiframe.artilingo.com)** · **[npmjs.com/package/@artilingo/artiframe-cli](https://www.npmjs.com/package/@artilingo/artiframe-cli)** · **[GitHub](https://github.com/utkuthecoder/artiframe-cli)**

© Artilingo — Licensed under [AGPLv3](https://www.gnu.org/licenses/agpl-3.0)

</div>
