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


## 2025-12-19 (W1D4 - Fri)

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

### React note:

React escapes values rendered as {msg} by default, so user-controlled strings are treated as text.

dangerouslySetInnerHTML bypasses this protection and behaves like Twig |raw, so attacker-controlled HTML/JS can execute if inserted unsafely.

Use it only with trusted content or after sanitizing with an allowlist-based HTML sanitizer (and still prefer not to render raw HTML).

### Take away

Output encoding at the sink is the primary control.


## 2025-12-20 (W1D5 - Sat)

### Goal (outcome)

I can explain SQL Injection (root cause + impact) and I can replace an unsafe string-concatenated SQL query with prepared statements (Legacy Lab). I can also point to a Symfony/Doctrine best-practice example for parameter binding.

### Plan (max 3)
  1. Read the PortSwigger SQLi overview + capture key terms in GLOSSARY.md
  2. Legacy Lab: add a small “user search” endpoint that is intentionally vulnerable (string concatenation), reproduce the issue, then fix it with prepared statements (toggle-based)
  3. Symfony: implement (or locate) a safe search example using Doctrine QueryBuilder with parameters


### What I shipped:
- Legacy Lab: added /search.php demo route with SQLi toggle via config `sqli_protected`
- Legacy Lab: implemented UserRepository::searchByUsername():
  - vulnerable mode uses string concatenation (for demo)
  - protected mode uses prepared statements + parameter binding for `:q`
  - hardened LIMIT handling by clamping and injecting a safe integer (no tainted input)
- Symfony (ORM): `/user/search` using Doctrine QueryBuilder with `:q` + `setParameter()` + `setMaxResults()`
- Symfony (DBAL): `/user/search-dbal` using `executeQuery()` with named parameters (LIMIT injected as clamped int)


### Proof / Evidence
- SQLi payload test `q=%' OR 1=1 --`:
  - Protection OFF: 2 results (unexpectedly returns all users)
  - Protection ON: 0 results (payload treated as literal input)

### Take away
- SQLi happens when untrusted input becomes part of the SQL query structure.
- SQLi is “data becomes SQL code”: string concatenation is the root cause.
- Prepared statements separate query structure from user-controlled values.
- Not every SQL fragment can be parameterized everywhere (e.g. LIMIT); when needed, clamp and inject safe integers.

## 2025-12-20 (W1D6 - Sat)

### Goal (outcome)
- Understand why OWASP Top 10 is an effective “first step” baseline for secure software development (shared language + prioritization + culture shift).
- Explain CORS precisely (same-origin policy, CORS headers, preflight) and identify common misconfigurations.
- Demonstrate a CORS misconfiguration in the Legacy Lab and fix it with an allowlist-based policy.
- Apply a best-practice CORS configuration in the Symfony app (restricted origins, methods, headers; correct credential handling; correct preflight behavior).

### Proof / Evidence

- Links to commits:
  - docs: add OWASP Top 10 + CORS glossary entries
  - legacy: reproduce insecure CORS + add secure allowlist policy
  - symfony: configure CORS safely (paths + env-based origins) + preflight handling
- Test evidence (copy results into the log):
  - curl preflight check (OPTIONS) showing correct Access-Control-Allow-* behavior
  - curl request from allowed origin returns CORS headers; disallowed origin does not
  - (Optional) Minimal browser PoC page demonstrating that a cross-origin read is blocked after the fix

### Definitions

- **OWASP (Open Worldwide Application Security Project)**: A non-profit foundation that supports projects, tools, and guidance to improve software security. 
- **OWASP Top 10**: A standard awareness document listing the most critical web application security risk categories, used as a baseline to align teams on priorities and secure coding practices. 
- **Same-Origin Policy (SOP)**: A browser security rule that restricts scripts from reading responses from a different origin (scheme/host/port) unless explicitly allowed.
- **Origin**: The tuple (scheme, host, port) identifying where a request is made from.
- **CORS (Cross-Origin Resource Sharing)**: An HTTP-header-based mechanism that allows a server to specify which other origins a browser may allow reading resources from; includes optional preflight requests. 
- **Preflight Request**: A browser-sent OPTIONS request that checks whether the server permits the intended cross-origin method/headers before sending the actual request. 
- **Credentialed Request (CORS)**: A cross-origin request that includes cookies/HTTP auth/client certificates; requires `Access-Control-Allow-Credentials: true` and cannot use `Access-Control-Allow-Origin: *`.
- **Access-Control-Allow-Origin**: Response header that indicates which origin may read the response. 
MDN Web Docs
- **Access-Control-Allow-Credentials**: Response header that enables credentialed cross-origin requests.

