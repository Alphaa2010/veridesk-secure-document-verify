<?php
// Run from a local terminal after configuring config/local.php: php database/seed.php
require __DIR__ . '/../config/bootstrap.php';
$pdo=db();
$pdo->beginTransaction();
try {
 $adminPassword=getenv('VERIDESK_ADMIN_PASSWORD');$candidatePassword=getenv('VERIDESK_CANDIDATE_PASSWORD');
 if(!$adminPassword||strlen($adminPassword)<12||!$candidatePassword||strlen($candidatePassword)<12)throw new RuntimeException('Set VERIDESK_ADMIN_PASSWORD and VERIDESK_CANDIDATE_PASSWORD (12+ characters) before seeding.');
 $hash=password_hash($adminPassword,PASSWORD_DEFAULT);
 $pdo->prepare("INSERT INTO users(name,email,password_hash,role) VALUES('VeriDesk Administrator','admin@veridesk.local',?,'admin') ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)")->execute([$hash]);
 $admin=(int)$pdo->lastInsertId();
 $names=['Avery Morgan','Jordan Lee','Taylor Brooks','Riley Shah','Casey Patel','Morgan Reed'];
 $refs=['VD-26A7K2','VD-26B8M4','VD-26C9P1','VD-26D3R6','VD-26E4T8','VD-26F5W2'];
 foreach($names as $i=>$name){$email='candidate'.($i+1).'@example.test';$pdo->prepare("INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,'candidate') ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)")->execute([$name,$email,password_hash($candidatePassword,PASSWORD_DEFAULT)]);$uid=(int)$pdo->lastInsertId();$pdo->prepare('INSERT INTO applications(user_id,reference_no,status,submitted_at) VALUES(?,?,?,NOW()) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)')->execute([$uid,$refs[$i],$i===0?'Completed':($i===1?'Action Required':'Under Review')]);$aid=(int)$pdo->lastInsertId();
  $states=$i===0?['Approved','Approved','Approved','Approved']:($i===1?['Approved','Resubmission Required','Pending','Pending']:['Pending','Pending','Pending','Pending']);
  foreach(['Identity Proof','Educational Certificate','Address Proof','Reference Document'] as $j=>$cat){$q=$pdo->prepare('SELECT id FROM documents WHERE application_id=? AND category=?');$q->execute([$aid,$cat]);if(!$q->fetchColumn())$pdo->prepare('INSERT INTO documents(application_id,category,original_name,stored_name,mime_type,file_size,status,admin_remarks) VALUES(?,?,?,?,?,?,?,?)')->execute([$aid,$cat,'DEMO - placeholder only.txt','demo-'.$aid.'-'.$j.'.pdf','application/pdf',0,$states[$j],$states[$j]==='Resubmission Required'?'Demo note: upload a clearer fictional sample.':null]);}
  $pdo->prepare('INSERT INTO notifications(user_id,title,body,related_id) VALUES(?,?,?,?)')->execute([$uid,'Application update','Your fictional demonstration application is available in VeriDesk.',$aid]);
 }
 $pdo->prepare('INSERT INTO notifications(user_id,title,body) VALUES(?,?,?)')->execute([$admin,'Demo data loaded','Six fictional candidate records were seeded.']);
 $pdo->prepare('INSERT INTO activity_logs(user_id,action,description) VALUES(?,?,?)')->execute([$admin,'demo_seeded','Fictional demonstration data seeded.']);
 $pdo->commit();echo "Seed complete. Local demo credentials are documented in README.md.\n";
}catch(Throwable $e){$pdo->rollBack();throw $e;}
