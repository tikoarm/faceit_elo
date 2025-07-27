<div id="top">

<!-- HEADER STYLE: CLASSIC -->
<div align="center">


# FACEIT Match Tracker


<em>Elevate Your Game, Dominate Every Match</em>

<!-- BADGES -->
<img src="https://img.shields.io/github/last-commit/tikoarm/faceit_elo?style=flat&logo=git&logoColor=white&color=0080ff" alt="last-commit">
<img src="https://img.shields.io/badge/Top%20Language-Python-3776AB?style=flat&logo=Python&logoColor=white" alt="repo-top-language">
<img src="https://img.shields.io/badge/Second%20Language-PHP-777BB4?style=flat&logo=PHP&logoColor=white" alt="second-language">
<img src="https://img.shields.io/github/languages/count/tikoarm/faceit_elo?style=flat&color=0080ff" alt="repo-language-count">


<em>Built with the technologies:</em>

<img src="https://img.shields.io/badge/Flask-000000.svg?style=flat&logo=Flask&logoColor=white" alt="Flask">
<img src="https://img.shields.io/badge/JavaScript-F7DF1E.svg?style=flat&logo=JavaScript&logoColor=black" alt="JavaScript">
<img src="https://img.shields.io/badge/Docker-2496ED.svg?style=flat&logo=Docker&logoColor=white" alt="Docker">
<img src="https://img.shields.io/badge/Python-3776AB.svg?style=flat&logo=Python&logoColor=white" alt="Python">
<img src="https://img.shields.io/badge/GitHub%20Actions-2088FF.svg?style=flat&logo=GitHub-Actions&logoColor=white" alt="GitHub%20Actions">
<img src="https://img.shields.io/badge/PHP-777BB4.svg?style=flat&logo=PHP&logoColor=white" alt="PHP">

</div>
<br>

---

## Table of Contents

- [Overview](#overview)
- [Documentation](#documentation)
- [Getting Started](#getting-started)
    - [Prerequisites](#prerequisites)
    - [Installation](#installation)
    - [Usage](#usage)

---

## Overview

**FACEIT Match Tracker** is a distributed, Docker-based system that automatically monitors players' FACEIT matches and provides real-time animated visualizations of ELO changes on a web frontend.

**Key Features:**
- 🎮 **Automated Match Tracking:** Sub-servers poll the FACEIT API to detect when a player’s match starts and ends.
- 📊 **ELO Change Animations:** Instantly renders ELO difference animations in the browser via Server-Sent Events.
- 🛠️ **Control Panel:** Web dashboard for managing users, sub-servers, and monitoring system health.
- 🐳 **Containerized Architecture:** Master service, database, sub-servers, and frontend run in isolated Docker containers for easy deployment and horizontal scaling.
- 🔒 **Secure Communication:** Sub-servers authenticate with API keys and IP validation to ensure data integrity.

**Architecture Overview:**
- **Master Service (Python 3.11, Flask):** Receives match events, stores them in MySQL, and pushes updates to the frontend.
- **Sub-Servers (Python asyncio):** Continuously poll FACEIT API and report match results to the master.
- **Web Animation Frontend (PHP, SSE):** Displays live ELO animations in users’ browsers.

---

## Documentation

More detailed information is available at [localhost:8890](http://localhost:8890) or [cs2-faceit.tikoarm.com](http://cs2-faceit.tikoarm.com).

Specifically, you can find:
- Project structure
- Project purpose
- API endpoints for both Master Service and Sub-Servers
- Database schema
- Information about security tokens and keys

---

## Getting Started

### Prerequisites

- Docker & docker-compose
- FACEIT API key
- Telegram Bot Token
- Free network ports (default: 5051, 8082, 8890, 8895)

---

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/tikoarm/faceit_elo.git
   cd faceit_elo
   ```

2. Create and configure environment files:
   ```bash
   cp docker/.env.example docker/.env
   # Fill in all required values in docker/.env
   ```

3. Launch services:
   ```bash
   docker-compose up -d
   ```

4. Import the database dump:
   ```bash
   docker exec -i faceit_db mysql -u root -p${MYSQL_PASSWORD} ${MYSQL_DATABASE} < faceit_db.sql
   ```
   Or use PhpMyAdmin at [localhost:8082](http://localhost:8082).

5. (Optional) Restart services if required:
   ```bash
   docker-compose restart
   ```

---

### Usage

**Access web interfaces:**
- Master Service API:       [localhost:5051](http://localhost:5051)
- PhpMyAdmin:               [localhost:8082](http://localhost:8082)
- Main Web UI (index.php):  [localhost:8890](http://localhost:8890)
- Control Panel (cp/index.php): [localhost:8890/cp/index.php](http://localhost:8890/cp/index.php)
- Web Animations:           [localhost:8895](http://localhost:8895)

---

<div align="left"><a href="#top">⬆ Return</a></div>

---
