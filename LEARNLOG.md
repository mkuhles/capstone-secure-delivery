# Learn Log

### 2025-12-16 (W1D1 - Tue)

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

### 2025-12-18 (W1D3 - Thur)

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
- a very insecure legacy app (lagacy-lab) and an attacker
- fix multiple vulnarabilities at legacy-lab:
    - dangerous url-parameters
    - no admin check on admin page
    - user names and passwords in code