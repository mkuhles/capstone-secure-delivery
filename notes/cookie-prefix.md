see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/Cookies#cookie_prefixes

## Worum geht’s in einem Satz?

Cookie-Prefixe sind ein Browser-Trick, um bestimmte Sicherheitsregeln für Cookies zu erzwingen.
Wenn ein Cookie-Name mit z.B. __Host- beginnt, akzeptiert der Browser das Cookie nur, wenn es sicher gesetzt wurde (HTTPS, Secure, Path=/, kein Domain-Attribut etc.). Sonst wird es abgelehnt.

## Warum braucht man das überhaupt?

Weil Cookies “alt” sind:

Der Server kann später oft nicht sicher beweisen, woher ein Cookie ursprünglich kam.

Und ein Subdomain-Angreifer kann unter Umständen Cookies so setzen, dass er dein Session-Verhalten beeinflusst (Session Fixation).

Prefixe sind also Defense-in-Depth: Sie verhindern bestimmte “falsch gesetzte” Cookies direkt im Browser.

## Konkretes Bild (Session Fixation in einfach)

Stell dir vor, dein Login prüft nur: „Gibt es Cookie SESSIONID? Dann gilt der User als eingeloggt.“

Wenn ein Angreifer irgendwie schafft, dass dein Browser schon vorher ein SESSIONID=attackersession hat (z.B. über Subdomain/Fehlkonfiguration), dann loggst du dich ein und bindest dein Login an seine Session → Fixation.

Prefixe sorgen dafür, dass ein sensibles Session-Cookie nicht “locker” gesetzt werden kann.

## Die zwei wichtigsten Prefixe

- `__Secure-…` Muss über HTTPS gesetzt sein und Secure haben.
→ Minimaler Schutz: “nicht über unsichere Verbindung setzbar”.

- `__Host-…` (der König für Session-Cookies)
Muss über HTTPS gesetzt sein + Secure
UND 
kein `Domain=` (also nur exakt dieser Host, nicht alle Subdomains)
SOWIE
`Path=/`
→ Dadurch kann kein anderer Subdomain/Path das Cookie “überschreiben” oder verbreitern.

Die anderen zwei (`__Http-`, `__Host-Http-`) sind Varianten, die zusätzlich HttpOnly erzwingen bzw. kombinieren. In der Praxis: nett, aber `__Host-` ist meist der große Hebel.