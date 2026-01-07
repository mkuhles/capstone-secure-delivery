<?php
declare(strict_types=1);
namespace LegacyLab\Tests;

final class IdorNotesTest extends BaseTestCase
{

    public function test_user_cannot_view_or_edit_other_users_note_when_protected(): void
    {
        // 1) Login as user
        $user = getenv('LL_USER1') ?: 'user1';
        $pass = getenv('LL_PASS1') ?: 'User1Pass123!';
        $jar = $this->loginAs($user, $pass);
        
        // 2) Verify we are logged in by accessing home page
        $home = $this->request('GET', '/index.php', [], $jar);
        $this->assertSame(200, $home['status']);
        $this->assertStringContainsString('Logged in as', $home['body']);

        // 3) Try to VIEW note #2 (belongs to user2). Expect 404 when idor_protected=true
        $view = $this->request('GET', '/notes.php?id=2', [], $jar);
        $this->assertSame(404, $view['status'], 'status='.$view['status'].' location='.($view['headers']['location'] ?? ''));
        $this->assertSame(404, $view['status'], 'should not be able to view other user\'s note');
        $this->assertStringNotContainsString('Note from user 2', $view['body'], 'response body should not contain other user\'s note content');

        // 4) Try to EDIT note #2. Expect 404 as well
        $list = $this->request('GET', '/notes.php', [], $jar);
        $this->assertSame(200, $list['status']);
        preg_match('/notes\\.php\\?id=(\\d+)/', $list['body'], $m);
        $this->assertNotEmpty($m, 'could not find an owned note link');
        $ownedId = (int)$m[1];

        $edit = $this->request('POST', '/notes.php?id=2', [
            'content' => 'hacked by test',
            '_csrf' => $this->extractCsrf($jar, '/notes.php?id='.$ownedId),
        ], $jar);
        $this->assertSame(404, $edit['status'], 'should not be able to edit other user\'s note');

        // 5) Verify note #2 unchanged by logging in as user2
        $jar2 = $this->loginAs(getenv('LL_USER2') ?: 'admin', getenv('LL_PASS2') ?: 'AdminPass123!');

        $view2 = $this->request('GET', '/notes.php?id=2', [], $jar2);
        $this->assertSame(200, $view2['status']);
        $this->assertStringContainsString('Note from user 2', $view2['body']);
        $this->assertStringNotContainsString('hacked by test', $view2['body']);
    }

}
