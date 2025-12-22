# Glossary

- **Abuse Case**: A short attacker-focused scenario describing how a feature can be misused to cause harm.
- **Access-Control-Allow-Credentials**: Response header that enables credentialed cross-origin requests.
- **Access-Control-Allow-Origin**: Response header that indicates which origin may read the response. 
MDN Web Docs
- **Asset**: Anything valuable that needs protection (e.g., credentials, session cookies, admin actions, user data).
- **Attack Surface**: All reachable entry points where untrusted input can enter (routes, APIs, forms, headers, uploads).
- **Authentication (AuthN)**: Proving who a user is (login/identity).
- **Authorization (AuthZ)**: Deciding what an authenticated user is allowed to do.
- **Blind SQLi**: No direct query output; attacker infers results via behavior (true/false) or timing.
- **Cookie**: Small data stored in the browser and sent with requests to maintain state (sessions).
- **Cookie Attributes**:
    - `httpOnly` can't be set by js
    - `path` will only be send in header if requested URL access the path or subdirectories
    - `SameSite`
        - `Strict` will only be send in header if requested URL request originating from cookie's origin site (no landingpages)
        - `Lax` default, will only be send in header if user navigates to cookie's origin site
        - `None`  specifies that cookies are sent on both originating and cross-site requests. So called thirt party cookies
    - `security` requriers https
- **CORS (Cross-Origin Resource Sharing)**: An HTTP-header-based mechanism that allows a server to specify which other origins a browser may allow reading resources from; includes optional preflight requests. 
- **Credentialed Request (CORS)**: A cross-origin request that includes cookies/HTTP auth/client certificates; requires `Access-Control-Allow-Credentials: true` and cannot use `Access-Control-Allow-Origin: *`.
- **CSRF (cross-site request forgery)**: A cross-site request forgery attack tricks a victim into using their credentials to invoke a state-changing activity.
- **DFD (Data-Flow Diagram)**: A diagram showing processes, data stores, data flows, and external entities to understand how data moves through a system.
- **HTTP**: A stateless client-server protocol used for web communication.
- **JWT (JSON Web Token)**: a way for securely transmitting information between parties as a JSON object
- **Header**: Metadata sent with a request/response (e.g., Content-Type).
- **Mitigation**: A control that reduces likelihood or impact (e.g., CSRF tokens, output encoding, prepared statements, access control).
- **Origin**: The tuple (scheme, host, port) identifying where a request is made from.
- **OWASP (Open Worldwide Application Security Project)**: A non-profit foundation that supports projects, tools, and guidance to improve software security. 
- **OWASP Top 10**: A standard awareness document listing the most critical web application security risk categories, used as a baseline to align teams on priorities and secure coding practices. 
- **Parameter binding**: Passing user input as parameters (e.g. :id, :q) instead of embedding it into SQL text.
- **Preflight Request**: A browser-sent OPTIONS request that checks whether the server permits the intended cross-origin method/headers before sending the actual request. 
- **Prepared Statement**: Query is defined with placeholders first; values are bound separately so input cannot change SQL structure.
- **Same-Origin Policy (SOP)**: A browser security rule that restricts scripts from reading responses from a different origin (scheme/host/port) unless explicitly allowed.
- **Sec-Fetch-Dest**: Browser header indicating the destination type of a request (e.g., document, script, image). Used as part of Fetch Metadata for request context and defensive filtering.
- **SQL Injection (SQLi)** Vulnerability where untrusted input becomes part of an SQL query, allowing an attacker to change the query logic/structure.
- **Status code**: A number describing the result of a request (e.g., 200, 404).
    - **401**: not authenticated
    - **403**: authenticated but not allowed.
- **STRIDE**: A threat categorization mnemonic: Spoofing, Tampering, Repudiation, Information Disclosure, Denial of Service, Elevation of Privilege.
- **String concatenation (in SQL)**: Building SQL queries by joining strings + user input (common root cause for SQLi).
- **Tainted input**: Data from untrusted sources (e.g. $_GET/$_POST) that must be treated as hostile until validated/bound safely.
- **thirt party cookies**: cookie set by an other website
- **Threat Actor**: A person/system that can attack (e.g., external attacker, malicious website, authenticated user with malicious intent).
- **Threat Modeling**: A structured activity to identify what can go wrong in a system, prioritize risks, and define mitigations and validations based on a model of the system (e.g., DFD).
- **Trust Boundary**: A boundary where the trust level changes (e.g., browser → server). Crossing it requires validation, authz, and defensive controls.
- **UNION-based SQLi**: Using UNION SELECT to extract data from other tables when the app returns query results.
- **Validation (Threat Model)**: Concrete checks to confirm mitigations work (tests, PoCs, security regression cases).
- **XSS (Cross-Site Scripting)** is a vulnerability where an application injects attacker-controlled input into a page without proper output encoding, enabling arbitrary JavaScript execution in a victim’s browser under the site’s origin.
    - **Reflected XSS** happens when attacker-controlled data from the request (often the URL/query string) is immediately included in the response without proper output encoding, causing JavaScript to execute in the victim’s browser.
    - **Stored XSS** occurs when malicious payloads are saved on the server (e.g., in a database) and later rendered without output encoding, causing the script to execute in users’ browsers.




