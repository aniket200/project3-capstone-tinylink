# TinyLink — Containerized URL Shortener

A production-style, containerized URL shortening application built using **Docker Compose, Nginx, PHP, Java, Redis, and NFS**.

TinyLink allows users to submit long URLs and receive a short code that can be used to redirect to the original URL. The application demonstrates multi-container architecture, reverse proxying, service-to-service communication, persistent storage, and shared NFS-based logging.

---

## 🏗️ Architecture

```text
                         Client / Browser
                                |
                                v
                    +-----------------------+
                    |   Nginx Reverse Proxy |
                    |       Port: 8080      |
                    +-----------+-----------+
                                |
                    +-----------+-----------+
                    |                       |
                    v                       v
             /api/* requests          Web requests
                    |                       |
                    v                       v
            +---------------+       +---------------+
            | Java Backend  |       | PHP Frontend  |
            |    :8080      |       |      :80      |
            +-------+-------+       +-------+-------+
                    |                       |
                    +-----------+-----------+
                                |
                                v
                       +----------------+
                       |     Redis      |
                       |     :6379      |
                       +----------------+
                                |
                                v
                       +----------------+
                       |   NFS Server   |
                       | Persistent     |
                       | Shared Storage |
                       +----------------+
```

### Request Flow

1. Client sends a request to Nginx.
2. Nginx acts as the single public entry point.
3. API requests under `/api/*` are forwarded to the Java backend.
4. The Java backend stores URL mappings in Redis.
5. PHP provides the web interface and handles browser-based redirection.
6. Java and PHP share an access log through NFS.
7. Redis data is persisted using NFS-backed storage.

---

## 🚀 Technologies Used

* **Docker**
* **Docker Compose**
* **Nginx**
* **Java**
* **Maven**
* **PHP**
* **Redis**
* **NFS**
* **Linux**
* **REST API**
* **Reverse Proxy**

---

## 📦 Docker Services

| Service      | Purpose                            | Image                          |
| ------------ | ---------------------------------- | ------------------------------ |
| Nginx        | Reverse proxy / public entry point | `nginx:1.25-alpine`            |
| Java Backend | URL shortening API                 | `aniketbeast007/tinylink-java` |
| PHP Frontend | Web interface                      | `aniketbeast007/tinylink-php`  |
| Redis        | URL mapping storage                | `redis:7-alpine`               |
| NFS Server   | Persistent/shared storage          | `aniketbeast007/tinylink-nfs`  |
| NFS Init     | Initializes NFS directories        | `alpine:3.19`                  |

---

## 🔗 Docker Hub Images

The project images are available on Docker Hub:

* `aniketbeast007/tinylink-java`
* `aniketbeast007/tinylink-php`
* `aniketbeast007/tinylink-nfs`

The project also uses official/third-party base images for Nginx, Redis, and Alpine.

---

## 📁 Project Structure

```text
project3-capstone-tinylink/
│
├── docker-compose.yml
├── .gitignore
├── README.md
│
├── nginx/
│   └── nginx.conf
│
├── java-backend/
│   ├── Dockerfile
│   ├── pom.xml
│   └── src/
│       └── main/
│           └── java/
│               └── edu/
│                   └── kucl/
│                       └── docker/
│                           └── App.java
│
└── php-frontend/
    ├── Dockerfile
    └── src/
        ├── index.php
        ├── submit.php
        ├── redirect.php
        └── logs.php
```

---

## ⚙️ How to Run

### Prerequisites

Make sure the system has:

* Docker
* Docker Compose
* Git

Check Docker:

```bash
docker --version
```

Check Docker Compose:

```bash
docker-compose --version
```

---

## 1. Clone the Repository

```bash
git clone https://github.com/aniket200/project3-capstone-tinylink.git
```

Enter the project directory:

```bash
cd project3-capstone-tinylink
```

---

## 2. Start the Application

```bash
docker-compose up -d
```

To rebuild the application images:

```bash
docker-compose up -d --build
```

---

## 3. Check Container Status

```bash
docker-compose ps
```

All application services should be running.

The NFS initialization container may show:

```text
Exit 0
```

This is expected because it performs initialization and then exits.

---

## 🌐 Access the Application

Open:

```text
http://localhost:8080
```

Nginx is the only service exposed to the host.

The application architecture keeps the Java, PHP, Redis, and NFS services inside the Docker network.

---

## 🔗 API Usage

### Create a Short URL

Send a POST request to:

```text
POST /api/shorten
```

Example:

```bash
curl -X POST \
  -H "Content-Type: application/json" \
  -d '{"url":"https://example.com"}' \
  http://localhost:8080/api/shorten
```

Example response:

```json
{
  "code": "LyEpCX",
  "url": "https://example.com"
}
```

---

### Resolve a Short URL

The API can resolve a short code:

```text
GET /api/resolve?code=LyEpCX
```

---

## 🔀 URL Redirection

Short URLs are handled through the PHP frontend.

Example:

```text
http://localhost:8080/redirect.php?c=LyEpCX
```

The application retrieves the original URL and redirects the user to it.

---

## 📝 Shared NFS Logging

Java and PHP use a shared NFS-backed log:

```text
/var/log/tinylink/access.log
```

Java records URL creation events.

Example:

```text
[java-backend] created short:LyEpCX -> https://example.com
```

PHP records redirect events.

Example:

```text
[php-frontend] redirected short:LyEpCX -> https://example.com
```

The shared log demonstrates communication through common NFS storage between application containers.

---

## 💾 Persistent Redis Storage

Redis data is stored using NFS-backed persistent storage.

This allows URL mappings to survive container recreation.

The project demonstrates:

```text
Application
    ↓
Redis
    ↓
NFS-backed persistent storage
```

---

## 🔍 Useful Docker Commands

### View all containers

```bash
docker-compose ps
```

### View logs

```bash
docker-compose logs
```

### View Java logs

```bash
docker-compose logs java-backend
```

### View PHP logs

```bash
docker-compose logs php-frontend
```

### View Redis logs

```bash
docker-compose logs redis
```

### Stop the application

```bash
docker-compose down
```

### Stop and remove volumes

```bash
docker-compose down -v
```

---

## 🧪 Testing

Check Redis:

```bash
docker-compose exec redis redis-cli ping
```

Expected:

```text
PONG
```

Check the API:

```bash
curl -X POST \
  -H "Content-Type: application/json" \
  -d '{"url":"https://example.com"}' \
  http://localhost:8080/api/shorten
```

Check shared logs:

```bash
curl http://localhost:8080/logs.php
```

---

## 🔐 Security & Networking

Nginx acts as the **single public entry point**.

The architecture is designed so that internal services communicate through the Docker network instead of exposing every service directly to the host.

```text
Internet / Client
       |
       v
    Nginx :8080
       |
       +----> PHP
       |
       +----> Java :8080
                    |
                    v
                  Redis
                    |
                    v
                   NFS
```

---

## 🎯 Project Objectives

This project demonstrates practical knowledge of:

* Containerization
* Docker Compose
* Multi-container application architecture
* Nginx reverse proxy configuration
* Java REST API development
* PHP frontend development
* Redis integration
* NFS persistent storage
* Shared network storage
* Container networking
* Persistent volumes
* Docker image creation and distribution
* Linux administration
* Application troubleshooting

---

## 👨‍💻 Author

**Aniket**

GitHub:

`https://github.com/aniket200`

Docker Hub:

`https://hub.docker.com/u/aniketbeast007`

---

## 📜 License

This project is intended for educational and portfolio purposes.
