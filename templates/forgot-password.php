<?php ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/auth-background.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root{--primary:#9333ea;--primary-2:#a855f7;--text:#111827;--muted:#6b7280;--border:#e5e7eb;--bg:#f3f4f6}
        *{box-sizing:border-box}
        body{font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont;margin:0}
        .card{background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:0 25px 60px rgba(17,24,39,.12);max-width:480px;width:100%;overflow:hidden}
        .card-header{padding:16px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:12px}
        .title{font-size:20px;font-weight:800;color:var(--text);margin:0}
        .subtitle{color:var(--muted);font-size:14px;margin:6px 0 0}
        .content{padding:20px 22px}
        label{display:block;font-size:12px;color:#374151;margin:12px 0 6px;font-weight:600}
        input{width:100%;padding:12px;border:1px solid var(--border);border-radius:10px;background:#f9fafb;font-size:14px;transition:border-color .15s,box-shadow .15s,background .15s}
        input:focus{outline:none;background:#fff;border-color:var(--primary);box-shadow:0 0 0 3px rgba(147,51,234,.12)}
        .row{display:flex;gap:10px}
        .btn{width:100%;margin-top:16px;padding:12px 14px;border:0;border-radius:12px;background:linear-gradient(135deg,var(--primary),var(--primary-2));color:#fff;font-weight:700;cursor:pointer}
        .btn:disabled{opacity:.5;cursor:not-allowed}
        .btn.loading{position:relative}
        .btn.loading::after{content:'';position:absolute;right:14px;top:50%;width:16px;height:16px;border:2px solid rgba(255,255,255,.5);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite;transform:translateY(-50%)}
        @keyframes spin{to{transform:translateY(-50%) rotate(360deg)}}
        .muted{color:var(--muted);font-size:12px;margin-top:8px}
        .toggle{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:transparent;border:none;color:#6b7280;cursor:pointer;padding:0;border-radius:8px;font-size:16px;display:inline-flex;align-items:center}
        .toggle:hover{color:#374151}
        .input-wrap{position:relative}
        .req{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:6px 16px;margin-top:8px}
        .req span{font-size:12px;color:#6b7280;padding:2px 0;border:none;background:transparent;display:flex;align-items:center;gap:8px}
        .req .mark{font-weight:700;color:#9ca3af;width:14px;text-align:center}
        .req span.ok{color:#065f46}
        .req span.ok .mark{color:#10b981}
        .alert{border-radius:10px;padding:10px 12px;margin-bottom:12px;font-size:14px}
        .err{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
        .ok{background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46}
        .hidden{display:none}
        .back-link{display:inline-flex;align-items:center;gap:6px;color:#9333ea;background:transparent;border:none;padding:0;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px}
        .back-link:hover{color:#7c3aed;text-decoration:underline}
        #fp-preloader{position:fixed;inset:0;background:rgba(255,255,255,.7);display:none;align-items:center;justify-content:center;z-index:1000}
        #fp-preloader .loader{border:4px solid #e5e7eb;border-top:4px solid var(--primary);border-radius:50%;width:46px;height:46px;animation:spin2 .8s linear infinite}
        @keyframes spin2{to{transform:rotate(360deg)}}
    </style>
    <script>
        function isStrong(p){
            return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/.test(p);
        }
        function toggle(id){
            const el=document.getElementById(id);
            el.type = el.type==='password'?'text':'password'
        }
        function setReq(id, ok){
            const item=document.getElementById(id);
            item.className = ok ? 'ok' : '';
            const m=item.querySelector('.mark');
            if(m) m.textContent = ok ? '✓' : '×';
        }
        function toggleEye(inputId, iconId){
            const pw=document.getElementById(inputId);
            const icon=document.getElementById(iconId);
            if(pw.type==='password'){
                pw.type='text';
                if(icon){ icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
            }else{
                pw.type='password';
                if(icon){ icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
            }
        }
        function updatePasswordUI(){
            const p=document.getElementById('newpwd').value;
            setReq('rqLen', p.length>=8);
            setReq('rqLow', /[a-z]/.test(p));
            setReq('rqUp', /[A-Z]/.test(p));
            setReq('rqNum', /\d/.test(p));
            setReq('rqSym', /[^A-Za-z0-9]/.test(p));
            const cp=document.getElementById('confirmpwd').value;
            document.getElementById('resetBtn').disabled = !(isStrong(p) && p===cp);
        }
        async function requestOTP(e){
            e.preventDefault();
            const email=document.getElementById('email').value.trim();
            if(!email){alert('Enter your email');return}
            const btn=document.getElementById('sendBtn');
            btn.classList.add('loading'); btn.disabled=true;
            document.getElementById('fp-preloader').style.display='flex';
            const res=await fetch('../auth/request_password_otp.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({email})});
            const data=await res.json();
            const msg=document.getElementById('msg');
            if(data.status==='success'){
                msg.className='alert ok'; msg.textContent='OTP sent to your email. Check your inbox.';
                setTimeout(()=>{
                  document.getElementById('step1').classList.add('hidden');
                  document.getElementById('step2').classList.remove('hidden');
                },250);
                document.getElementById('email2').value=email;
            }else{ msg.className='alert err'; msg.textContent=data.message||'Email not found.'; }
            btn.classList.remove('loading'); btn.disabled=false;
            document.getElementById('fp-preloader').style.display='none';
        }
        async function resetPassword(e){
            e.preventDefault();
            const payload={
                email: document.getElementById('email2').value.trim(),
                otp: document.getElementById('otp').value.trim(),
                password: document.getElementById('newpwd').value.trim()
            };
            if(!isStrong(payload.password)){ alert('Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.'); return; }
            const res=await fetch('../auth/reset_password_with_otp.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
            const data=await res.json();
            const msg=document.getElementById('msg');
            if(data.status==='success'){
                msg.className='alert ok'; msg.textContent='Password updated. You can now log in.';
            }else{ msg.className='alert err'; msg.textContent=data.message||'Reset failed.'; }
        }
    </script>
    </head>
<body>
    <div class="auth-page">
    	<div class="auth-glow"></div>
    	<div class="card auth-card">
        <div class="card-header">
            <div>
                <p class="title">Forgot password</p>
                <p class="subtitle">We will send a one-time code to your email</p>
            </div>
            <a class="back-link" href="./login.php">← Back to login</a>
        </div>
        <div class="content">
            <div id="msg"></div>
            <form id="step1" onsubmit="requestOTP(event)">
                <label>Email address</label>
                <input id="email" type="email" placeholder="you@example.com" required>
                <button id="sendBtn" class="btn" type="submit">Send OTP</button>
            </form>

            <form id="step2" class="hidden" onsubmit="resetPassword(event)">
                <div class="row">
                    <div style="flex:1">
                        <label>OTP</label>
                        <input id="otp" type="text" placeholder="6-digit code" maxlength="6" required>
                    </div>
                    <div style="flex:1">
                        <label>Email</label>
                        <input id="email2" type="email" readonly>
                    </div>
                </div>

                <label>New password</label>
                <div class="input-wrap">
                    <input id="newpwd" type="password" minlength="8" placeholder="At least 8 characters" required oninput="updatePasswordUI()">
                    <button type="button" class="toggle" aria-label="Toggle password visibility" onclick="toggleEye('newpwd','eyeNew')"><i class="fas fa-eye-slash" id="eyeNew"></i></button>
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
                    <input id="confirmpwd" type="password" minlength="8" placeholder="Re-enter password" required oninput="updatePasswordUI()">
                    <button type="button" class="toggle" aria-label="Toggle password visibility" onclick="toggleEye('confirmpwd','eyeConfirm')"><i class="fas fa-eye-slash" id="eyeConfirm"></i></button>
                </div>
                <button id="resetBtn" class="btn" type="submit" disabled>Reset password</button>
            </form>
            <p class="muted">Didn’t receive a code? Check your spam folder or request again after a minute.</p>
        </div>
    	</div>
    </div>
    <div id="fp-preloader"><div class="loader"></div></div>
</body>
</html>


