@extends('layouts.app')
@section('title', 'Kuis Tebak Bendera')

@push('styles')
<style>
    #quizBox { display: none; }
    #resultBox { display: none; }
    .option-btn {
        width: 100%; padding: 12px; margin: 6px 0;
        border: 2px solid #dee2e6; border-radius: 8px;
        background: #fff; font-size: 1rem; cursor: pointer;
        transition: all .2s; text-align: left;
    }
    .option-btn:hover { border-color: #1a3c6e; background: #f0f4ff; }
    .option-btn.correct { border-color: #198754; background: #d1e7dd; color: #0a3622; }
    .option-btn.wrong   { border-color: #dc3545; background: #f8d7da; color: #58151c; }
    .option-btn:disabled { cursor: not-allowed; }
    .flag-quiz { max-height: 200px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,.15); }
    #progressBar { height: 10px; border-radius: 5px; transition: width .4s; }
    #timerBar { height: 6px; border-radius: 3px; transition: width 1s linear; }
    .score-circle {
        width: 120px; height: 120px; border-radius: 50%;
        background: #1a3c6e; color: #fff;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        margin: 0 auto;
    }
</style>
@endpush

@section('content')
<h4 class="fw-bold mb-1">🏳️ Kuis Tebak Bendera</h4>
<p class="text-muted mb-3">Tebak nama negara dari benderanya! 10 soal, tiap soal 15 detik. Data diambil dari RestCountries API.</p>

<div id="startBox" class="card text-center py-5">
    <div style="font-size:5rem">🌍</div>
    <h4 class="mt-3 fw-bold">Siap Mulai Kuis?</h4>
    <p class="text-muted">10 soal · 15 detik per soal · Skor maks 100</p>
    <button class="btn btn-primary btn-lg mx-auto px-5" onclick="startQuiz()" id="btnStart">
        <i class="bi bi-play-fill"></i> Mulai Kuis
    </button>
</div>

<div id="quizBox">
    <div class="card mb-3 p-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="fw-semibold">Soal <span id="qNum">1</span> / <span id="qTotal">10</span></span>
            <span class="badge bg-primary" id="scoreDisplay">Skor: 0</span>
            <span class="fw-semibold text-danger" id="timerText">⏱ 15</span>
        </div>
        <div class="bg-light rounded overflow-hidden mb-1">
            <div id="progressBar" class="bg-primary" style="width:0%"></div>
        </div>
        <div class="bg-light rounded overflow-hidden">
            <div id="timerBar" class="bg-danger" style="width:100%"></div>
        </div>
    </div>

    <div class="card p-4 text-center">
        <p class="text-muted mb-3">Negara manakah yang memiliki bendera ini?</p>
        <img id="flagImg" src="" alt="Bendera" class="flag-quiz mb-4 mx-auto d-block">
        <div id="optionsBox"></div>
        <div id="feedbackBox" class="mt-3 d-none">
            <div id="feedbackText" class="fw-semibold fs-5"></div>
            <div id="correctAnswer" class="text-muted small"></div>
        </div>
    </div>
</div>

<div id="resultBox" class="card text-center py-5 px-4">
    <h4 class="fw-bold mb-4">🎉 Kuis Selesai!</h4>
    <div class="score-circle mb-3">
        <div class="fs-2 fw-bold" id="finalScore">0</div>
        <div class="small">/ 100</div>
    </div>
    <div class="mt-3 mb-1" id="resultMsg"></div>
    <div class="text-muted small mb-4" id="resultDetail"></div>

    <div class="d-flex gap-3 justify-content-center">
        <button class="btn btn-primary" onclick="startQuiz()">
            <i class="bi bi-arrow-repeat"></i> Main Lagi
        </button>
        <a href="{{ route('leaderboard.index') }}" class="btn btn-outline-warning">
            <i class="bi bi-trophy"></i> Lihat Leaderboard
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

let questions = [], currentQ = 0, score = 0, correctCount = 0;
let timer = null, timeLeft = 15, startTime = null;
let answered = false;

async function startQuiz() {
    const btn = document.getElementById('btnStart');
    if(btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memuat...'; }

    try {
        const res = await fetch('{{ route("kuis.questions") }}', { headers: {'X-Requested-With':'XMLHttpRequest'} });
        const data = await res.json();

        if (!res.ok || data.error) {
            alert(data.error || 'Gagal memuat soal.');
            if(btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-play-fill"></i> Mulai Kuis'; }
            return;
        }

        questions   = data.questions;
        currentQ    = 0;
        score       = 0;
        correctCount = 0;
        startTime   = Date.now();

        document.getElementById('startBox').style.display  = 'none';
        document.getElementById('resultBox').style.display = 'none';
        document.getElementById('quizBox').style.display   = 'block';

        renderQuestion();
    } catch(err) {
        alert('Koneksi gagal: ' + err.message);
        if(btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-play-fill"></i> Mulai Kuis'; }
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
    timeLeft = 15;
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
    document.getElementById('timerBar').style.width = ((timeLeft / 15) * 100) + '%';
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
        const earned = Math.max(5, Math.round((timeLeft / 15) * 10));
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
    text.textContent = correct ? '✅ Betul!' : '❌ Salah!';
    text.style.color = correct ? '#198754' : '#dc3545';
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
    document.getElementById('resultDetail').textContent =
        `Benar: ${correctCount}/${questions.length} · Waktu: ${duration} detik`;

    const pct = Math.round((correctCount / questions.length) * 100);
    let msg = pct >= 80 ? '🏆 Luar biasa!' : pct >= 60 ? '👍 Bagus!' : pct >= 40 ? '😊 Lumayan!' : '💪 Terus berlatih!';
    document.getElementById('resultMsg').innerHTML = `<span class="fs-5">${msg}</span>`;

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