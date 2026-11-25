<?php
function e(?string $v): string { return htmlspecialchars($v ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function go(string $url): never { header('Location: ' . $url); exit; }
function csrf(): string { if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32)); return $_SESSION['_csrf']; }
function verify_csrf(): void { if (!hash_equals(csrf(), (string)($_POST['_csrf'] ?? ''))) { http_response_code(419); exit('Your form expired. Refresh and try again.'); } }
function user(): ?array { return $_SESSION['user'] ?? null; }
function require_role(string $role): void { if (!user()) go('?page=login'); if (user()['role'] !== $role) { http_response_code(403); exit('Access denied.'); } }
function flash(string $message, string $type='success'): void { $_SESSION['_flash'] = [$message, $type]; }
function audit(string $action, ?int $relatedId=null, string $description=''): void { $s=db()->prepare('INSERT INTO activity_logs(user_id,action,related_id,description,ip_address) VALUES(?,?,?,?,?)'); $s->execute([user()['id'] ?? null,$action,$relatedId,$description,substr($_SERVER['REMOTE_ADDR'] ?? '',0,45)]); }
function notify(int $uid, string $title, string $body, ?int $ref=null): void { db()->prepare('INSERT INTO notifications(user_id,title,body,related_id) VALUES(?,?,?,?)')->execute([$uid,$title,$body,$ref]); }
function app_status(int $appId): string { $q=db()->prepare('SELECT status FROM documents WHERE application_id=?'); $q->execute([$appId]); $s=array_column($q->fetchAll(),'status'); if (!$s) return 'Not Submitted'; if (array_intersect($s,['Rejected','Resubmission Required'])) return 'Action Required'; if (count($s)>=4 && count(array_filter($s,fn($v)=>$v==='Approved'))===4) return 'Completed'; return 'Under Review'; }
function sync_application(int $id): string { $status=app_status($id); db()->prepare('UPDATE applications SET status=?,updated_at=NOW() WHERE id=?')->execute([$status,$id]); return $status; }
function current_application(): ?array { if (!user()) return null; $q=db()->prepare('SELECT * FROM applications WHERE user_id=?'); $q->execute([user()['id']]); return $q->fetch() ?: null; }
function url(string $page, array $params=[]): string { return '?' . http_build_query(['page'=>$page]+$params); }