### What I shipped:

#### Legacy Lab

- `Cors` helper with `cors_protected` switch + allowlist logic + explicit preflight handling.
- public/api/ping.php JSON endpoint protected by `Cors::handle()`.
- `attacker/W1D5-cors-attacker.html` demo page (Legacy + Symfony targets).

#### Symfony
- nelmio/cors-bundle setup with path-scoped CORS (^/api/) and allowlist origin.
- `/api/ping` endpoint plus explicit OPTIONS preflight route.

### Take away

- CORS is enforced by browsers (SOP), but the server must explicitly opt-in via Access-Control-Allow-*.
- Preflight (OPTIONS) is the common failure point: redirects and missing OPTIONS handling break CORS even when the allowlist is correct.
- Best practice: scope CORS narrowly (e.g., only /api/*), use an allowlist, and only enable credentials when truly required.


## 2025-12-22 (W2D1 - Mon)

### Goal (outcome)
I can create a simple threat model for this capstone: a data-flow diagram (DFD), a list of assets/attackers/entry points, and a STRIDE-based threat list with concrete mitigations. I can write 5–10 abuse-cases and validate them against the Legacy Lab switches and Symfony best practices.

### Proof / Evidence
- `notes/threat-model/THREAT_MODEL.md` contains:
  - a DFD (Mermaid)
  - assets + threat actors + entry points + trust boundaries
  - STRIDE threats with mitigations
  - 10 abuse-cases
  - a short validation checklist
- Evidence notes for at least 2 abuse-cases reproduced in Legacy Lab (switch OFF) and blocked after fix (switch ON)
- Optional: screenshot/export of diagram if I used a diagram tool

### Definitions

- **Threat Modeling**: A structured activity to identify what can go wrong in a system, prioritize risks, and define mitigations and validations based on a model of the system (e.g., DFD).
- **DFD (Data-Flow Diagram)**: A diagram showing processes, data stores, data flows, and external entities to understand how data moves through a system.
- **Trust Boundary**: A boundary where the trust level changes (e.g., browser → server). Crossing it requires validation, authz, and defensive controls.
- **Asset**: Anything valuable that needs protection (e.g., credentials, session cookies, admin actions, user data).
- **Threat Actor**: A person/system that can attack (e.g., external attacker, malicious website, authenticated user with malicious intent).
- **Attack Surface**: All reachable entry points where untrusted input can enter (routes, APIs, forms, headers, uploads).
- **STRIDE**: A threat categorization mnemonic: Spoofing, Tampering, Repudiation, Information Disclosure, Denial of Service, Elevation of Privilege.
- **Abuse Case**: A short attacker-focused scenario describing how a feature can be misused to cause harm.
- **Mitigation**: A control that reduces likelihood or impact (e.g., CSRF tokens, output encoding, prepared statements, access control).
- **Validation (Threat Model)**: Concrete checks to confirm mitigations work (tests, PoCs, security regression cases).

### What I shipped:
- docs: added initial threat model (DFD + STRIDE threats + mitigations + abuse-cases)
- legacy-lab: validated 3 abuse-cases using switches (CSRF, XSS, SQLi) and documented the proof
- symfony: mapped existing mitigations (CSRF/login, access_control, parameter binding, CORS allowlist) back into the threat model

### Take away
- A threat model is only useful if it is tied to concrete entry points and validated with evidence.
- STRIDE is a great checklist, but the DFD (and trust boundaries) is what makes threats “real”.
- My Legacy Lab switches are perfect for proving risk → mitigation → regression-style thinking.


## 2026-01-05 (W2D2)

### Goal (outcome)

- Implement structured logging (JSON / key-value) in both:
  - Legacy Lab (custom logger)
  - Symfony app (Monolog JSON formatting + consistent context fields)
- Implement Correlation ID propagation:
  - Accept incoming header (e.g. X-Correlation-ID)
  - Generate if missing
   Attach to response header
  - Include in every log line
- Define a minimal log schema (fields you always emit)

### Proof / Evidence

- Commit(s): W2D2: structured logging + correlation IDs
- Example log output (copy 3–5 lines) showing:
  - correlation_id present
  - consistent fields (`timestamp`, `level`, `message`, `route`/`path`, `user_id` when available)
- One curl or browser repro:
  - curl -H "X-Correlation-ID: test-123" ... and logs reflect it
- Symfony: functional test proving response has X-Correlation-ID

### Definitions

- **Structured Logging**: Logs emitted as machine-parsable key-value data (often JSON) instead of free-form text.
- **Log Schema**: A consistent set of fields (names + types) that every log entry should include.
- **Correlation ID**: A unique identifier attached to a request/transaction to correlate log entries across components.
- **Propagation**: Passing the same correlation ID through request/response boundaries and between services.
- **Log Context**: Additional structured fields (e.g., user_id, route, ip) attached to log records.
- **Log Injection (Log Forging)**: Manipulating log output (e.g., via newline/control chars) to create misleading or fake log entries.
- **Monolog Processor**: A callable that enriches every log record with extra data before it’s written. 

### What I shipped

#### Legacy Lab

- Implemented structured JSON logging with a consistent base schema (timestamp, level, request_id, method, path).
- Added request ID propagation:
  - Generate UUIDv4 per request.
  - Accept incoming request IDs only from trusted proxies.
  - Always return X-Request-ID in the response.
- Built a central logging abstraction for the legacy codebase instead of ad-hoc string logging.
- Demonstrated a log injection (log forging) vulnerability using unstructured logs and user-controlled input.
- Fixed log injection by:
  - Switching to structured logging.
  - Sanitizing control characters in log messages.
- Implemented context sanitization and redaction:
  - Dropped super-sensitive fields (passwords, tokens, cookies).
  - Redacted sensitive fields (e.g. username, email) while keeping field names.
- Added a fail-safe logging fallback to ensure logs are written even if JSON encoding fails.
- Introduced feature flags to toggle vulnerable vs. fixed behavior for demonstration purposes.

#### Symfony
- Implemented request ID propagation using a kernel event subscriber:
  - Generate a UUIDv4 per request.
  - Accept incoming request IDs only from trusted proxies.
  - Always return X-Request-ID in the response.
- Added a Monolog processor that automatically enriches every log record with request_id.
- Ensured request IDs are consistently available across controllers and logs without manual passing.
- Added functional tests to verify:
  - X-Request-ID is always present and valid.
  - Incoming request IDs are accepted from trusted proxies.
  - Incoming request IDs from untrusted clients are ignored.

### Take away

- Correlation IDs only add value when they are consistently generated, propagated, and trusted.
- Trust boundaries matter: identifiers provided by untrusted clients must not be treated as authoritative.
- Framework-level integration (event subscribers + log processors) is more reliable than ad-hoc logging.

## 2026-01-05 (W2D3)

### Goal (outcome)

Understand and apply essential HTTP security headers to reduce common browser-based attack vectors, and demonstrate their impact in both a legacy application and a Symfony application.

### Proof / Evidence

- Verified presence and behavior of security headers via browser dev tools and automated tests.
- Demonstrated vulnerable vs. hardened configurations using feature flags.
- Strict-Transport-Security header present over HTTPS: `max-age=300; includeSubDomains=`

### Definitions

- **Clickjacking**: A UI-based attack where a user is tricked into clicking on a hidden or disguised element, often by embedding a page in an iframe.
- **X-Frame-Options**: An HTTP response header that controls whether a page may be embedded in a frame or iframe.



- CSP in Report-Only revealed blocked inline scripts/styles.
- Browser console provides actionable feedback before enforcing CSP.
- Even simple pages rely on inline code by default.

- Strict-Transport-Security header present over HTTPS: max-age=300; includeSubDomains

### What I shipped

#### Symfony
* Integrated **NelmioSecurityBundle** to manage security headers using Symfony best practices.
* Implemented **Content Security Policy (CSP)** with **nonce-based authorization** for scripts and styles.
* Successfully integrated CSP nonces with **Symfony Importmap and Turbo**, including proper handling via `meta[name="csp-nonce"]`.
* Cleaned up CSP violations by:

  * Adding nonces to inline scripts (Importmap)
  * Removing or externalizing inline styles
  * Aligning dynamic Turbo-injected assets with CSP
* Enabled **HSTS (Strict-Transport-Security)** via `forced_ssl` with a safe rollout configuration.
* Added **functional tests** verifying presence of:

  * CSP (enforced or report-only)
  * HSTS over HTTPS
  * X-Content-Type-Options
  * Clickjacking protection (X-Frame-Options or CSP `frame-ancestors`)
* Updated existing tests to correctly simulate **HTTPS**, ensuring compatibility with forced SSL redirects.

### Take away

* CSP with nonces is the correct long-term strategy for modern Symfony apps, especially when using Importmap and Turbo.
* Nonces are not secrets; they are request-scoped execution permissions bound to the response.
* Using a battle-tested bundle (NelmioSecurityBundle) avoids fragile custom implementations in production code.
* Security headers must be tested under realistic conditions (HTTPS), otherwise tests can silently break.
* Report-Only mode is essential for safely rolling out CSP in real applications.
