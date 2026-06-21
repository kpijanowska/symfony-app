  Aplikacja webowa zbudowana w Symfony 7.4 LTS, udostępniająca część publiczną
  dla czytelników oraz panel administracyjny do zarządzania treścią.

  ## Funkcjonalności

  - Przeglądanie artykułów (lista z paginacją i sortowaniem) oraz kategorii
  - Dodawanie komentarzy przez niezalogowanych użytkowników
  - Panel administracyjny: CRUD artykułów, kategorii i użytkowników
  - Moderacja komentarzy oraz zarządzanie kontami i uprawnieniami
  - Uwierzytelnianie, zmiana hasła i danych administratora

  ## Stos technologiczny

  - PHP 8.x, Symfony 7.4 LTS
  - MySQL 8.3
  - Doctrine ORM (migracje, repozytoria, fixtures)
  - Twig, Bootstrap 5
  - Środowisko uruchomieniowe: Docker / Docker Compose

  ## Wymagania

  - Docker oraz Docker Compose

  ## Instalacja i uruchomienie

  ### 1. Uruchomienie środowiska

  W katalogu głównym repozytorium (z plikiem `docker-compose.yml`) uruchom
  kontenery (Apache, PHP, MySQL, maildev):

  ```bash
  docker compose up -d
  ```

  ### 2. Wejście do kontenera PHP

  Wszystkie polecenia aplikacji wykonujemy wewnątrz kontenera PHP:

  ```bash
  docker compose exec php bash
  cd app
  ```

  ### 3. Instalacja zależności

  ```bash
  composer install
  ```

  ### 4. Inicjalizacja bazy danych

  Utworzenie schematu (migracje) i załadowanie danych startowych (fixtures):

  ```bash
  composer init-app
  ```

  Odpowiednik ręczny:

  ```bash
  php bin/console doctrine:migrations:migrate
  php bin/console doctrine:fixtures:load
  ```

  ## Konta testowe
  Dane do logowania: 
  Administrator: login: admin0@example.com, hasło: admin1234
  Użytkownik: login: user0@example.com, hasło: user1234


  ```bash
  composer static-analysis
  ```
