# Secure Delivery Capstone (Symfony)

Goal: Build a small Symfony app and evolve it into a secure, automated delivery pipeline (AppSec/DevSecOps practice).

Tech: PHP, Symfony, Git (Docker optional)

There are three web projects in this project:
- `app` and symfony app which shows how it should be in modern days
    - start with `symfony setrve`
    - form W1D3 on you need `symfony server:ca:install` and restart zour browser

- `lab-legacy` a reely basic app with lots of vulnarabilities, which will be fixed at time
    - start with `php -S 127.0.0.1:8081`

- `attacker` scripts with bad habits
    - start with `php -S 127.0.0.1:8082`