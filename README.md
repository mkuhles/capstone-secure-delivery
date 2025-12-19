# Secure Delivery Capstone (Symfony)

Goal: Build a small Symfony app and evolve it into a secure, automated delivery pipeline (AppSec/DevSecOps practice).

Tech: PHP, Symfony, Git (Docker optional)

There are three web projects in this project:
- `app` and symfony app which shows how it should be in modern days
    - start with `symfony setrve`
    - form W1D3 on you need `symfony server:ca:install` and restart zour browser

- `lab-legacy` a basic app with lots of vulnarabilities, which will be fixed at time
    - start with `cd lab-legacy/public; php -S 127.0.0.1:8081`
    - setup in terminal `cd legacy-lab; php setup/setup.php`
    - run composer `composer dump-autoload -o`

- `attacker` scripts with bad habits
    - start with `php -S 127.0.0.1:8082`

## Milestone tags

- `csrf-test` — state BEFORE CSRF protection on `POST /admin.php` (CSRF works / attacker.html can change note)
- `csrf-fixed` — state AFTER CSRF token validation (attacker gets 403)