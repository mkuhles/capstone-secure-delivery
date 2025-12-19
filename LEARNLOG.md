# Learn Log

## 2025-12-16 (W1D1 - Tue)

- **Goal (outcome)**: Set up the capstone repo and get a Symfony app running locally.
- **Plan (max 3)**:
    1. Create repo structure + basic docs (README, glossary, checklists)
    2. Create Symfony skeleton app
    3. Inspect one request in Chrome DevTools (Network tab): status + headers
- **Done when**: I can open the app locally and I can see the request/response details in DevTools.
- **What I shipped today**: Symfony app runs locally at http://127.0.0.1:8000/ and has /health endpoint
    returning JSON  with status 200.

    - HTTP check: Status 200, content-type application/json

- **New term**: Sec-Fetch-Dest


### 2025-12-17 (W1D2 - Wed)

**Goal (outcome):** Implement form login (AuthN) + role-based access control (AuthZ) and make the UX presentable.

**What I shipped today:**
- Generated form login (/login) + logout route and created basic auth flow
- Added `/admin` route protected by ROLE_ADMIN
- Verified behavior:
  - anonymous -> `/admin` redirects to `/login`
  - user -> `/admin` returns 403
  - admin -> `/admin` returns 200
- Added a custom 403 error page template (works in prod mode)
- Started SQLite/Doctrine work:
  - Added/verified SQLite DATABASE_URL
  - Created DB + migrations and a `user` table (username, roles, password)
  - Completed switch from in-memory users to Doctrine entity provider (DB-backed auth)
  - Verified login works with DB users and role-based access still behaves as expected
- Added Symfony UX Twig Component `Menu` (installed `symfony/ux-twig-component`) and rendered it in Twig

**Blockers / fixes:**
- Symfony dev exception page for 403: confirmed custom 403 template renders in prod (APP_ENV=prod, APP_DEBUG=0)
- Twig `component()` was unknown -> fixed by installing `symfony/ux-twig-component`

## 2025-12-18 (W1D3 - Thu)

**Goal (outcome):** 
- Refresh sessions and cookies and understand CSRF
- investigate capstones login for the principals and fix potential issus

**What I learned today:**
- see https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/Cookies
    - cookies with `security` attribute requriers https
    - cookies with `httpOnly` attribute can't be set by js
    - cookies with `path` attribute will only be send in header if requested URL access the path or subdirectories
    - cookie prefixes: never heard before, need more information 

**What I shipped today:**
- Verified session cookie attributes (Secure/HttpOnly/SameSite=Lax) over HTTPS
- Confirmed CSRF token present in Symfony login form
- Built legacy-lab CSRF demo and then fixed it with synchronizer token pattern (403 on missing/invalid token)
- Refactored legacy-lab structure (public/lib/setup/var) and moved state changes (admin_note) to SQLite for persistence
- Note: session cookie is still PHPSESSID (no __Host- prefix yet); plan to enable later under HTTPS

**Repo tags created:**
- `csrf-test` (before CSRF possible in legacy-lab admin POST) and `csrf-fixed` (after fix)


## 2025-12-18 (W1D4 - Fri)

### Goal (outcome)
Demonstrate and fix one reflected XSS in Symfony/Twig, and (bonus) demonstrate and fix one stored XSS in the Legacy lab using output encoding.

### Definitions
- **XSS (Cross-Site Scripting)** is a vulnerability where an application injects attacker-controlled input into a page without proper output encoding, enabling arbitrary JavaScript execution in a victim’s browser under the site’s origin.
- **Reflected XSS** happens when attacker-controlled data from the request (often the URL/query string) is immediately included in the response without proper output encoding, causing JavaScript to execute in the victim’s browser.
- **Stored XSS** occurs when malicious payloads are saved on the server (e.g., in a database) and later rendered without output encoding, causing the script to execute in users’ browsers.

### Proof / Evidence

#### Symfony:

- Route A (unsafe): renders msg with |raw (or equivalent).
- Route B (safe): renders msg normally (auto-escaped).

#### Legacy Lab (bonus)

Add a toggle/switch that flips between raw output and htmlspecialchars(...) for the stored note, then show “executes” vs “renders as text”.

### What I shipped:

#### Symfony

- https://127.0.0.1:8000/xss/unsafe?msg=%3Cscript%3Eprint()%3C/script%3E opens the print dialog
- https://127.0.0.1:8000/xss/safe?msg=%3Cscript%3Eprint()%3C/script%3E echos excapted script tag in message block

#### Legacy Lab

Legacy stored XSS: added central output-encoding wrapper (config toggle), verified unsafe vs safe behavior.

### Take away

Output encoding at the sink is the primary control.
