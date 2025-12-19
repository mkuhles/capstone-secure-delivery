# Glossary

- **Authentication (AuthN)**: Proving who a user is (login/identity).
- **Authorization (AuthZ)**: Deciding what an authenticated user is allowed to do.
- **Cookie**: Small data stored in the browser and sent with requests to maintain state (sessions).
- **Cookie Attributes**:
    - `httpOnly` can't be set by js
    - `path` will only be send in header if requested URL access the path or subdirectories
    - `SameSite`
        - `Strict` will only be send in header if requested URL request originating from cookie's origin site (no landingpages)
        - `Lax` default, will only be send in header if user navigates to cookie's origin site
        - `None`  specifies that cookies are sent on both originating and cross-site requests. So called thirt party cookies
    - `security` requriers https
- **CSRF (cross-site request forgery)**: A cross-site request forgery attack tricks a victim into using their credentials to invoke a state-changing activity.
- **HTTP**: A stateless client-server protocol used for web communication.
- **JWT (JSON Web Token)**: a way for securely transmitting information between parties as a JSON object
- **Header**: Metadata sent with a request/response (e.g., Content-Type).
- **Sec-Fetch-Dest**: Browser header indicating the destination type of a request (e.g., document, script, image). Used as part of Fetch Metadata for request context and defensive filtering.
- **Status code**: A number describing the result of a request (e.g., 200, 404).
    - **401**: not authenticated
    - **403**: authenticated but not allowed.
- **thirt party cookies**: cookie set by an other website
- **XSS (Cross-Site Scripting)** is a vulnerability where an application injects attacker-controlled input into a page without proper output encoding, enabling arbitrary JavaScript execution in a victim’s browser under the site’s origin.
    - **Reflected XSS** happens when attacker-controlled data from the request (often the URL/query string) is immediately included in the response without proper output encoding, causing JavaScript to execute in the victim’s browser.
    - **Stored XSS** occurs when malicious payloads are saved on the server (e.g., in a database) and later rendered without output encoding, causing the script to execute in users’ browsers.

