<?php
declare(strict_types=1);
namespace LegacyLab\Tests;

use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase {
    private string $baseUrl = 'http://127.0.0.1:8081';

    protected function loginAs(string $username, string $password): string
    {
        $jar = tempnam(sys_get_temp_dir(), 'cookiejar_');
        $this->assertNotFalse($jar);
        
        $token = $this->extractCsrf($jar, '/login.php');
        $this->assertNotSame('', $token, 'csrf token missing on login page');

        $login = $this->request('POST', '/login.php', [
            'username' => $username,
            'password' => $password,
            '_csrf' => $token,
        ], $jar);

        // Many flows redirect on success; 200 could also happen.
        $this->assertContains($login['status'], [200, 302], 'login POST for '.$username.'  did not return 200/302');

        // Now verify session is actually authenticated
        $home = $this->request('GET', '/index.php', [], $jar);
        $this->assertSame(200, $home['status'], 'home should be reachable after login');

        // This makes wrong creds obvious:
        $this->assertStringContainsString(
            $username,
            $home['body'],
            'login seems to have failed: username not present on home page'
        );

        return $jar;
    }

    /** @return array{status:int, body:string, headers:array<string,string>} */
    protected function request(string $method, string $path, array $data, string $cookieJar): array
    {
        $ch = curl_init();
        $url = $this->baseUrl . $path;

        $headers = [];
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HEADERFUNCTION => function ($ch, $line) use (&$headers) {
                $len = strlen($line);
                $parts = explode(':', $line, 2);
                if (count($parts) === 2) {
                    $headers[strtolower(trim($parts[0]))] = trim($parts[1]);
                }
                return $len;
            },
            CURLOPT_COOKIEJAR => $cookieJar,
            CURLOPT_COOKIEFILE => $cookieJar,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $body = (string)curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        return ['status' => $status, 'body' => $body, 'headers' => $headers];
    }

    protected function extractCsrf(string $jar, string $path, array $data = [], string $fieldName = '_csrf') : string {
        $page = $this->request('GET', $path, $data, $jar);
        $this->assertSame(200, $page['status'], $path.' page must load');

        if (preg_match('/name="'.$fieldName.'" value="([^"]+)"/', $page['body'], $m)) {
            return html_entity_decode($m[1], ENT_QUOTES);
        }
        return '';
    }
}