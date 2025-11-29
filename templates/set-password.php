<?php
require_once '../includes/database.php';

$token = $_GET['token'] ?? '';
$valid = false;
$error = '';

if ($token) {
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("SELECT id, email, set_password_expires_at FROM student_registrations WHERE set_password_token = ? LIMIT 1");
        $stmt->execute([$token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            if (new DateTime() <= new DateTime($row['set_password_expires_at'])) {
                $valid = true;
                $studentId = $row['id'];
                $email = $row['email'];
            } else {
                $error = 'This link has expired. Please request a new one.';
            }
        } else {
            $error = 'Invalid or already used link.';
        }
    } catch (Exception $e) {
        $error = 'Unexpected error. Please try again later.';
    }
} else {
    $error = 'Missing token.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Password</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/auth-background.css">
    <style>
        :root{--primary:#9333ea;--primary-2:#a855f7;--text:#111827;--muted:#6b7280;--border:#e5e7eb;--bg:#f3f4f6}
        *{box-sizing:border-box}
        body{font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont;margin:0}
        .card{background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:0 25px 60px rgba(17,24,39,.12);max-width:460px;width:100%;overflow:hidden}
        .card-header{display:flex;align-items:center;gap:12px;padding:20px 22px;border-bottom:1px solid var(--border);background:linear-gradient(0deg,#fff, #fff)}
        .badge{width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,var(--primary),var(--primary-2));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;box-shadow:0 6px 18px rgba(147,51,234,.3);overflow:hidden}
        .badge img{width:26px;height:26px;display:block}
        .title{font-size:20px;font-weight:800;color:var(--text);margin:0}
        .content{padding:20px 22px}
        .subtitle{color:var(--muted);font-size:14px;margin:4px 0 16px}
        label{display:block;font-size:12px;color:#374151;margin:12px 0 6px;font-weight:600}
        .input-wrap{position:relative}
        input{width:100%;padding:12px 56px 12px 12px;border:1px solid var(--border);border-radius:10px;background:#f9fafb;font-size:14px;transition:border-color .15s,box-shadow .15s,background .15s}
        input:focus{outline:none;background:#fff;border-color:var(--primary);box-shadow:0 0 0 3px rgba(147,51,234,.12)}
        .toggle{position:absolute;right:10px;top:50%;transform:translateY(-50%);border:1px solid var(--border);background:#fff;color:#6b7280;cursor:pointer;padding:4px 10px;border-radius:8px;font-size:12px}
        .hint{font-size:12px;color:var(--muted);margin-top:8px}
        .req{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:6px 16px;margin-top:8px}
        .req span{font-size:12px;color:#6b7280;padding:2px 0;border:none;background:transparent;display:flex;align-items:center;gap:8px}
        .req .mark{font-weight:700;color:#9ca3af;width:14px;text-align:center}
        .req span.ok{color:#065f46}
        .req span.ok .mark{color:#10b981}
        .strength{margin-top:8px}
        .bar-wrap{height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden}
        .bar{height:100%;width:0;background:#ef4444;transition:width .2s ease,background .2s ease}
        .label{font-size:13px;margin:6px 0 0;color:#374151;font-weight:600}
        .btn{width:100%;margin-top:16px;padding:12px 14px;border:0;border-radius:12px;background:linear-gradient(135deg,var(--primary),var(--primary-2));color:#fff;font-weight:700;cursor:pointer;transition:transform .06s ease,filter .2s}
        .btn:disabled{opacity:.5;cursor:not-allowed;filter:grayscale(.2)}
        .btn:active{transform:translateY(1px)}
        .alert{border-radius:10px;padding:10px 12px;margin-bottom:12px;font-size:14px}
        .err{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
        .ok{background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46}
    </style>
    <script>
    function toggle(id){
        const el=document.getElementById(id);
        el.type = el.type==='password'?'text':'password'
    }

    function isStrong(p){
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/.test(p);
    }

    function updateMeter(){
        const p=document.getElementById('pwd').value; 
        const btn=document.getElementById('submitBtn');
        const cp=document.getElementById('cpwd').value; btn.disabled = !(isStrong(p) && p===cp);
        // checklist
        const st = {
            len: p.length>=8,
            low: /[a-z]/.test(p),
            up: /[A-Z]/.test(p),
            num: /\d/.test(p),
            sym: /[^A-Za-z0-9]/.test(p)
        };
        function setReq(id, ok){
            const item=document.getElementById(id);
            item.className = ok ? 'ok' : '';
            const m=item.querySelector('.mark');
            if(m) m.textContent = ok ? '✓' : '×';
        }
        setReq('rqLen', st.len);
        setReq('rqLow', st.low);
        setReq('rqUp',  st.up);
        setReq('rqNum', st.num);
        setReq('rqSym', st.sym);
        // strength bar and label
        const met = Object.values(st).filter(Boolean).length;
        const percent = Math.min(100, met/5*100);
        const bar = document.getElementById('strengthBar');
        const label = document.getElementById('strengthLabel');
        bar.style.width = percent + '%';
        let txt = 'Weak password. Must contain:'; let color = '#ef4444';
        if (met>=3){ txt='Fair password. Improve by adding missing types:'; color = '#f59e0b'; }
        if (met>=4){ txt='Good password. Consider adding more variety:'; color = '#10b981'; }
        if (met===5){ txt='Strong password.'; color = '#10b981'; }
        bar.style.background = color; label.textContent = txt;
    }

    async function submitForm(e){
        e.preventDefault();
        const btn=document.getElementById('submitBtn');
        const pwd = document.getElementById('pwd').value.trim();
        const cpwd = document.getElementById('cpwd').value.trim();
        if(!isStrong(pwd)){
            alert('Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.');
            return;
        }
        if(pwd !== cpwd){alert('Passwords do not match.');return}
        btn.disabled=true; btn.textContent='Setting...';
        const res = await fetch('../auth/set_password.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({token:'<?php echo htmlspecialchars($token,ENT_QUOTES); ?>',password:pwd})});
        const data = await res.json();
        btn.disabled=false; btn.textContent='Set password';
        if(data.status==='success'){
            document.getElementById('msg').className='alert ok';
            document.getElementById('msg').textContent='Password set! Redirecting to login...';
            setTimeout(() => { window.location.href = data.redirect || '../templates/login.php'; }, 2000);
        }
        else{document.getElementById('msg').className='alert err';document.getElementById('msg').textContent=data.message||'Failed to set password.'}
    }
    </script>
    </head>
<body>
    <div class="auth-page">
    	<div class="auth-glow"></div>
    	<div class="card auth-card">
        <div class="card-header">
            <div>
                <p class="title">Set your password</p>
                <p class="subtitle">Create a password to secure your account</p>
            </div>
        </div>
        <?php if(!$valid){ ?>
            <div class="alert err"><?php echo htmlspecialchars($error); ?></div>
        <?php } else { ?>
            <div class="content">
                <p class="subtitle">Create a password for <strong><?php echo htmlspecialchars($email); ?></strong>.</p>
                <div id="msg"></div>
                <form onsubmit="submitForm(event)">
                    <label>New password</label>
                    <div class="input-wrap">
                        <input id="pwd" type="password" required minlength="8" placeholder="At least 8 characters" oninput="updateMeter()">
                        <button type="button" class="toggle" aria-label="Toggle password visibility" onclick="this.textContent=this.textContent==='Show'?'Hide':'Show';toggle('pwd')">Show</button>
                    </div>
                    <div class="strength">
                        <div class="bar-wrap"><div class="bar" id="strengthBar"></div></div>
                        <div class="label" id="strengthLabel">Weak password. Must contain:</div>
                    </div>
                    <div class="req">
                        <span id="rqLen"><span class="mark">×</span> At least 8 characters</span>
                        <span id="rqLow"><span class="mark">×</span> At least 1 lowercase letter</span>
                        <span id="rqUp"><span class="mark">×</span> At least 1 uppercase letter</span>
                        <span id="rqNum"><span class="mark">×</span> At least 1 number</span>
                        <span id="rqSym"><span class="mark">×</span> At least 1 symbol</span>
                    </div>
                    <label>Confirm password</label>
                    <div class="input-wrap">
                        <input id="cpwd" type="password" required minlength="8" placeholder="Re-enter password" oninput="updateMeter()">
                        <button type="button" class="toggle" aria-label="Toggle password visibility" onclick="this.textContent=this.textContent==='Show'?'Hide':'Show';toggle('cpwd')">Show</button>
                    </div>
                    <button id="submitBtn" class="btn" type="submit" disabled>Set password</button>
                </form>
            </div>
        <?php } ?>
    	</div>
    </div>
</body>
</html>


