@extends('layouts.app')
@section('title', 'Kuis Tebak Bendera')

@push('styles')
<style>
    #quizBox, #resultBox { display: none; }
    .option-btn {
        width: 100%; padding: 12px 16px; margin: 6px 0;
        border: 2px solid #E2E8F0; border-radius: 10px;
        background: #fff; font-size: .95rem; cursor: pointer;
        transition: all .2s; text-align: left; font-weight: 500;
    }
    .option-btn:hover { border-color: var(--primary); background: #F8FAFC; transform: translateX(3px); }
    .option-btn.correct { border-color: #059669; background: #D1FAE5; color: #065F46; }
    .option-btn.wrong   { border-color: #DC2626; background: #FEE2E2; color: #991B1B; }
    .option-btn:disabled { cursor: not-allowed; transform: none !important; }
    .flag-quiz { max-height: 200px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,.12); }
    #progressBar { height: 8px; border-radius: 4px; transition: width .5s; }
    #timerBar { height: 6px; border-radius: 3px; transition: width 1s linear; }
    .score-circle {
        width: 120px; height: 120px; border-radius: 50%;
        background: linear-gradient(135deg, #0F2B4B, #1A3F6A);
        color: #fff; display: flex; flex-direction: column;
        align-items: center; justify-content: center; margin: 0 auto;
        box-shadow: 0 4px 20px rgba(15,43,75,.2);
    }

    /* Level selector */
    .level-card {
        border: 2px solid #E2E8F0; border-radius: var(--radius);
        padding: 1.5rem; cursor: pointer; text-align: center;
        transition: all .25s; background: #fff;
    }
    .level-card:hover { border-color: var(--primary); transform: translateY(-3px); box-shadow: var(--card-shadow-hover); }
    .level-card.selected { border-color: var(--primary); background: #EFF6FF; box-shadow: 0 0 0 3px rgba(15,43,75,.1); }
    .level-card .icon { font-size: 2.5rem; margin-bottom: 8px; }
    .level-card h5 { font-weight: 700; color: var(--primary); margin-bottom: 4px; }
    .level-card .desc { color: #64748B; font-size: .85rem; }
    .level-card .badge-custom { font-size: .7rem; padding: 2px 10px; }

    .custom-options { display: none; margin-top: 1rem; padding: 1rem; background: #F8FAFC; border-radius: var(--radius-sm); }
    .custom-options.show { display: block; }
    .custom-options label { font-weight: 600; font-size: .85rem; color: #475569; }
    .custom-options .form-range { width: 100%; }
    .custom-options .val { font-weight: 700; color: var(--primary); }
</style>
@endpush

@section('content')
<h4 class="fw-bold mb-1" style="color:var(--primary);">🏳️ Kuis Tebak Bendera</h4>
<p class="text-muted mb-3">Tebak nama negara dari benderanya! Pilih level dan mulai kuis.</p>

<div id="startBox" class="card p-4">
    <div class="text-center mb-4">
        <div style="width:70px;height:70px;margin:0 auto 12px;background:linear-gradient(135deg,#EDE9FE,#C4B5FD);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;">🌍</div>
        <h4 class="fw-bold" style="color:var(--primary);">Pilih Level Kuis</h4>
        <p class="text-muted small mb-0">Atur tingkat kesulitan sesuai keinginanmu</p>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4 col-6">
            <div class="level-card selected" data-level="easy" onclick="selectLevel(this)">
                <div class="icon">😊</div>
                <h5>Easy</h5>
                <div class="desc">10 soal · 20 detik<br>4 pilihan</div>
                <span class="badge bg-success mt-2">Skor maks: 100</span>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="level-card" data-level="hard" onclick="selectLevel(this)">
                <div class="icon">😈</div>
                <h5>Hard</h5>
                <div class="desc">20 soal · 10 detik<br>6 pilihan</div>
                <span class="badge bg-danger mt-2">Skor maks: 200</span>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="level-card" data-level="custom" onclick="selectLevel(this)">
                <div class="icon">🎛️</div>
                <h5>Custom</h5>
                <div class="desc">Atur sendiri<br>soal & timer</div>
                <span class="badge bg-warning text-dark mt-2">Bebas atur</span>
            </div>
        </div>
    </div>

    <div class="custom-options" id="customOptions">
        <div class="row g-3">
            <div class="col-md-6">
                <label>Jumlah Soal: <span class="val" id="countVal">10</span></label>
                <input type="range" class="form-range" min="5" max="30" value="10" id="customCount"
                       oninput="document.getElementById('countVal').textContent=this.value">
                <div class="d-flex justify-content-between small text-muted"><span>5</span><span>30</span></div>
            </div>
            <div class="col-md-6">
                <label>Waktu per Soal: <span class="val" id="timerVal">15</span> detik</label>
                <input type="range" class="form-range" min="5" max="30" value="15" id="customTimer"
                       oninput="document.getElementById('timerVal').textContent=this.value">
                <div class="d-flex justify-content-between small text-muted"><span>5</span><span>30</span></div>
            </div>
        </div>
    </div>

    <div class="text-center mt-3">
        <button class="btn btn-primary btn-lg px-5" onclick="startQuiz()" id="btnStart">
            <i class="bi bi-play-fill"></i> Mulai Kuis
        </button>
    </div>
</div>

<div id="quizBox">
    <div class="card mb-3 p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-semibold" style="color:var(--primary);">
                <i class="bi bi-question-circle"></i> Soal <span id="qNum">1</span> / <span id="qTotal">10</span>
            </span>
            <span class="badge me-1" id="levelBadge" style="background:var(--primary);font-size:.7rem;">Easy</span>
            <span class="badge" style="background:var(--primary);font-size:.85rem;" id="scoreDisplay">Skor: 0</span>
            <span class="fw-bold" style="color:#DC2626;" id="timerText">⏱ 20</span>
        </div>
        <div class="bg-light rounded overflow-hidden mb-1">
            <div id="progressBar" class="bg-primary" style="width:0%"></div>
        </div>
        <div class="bg-light rounded overflow-hidden">
            <div id="timerBar" style="width:100%;background:linear-gradient(90deg,#DC2626,#EF4444);"></div>
        </div>
    </div>

    <div class="card p-4 text-center">
        <p class="text-muted mb-3">Negara manakah yang memiliki bendera ini?</p>
        <img id="flagImg" src="" alt="Bendera" class="flag-quiz mb-4 mx-auto d-block">
        <div id="optionsBox" class="text-start"></div>
        <div id="feedbackBox" class="mt-3 d-none">
            <div id="feedbackText" class="fw-bold fs-5"></div>
            <div id="correctAnswer" class="text-muted small"></div>
        </div>
    </div>
</div>

<div id="resultBox" class="card text-center py-5 px-4">
    <h4 class="fw-bold mb-3" style="color:var(--primary);">🎉 Kuis Selesai!</h4>
    <div class="score-circle mb-3">
        <div class="fs-2 fw-bold" id="finalScore">0</div>
        <div class="small" style="opacity:.8;" id="finalMax">/ 100</div>
    </div>
    <div class="mt-3 mb-1 fs-5" id="resultMsg"></div>
    <div class="text-muted small mb-4" id="resultDetail"></div>

    <div class="d-flex gap-3 justify-content-center flex-wrap">
        <button class="btn btn-primary px-4" onclick="location.reload()">
            <i class="bi bi-arrow-repeat"></i> Main Lagi
        </button>
        <a href="{{ route('leaderboard.index') }}" class="btn btn-warning px-4">
            <i class="bi bi-trophy"></i> Leaderboard
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

let questions = [], currentQ = 0, score = 0, correctCount = 0;
let timer = null, timeLeft = 15, startTime = null, totalTime = 15;
let answered = false, maxScore = 100, maxQuestions = 10;

function selectLevel(el) {
    document.querySelectorAll('.level-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');

    const isCustom = el.dataset.level === 'custom';
    document.getElementById('customOptions').classList.toggle('show', isCustom);
}

async function startQuiz() {
    const selected = document.querySelector('.level-card.selected');
    const level = selected ? selected.dataset.level : 'easy';

    const btn = document.getElementById('btnStart');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memuat...';

    let params = `level=${level}`;
    if (level === 'custom') {
        const count = document.getElementById('customCount').value;
        const timer = document.getElementById('customTimer').value;
        params += `&count=${count}&timer=${timer}`;
    }

    try {
        const res = await fetch(`{{ route("kuis.questions") }}?${params}`, {
            headers: {'X-Requested-With':'XMLHttpRequest'}
        });
        const data = await res.json();

        if (!res.ok || data.error) {
            alert(data.error || 'Gagal memuat soal.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-play-fill"></i> Mulai Kuis';
            return;
        }

        questions    = data.questions;
        currentQ     = 0;
        score        = 0;
        correctCount = 0;
        startTime    = Date.now();
        totalTime    = data.timer || 15;
        maxScore     = data.max_score || 100;
        maxQuestions = data.total || 10;

        document.getElementById('startBox').style.display  = 'none';
        document.getElementById('resultBox').style.display = 'none';
        document.getElementById('quizBox').style.display   = 'block';

        const badge = document.getElementById('levelBadge');
        badge.textContent = level.charAt(0).toUpperCase() + level.slice(1);

        renderQuestion();
    } catch(err) {
        alert('Koneksi gagal: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-play-fill"></i> Mulai Kuis';
    }
}

function renderQuestion() {
    if (currentQ >= questions.length) { showResult(); return; }

    const q = questions[currentQ];
    answered = false;

    document.getElementById('qNum').textContent    = currentQ + 1;
    document.getElementById('qTotal').textContent  = questions.length;
    document.getElementById('flagImg').src         = q.flag;
    document.getElementById('feedbackBox').classList.add('d-none');
    document.getElementById('progressBar').style.width = ((currentQ / questions.length) * 100) + '%';

    document.getElementById('optionsBox').innerHTML = q.options.map(opt => `
        <button class="option-btn" onclick="checkAnswer(this, '${opt.replace(/'/g,"\\'")}', '${q.answer.replace(/'/g,"\\'")}')">
            ${opt}
        </button>
    `).join('');

    startTimer();
}

function startTimer() {
    clearInterval(timer);
    timeLeft = totalTime;
    updateTimerUI();

    timer = setInterval(() => {
        timeLeft--;
        updateTimerUI();
        if (timeLeft <= 0) {
            clearInterval(timer);
            if (!answered) autoFail();
        }
    }, 1000);
}

function updateTimerUI() {
    document.getElementById('timerText').textContent = '⏱ ' + timeLeft;
    document.getElementById('timerBar').style.width = ((timeLeft / totalTime) * 100) + '%';
}

function autoFail() {
    answered = true;
    const q = questions[currentQ];
    document.querySelectorAll('.option-btn').forEach(btn => {
        btn.disabled = true;
        if (btn.textContent.trim() === q.answer) btn.classList.add('correct');
    });
    showFeedback(false, q.answer);
    setTimeout(nextQuestion, 1800);
}

function checkAnswer(btn, chosen, correct) {
    if (answered) return;
    answered = true;
    clearInterval(timer);

    document.querySelectorAll('.option-btn').forEach(b => b.disabled = true);

    const isCorrect = chosen === correct;
    btn.classList.add(isCorrect ? 'correct' : 'wrong');

    if (!isCorrect) {
        document.querySelectorAll('.option-btn').forEach(b => {
            if (b.textContent.trim() === correct) b.classList.add('correct');
        });
    } else {
        const earned = Math.max(5, Math.round((timeLeft / totalTime) * 10));
        score += earned;
        correctCount++;
        document.getElementById('scoreDisplay').textContent = 'Skor: ' + score;
    }

    showFeedback(isCorrect, correct);
    setTimeout(nextQuestion, 1800);
}

function showFeedback(correct, answer) {
    const box  = document.getElementById('feedbackBox');
    const text = document.getElementById('feedbackText');
    const ans  = document.getElementById('correctAnswer');
    box.classList.remove('d-none');
    if (correct) {
        text.innerHTML = '✅ Betul!';
        text.style.color = '#059669';
    } else {
        text.innerHTML = '❌ Salah!';
        text.style.color = '#DC2626';
    }
    ans.textContent  = correct ? '' : 'Jawaban: ' + answer;
}

function nextQuestion() {
    currentQ++;
    renderQuestion();
}

async function showResult() {
    clearInterval(timer);
    const duration = Math.round((Date.now() - startTime) / 1000);

    document.getElementById('quizBox').style.display   = 'none';
    document.getElementById('resultBox').style.display = 'block';
    document.getElementById('finalScore').textContent  = score;
    document.getElementById('finalMax').textContent    = `/ ${maxScore}`;
    document.getElementById('resultDetail').textContent =
        `Benar: ${correctCount}/${questions.length} · Waktu: ${duration} detik`;

    const pct = Math.round((correctCount / questions.length) * 100);
    let msg = pct >= 80 ? '🏆 Luar biasa!' : pct >= 60 ? '👍 Bagus!' : pct >= 40 ? '😊 Lumayan!' : '💪 Terus berlatih!';
    document.getElementById('resultMsg').textContent = msg;

    try {
        await fetch('{{ route("kuis.result") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({
                score, total_questions: questions.length,
                correct_answers: correctCount, duration_seconds: duration
            })
        });
    } catch(e) { console.warn('Gagal simpan hasil:', e); }
}
</script>
@endpush
