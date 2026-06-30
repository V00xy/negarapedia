@extends('layouts.app')
@section('title', 'AI Chat Negara')

@push('styles')
<style>
    #chatContainer {
        display: flex; flex-direction: column; height: calc(100vh - 200px);
        background: #fff; border-radius: var(--radius);
        box-shadow: var(--card-shadow); overflow: hidden;
    }
    #chatHeader {
        background: linear-gradient(135deg, #0F2B4B 0%, #1A3F6A 50%, #1E4D7A 100%);
        color: #fff; padding: 14px 20px;
        display: flex; align-items: center; justify-content: space-between;
    }
    #chatHeader .ai-info { display: flex; align-items: center; gap: 12px; }
    #chatHeader .ai-avatar {
        width: 40px; height: 40px;
        background: rgba(255,255,255,.15);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
    }
    #chatHeader h5 { margin: 0; font-weight: 700; font-size: 1rem; }
    #chatHeader .status {
        display: flex; align-items: center; gap: 5px;
        font-size: .75rem; color: rgba(255,255,255,.7);
    }
    #chatHeader .status-dot {
        width: 8px; height: 8px; background: #22C55E;
        border-radius: 50%; display: inline-block;
        animation: pulse-dot 2s infinite;
    }
    @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.4} }

    #chatMessages {
        flex: 1; overflow-y: auto; padding: 20px;
        background: #F8FAFC;
        scroll-behavior: smooth;
    }
    .msg {
        display: flex; gap: 10px; margin-bottom: 20px;
        animation: msgIn .35s ease-out;
    }
    @keyframes msgIn { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
    .msg-avatar {
        width: 32px; height: 32px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: .9rem; flex-shrink: 0;
        background: linear-gradient(135deg, #0F2B4B, #1A3F6A);
        color: #fff;
    }
    .msg-user { flex-direction: row-reverse; }
    .msg-user .msg-avatar { background: linear-gradient(135deg, #F5A623, #E89820); }
    .msg-bubble {
        max-width: 75%; padding: 12px 16px;
        border-radius: 12px; line-height: 1.6; font-size: .93rem;
        position: relative;
    }
    .msg-ai .msg-bubble {
        background: #fff; color: #1a2332;
        border: 1px solid #E2E8F0;
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .msg-user .msg-bubble {
        background: linear-gradient(135deg, #0F2B4B, #1A3F6A);
        color: #fff;
        border-bottom-right-radius: 4px;
    }
    .msg-user .msg-bubble a { color: #F5A623; }
    .msg-time {
        font-size: .65rem; margin-top: 6px;
        display: flex; align-items: center; gap: 8px;
    }
    .msg-ai .msg-time { color: #94A3B8; }
    .msg-user .msg-time { color: rgba(255,255,255,.5); justify-content: flex-end; }

    .msg-ai .msg-bubble p { margin-bottom: 6px; }
    .msg-ai .msg-bubble p:last-child { margin-bottom: 0; }
    .msg-ai .msg-bubble ul, .msg-ai .msg-bubble ol { margin-bottom: 4px; padding-left: 20px; }
    .msg-ai .msg-bubble strong { color: #0F2B4B; }
    .msg-ai .msg-bubble code {
        background: #F1F5F9; padding: 1px 5px; border-radius: 4px;
        font-size: .85rem; color: #DC2626;
    }

    .copy-btn {
        background: none; border: 1px solid #E2E8F0;
        border-radius: 6px; padding: 2px 8px;
        font-size: .65rem; color: #94A3B8;
        cursor: pointer; transition: all .2s;
        opacity: 0; margin-left: auto;
    }
    .msg-ai:hover .copy-btn { opacity: 1; }
    .copy-btn:hover { background: #F1F5F9; color: #0F2B4B; }

    .typing-indicator {
        display: flex; gap: 10px; margin-bottom: 20px;
        animation: msgIn .35s ease-out; align-items: flex-end;
    }
    .typing-indicator .msg-avatar {
        width: 32px; height: 32px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: .9rem; flex-shrink: 0;
        background: linear-gradient(135deg, #0F2B4B, #1A3F6A);
        color: #fff;
    }
    .typing-dots {
        background: #fff; border: 1px solid #E2E8F0;
        border-radius: 12px; border-bottom-left-radius: 4px;
        padding: 14px 20px; display: flex; gap: 5px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .typing-dots span {
        width: 8px; height: 8px; background: #94A3B8;
        border-radius: 50%; display: inline-block;
        animation: typingBounce 1.4s infinite ease-in-out;
    }
    .typing-dots span:nth-child(2) { animation-delay: .2s; }
    .typing-dots span:nth-child(3) { animation-delay: .4s; }
    @keyframes typingBounce { 0%,80%,100%{transform:scale(.6)} 40%{transform:scale(1)} }

    #chatInputArea {
        padding: 16px 20px; border-top: 1px solid #E2E8F0;
        background: #fff;
    }
    .suggestion-chips {
        display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px;
    }
    .suggestion-chips .chip {
        background: #F1F5F9; border: 1px solid #E2E8F0;
        border-radius: 20px; padding: 5px 14px;
        font-size: .78rem; cursor: pointer;
        transition: all .2s; color: #475569;
        font-weight: 500; white-space: nowrap;
    }
    .suggestion-chips .chip:hover {
        background: #0F2B4B; color: #fff; border-color: #0F2B4B;
    }
    #chatInput {
        border-radius: 10px; resize: none; border: 2px solid #E2E8F0;
        transition: border-color .2s; font-size: .93rem;
        padding: 10px 14px;
    }
    #chatInput:focus {
        border-color: #0F2B4B;
        box-shadow: 0 0 0 3px rgba(15,43,75,.1);
    }
    #btnSend {
        border-radius: 10px; padding: .5rem 1.2rem;
        background: linear-gradient(135deg, #0F2B4B, #1A3F6A);
        border: none; font-weight: 600;
        transition: all .25s; display: flex; align-items: center; gap: 6px;
    }
    #btnSend:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(15,43,75,.3); }
    #btnSend:disabled { opacity: .6; transform: none; }

    .welcome-msg { text-align: center; padding: 40px 20px 20px; }
    .welcome-msg .welc-icon {
        width: 64px; height: 64px; margin: 0 auto 16px;
        background: linear-gradient(135deg, #EDE9FE, #C4B5FD);
        border-radius: 16px; display: flex; align-items: center; justify-content: center;
        font-size: 2rem;
    }
    .welcome-msg h5 { font-weight: 700; color: #0F2B4B; }
    .welcome-msg p { color: #64748B; font-size: .9rem; max-width: 400px; margin: 0 auto; }

    @media(max-width:767px){
        #chatContainer { height: calc(100vh - 180px); border-radius: 0; }
        .msg-bubble { max-width: 90%; }
        .suggestion-chips { overflow-x: auto; flex-wrap: nowrap; padding-bottom: 4px; }
        .suggestion-chips .chip { flex-shrink: 0; }
    }
</style>
@endpush

@section('content')
<div id="chatContainer">
    <div id="chatHeader">
        <div class="ai-info">
            <div class="ai-avatar">🌍</div>
            <div>
                <h5>AI NegaraPedia</h5>
                <div class="status">
                    <span class="status-dot"></span>
                    <span>Online &middot; Groq AI + RestCountries</span>
                </div>
            </div>
        </div>
        <button class="btn btn-sm" style="background:rgba(255,255,255,.1);color:#fff;border-radius:8px;"
                onclick="clearChat()" title="Hapus percakapan">
            <i class="bi bi-trash"></i>
        </button>
    </div>

    <div id="chatMessages">
        <div class="welcome-msg" id="welcomeMsg">
            <div class="welc-icon">🌏</div>
            <h5>Selamat datang di AI NegaraPedia! 👋</h5>
            <p>Tanya apa aja tentang negara-negara di dunia. AI akan jawab dengan data dari RestCountries API.</p>
            <p class="small text-muted mt-2">
                💡 Contoh: <em>"Ceritakan tentang Jepang"</em> &middot;
                <em>"Bandingkan Indonesia dan Malaysia"</em> &middot;
                <em>"Apa ibu kota Brazil?"</em>
            </p>
        </div>
    </div>

    <div id="chatInputArea">
        <div class="suggestion-chips" id="chipContainer">
            <button class="chip" onclick="sendChip('Ceritakan tentang Indonesia')">🇮🇩 Indonesia</button>
            <button class="chip" onclick="sendChip('Apa ibu kota Jepang?')">🗼 Ibu Kota Jepang</button>
            <button class="chip" onclick="sendChip('Negara dengan populasi terbesar?')">👥 Populasi</button>
            <button class="chip" onclick="sendChip('Bandingkan Indonesia dan Malaysia')">⚖️ Indonesia vs Malaysia</button>
            <button class="chip" onclick="sendChip('Negara di Eropa yang terkenal?')">🌍 Eropa</button>
        </div>
        <div class="input-group">
            <textarea id="chatInput" class="form-control" rows="1"
                      placeholder="Ketik pertanyaan tentang negara..." style="min-height:46px;"
                      onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage()}"></textarea>
            <button id="btnSend">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;
const chatMessages = document.getElementById('chatMessages');
const chatInput = document.getElementById('chatInput');
const btnSend = document.getElementById('btnSend');
const welcomeMsg = document.getElementById('welcomeMsg');
let messageHistory = [];

function sendChip(text) {
    chatInput.value = text;
    sendMessage();
}

async function sendMessage() {
    const msg = chatInput.value.trim();
    if (!msg) return;

    chatInput.value = '';
    chatInput.style.height = 'auto';

    welcomeMsg?.remove();
    appendMessage('user', msg);
    messageHistory.push({ role: 'user', content: msg });

    btnSend.disabled = true;
    btnSend.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    const typingEl = showTyping();

    try {
        const res = await fetch('{{ route("ai-chat.chat") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                message: msg,
                history: messageHistory.slice(-20)
            })
        });

        const data = await res.json();
        typingEl.remove();

        if (!res.ok) {
            appendMessage('ai', '❌ ' + (data.error || 'Terjadi kesalahan. Coba lagi.'));
            return;
        }

        appendMessage('ai', data.reply);
        messageHistory.push({ role: 'assistant', content: data.reply });
    } catch (err) {
        typingEl.remove();
        appendMessage('ai', '❌ Koneksi gagal. Periksa internet kamu dan coba lagi.');
    } finally {
        btnSend.disabled = false;
        btnSend.innerHTML = '<i class="bi bi-send-fill"></i>';
    }
}

function appendMessage(role, content) {
    const div = document.createElement('div');
    div.className = `msg msg-${role}`;

    const avatar = document.createElement('div');
    avatar.className = 'msg-avatar';
    avatar.textContent = role === 'ai' ? '🌍' : '👤';

    const bubble = document.createElement('div');
    bubble.className = 'msg-bubble';

    if (role === 'ai') {
        const textWrapper = document.createElement('div');
        textWrapper.innerHTML = formatAIResponse(content);
        bubble.appendChild(textWrapper);

        const time = document.createElement('div');
        time.className = 'msg-time';
        time.innerHTML = `<span>${getTimeLabel()}</span>`;
        const copyBtn = document.createElement('button');
        copyBtn.className = 'copy-btn';
        copyBtn.innerHTML = '<i class="bi bi-clipboard"></i> Salin';
        copyBtn.onclick = function(e) {
            e.stopPropagation();
            copyToClipboard(content);
        };
        time.appendChild(copyBtn);
        bubble.appendChild(time);
    } else {
        bubble.textContent = content;
        const time = document.createElement('div');
        time.className = 'msg-time';
        time.textContent = getTimeLabel();
        bubble.appendChild(time);
    }

    div.appendChild(avatar);
    div.appendChild(bubble);

    chatMessages.appendChild(div);
    scrollToBottom();
}

function formatAIResponse(text) {
    let html = text
        .replace(/### (.+)/g, '<h6 class="fw-bold mt-2 mb-1" style="color:#0F2B4B;">$1</h6>')
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.+?)\*/g, '<em>$1</em>')
        .replace(/```(\w*)\n([\s\S]*?)```/g, '<pre style="background:#F1F5F9;padding:10px;border-radius:6px;font-size:.85rem;overflow-x:auto;">$2</pre>')
        .replace(/`([^`]+)`/g, '<code>$1</code>');

    // Convert markdown lists
    const lines = html.split('\n');
    let result = [];
    let inList = false;

    for (let i = 0; i < lines.length; i++) {
        const line = lines[i];
        const listMatch = line.match(/^(\s*)[-*]\s(.+)/);
        if (listMatch) {
            if (!inList) { result.push('<ul>'); inList = true; }
            result.push(`<li>${listMatch[2]}</li>`);
        } else {
            if (inList) { result.push('</ul>'); inList = false; }
            // Handle numbered lists
            const numMatch = line.match(/^(\s*)\d+\.\s(.+)/);
            if (numMatch) {
                result.push(`<li style="list-style:decimal;margin-left:20px;">${numMatch[2]}</li>`);
            } else {
                result.push(line ? `<p>${line}</p>` : '');
            }
        }
    }
    if (inList) result.push('</ul>');

    return result.join('\n');
}

function showTyping() {
    const div = document.createElement('div');
    div.className = 'typing-indicator';
    div.innerHTML = `
        <div class="msg-avatar">🌍</div>
        <div class="typing-dots">
            <span></span><span></span><span></span>
        </div>
    `;
    chatMessages.appendChild(div);
    scrollToBottom();
    return div;
}

function getTimeLabel() {
    return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

function scrollToBottom() {
    setTimeout(() => { chatMessages.scrollTop = chatMessages.scrollHeight; }, 50);
}

function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).catch(() => fallbackCopy(text));
    } else {
        fallbackCopy(text);
    }
}

function fallbackCopy(text) {
    const ta = document.createElement('textarea');
    ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
    document.body.appendChild(ta); ta.select();
    try { document.execCommand('copy'); } catch(e) {}
    document.body.removeChild(ta);
}

function clearChat() {
    if (!confirm('Hapus semua percakapan?')) return;
    chatMessages.innerHTML = `
        <div class="welcome-msg" id="welcomeMsg">
            <div class="welc-icon">🌏</div>
            <h5>Percakapan baru dimulai ✨</h5>
            <p>Tanya apa aja tentang negara-negara di dunia.</p>
        </div>
    `;
    messageHistory = [];
    scrollToBottom();
}

// Auto-resize textarea
chatInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});
</script>
@endpush
