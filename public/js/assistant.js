/* ============================================================
   ASSISTANT.JS — Si Meung AI Assistant
   ============================================================ */

(function () {
  'use strict';

  // ── DOM Elements ────────────────────────
  const widget      = document.getElementById('assistantWidget');
  const toggleBtn   = document.getElementById('assistantToggle');
  const panel       = document.getElementById('assistantPanel');
  const closeBtn    = document.getElementById('assistantClose');
  const messages    = document.getElementById('assistantMessages');
  const suggestions = document.getElementById('assistantSuggestions');
  const input       = document.getElementById('assistantInput');
  const sendBtn     = document.getElementById('assistantSend');
  const badge       = document.getElementById('assistantBadge');

  if (!widget || !toggleBtn) return;

  // ── State ────────────────────────────────
  let faqs        = [];
  let isOpen      = false;
  let greeting    = '';
  let villageName = '';
  let ttsUtterance = null;
  let hasGreeted  = false;

  // ── Fallback FAQ data ────────────────────
  const FALLBACK_FAQS = [
    {
      q: 'Apa itu Gampong Muenye Pirak?',
      a: 'Gampong Muenye Pirak adalah sebuah desa yang terletak di Kecamatan Nisam, Kabupaten Aceh Utara, Provinsi Aceh. Kami berkomitmen memberikan pelayanan terbaik bagi seluruh warga.',
      keywords: ['gampong', 'desa', 'muenye', 'pirak', 'tentang', 'apa', 'informasi']
    },
    {
      q: 'Dimana lokasi kantor gampong?',
      a: 'Kantor Gampong Muenye Pirak berlokasi di Kecamatan Nisam, Kabupaten Aceh Utara. Silahkan cek halaman Kontak kami untuk informasi alamat lengkap dan peta lokasi.',
      keywords: ['lokasi', 'kantor', 'alamat', 'dimana', 'letak', 'tempat', 'maps']
    },
    {
      q: 'Bagaimana cara menghubungi gampong?',
      a: 'Anda dapat menghubungi kami melalui:\n• Formulir kontak di halaman Kontak\n• WhatsApp (nomor tersedia di footer)\n• Telepon ke kantor gampong\n• Kunjungi langsung ke kantor kami pada jam kerja.',
      keywords: ['hubungi', 'kontak', 'telepon', 'whatsapp', 'wa', 'email', 'komunikasi']
    },
    {
      q: 'Apa saja berita terbaru gampong?',
      a: 'Untuk membaca berita dan informasi terkini dari gampong, kunjungi halaman Berita kami. Kami rutin memperbarui informasi kegiatan dan pengumuman penting.',
      keywords: ['berita', 'informasi', 'pengumuman', 'kabar', 'terkini', 'terbaru', 'kegiatan']
    },
    {
      q: 'Bagaimana cara melihat galeri foto?',
      a: 'Galeri foto dan dokumentasi kegiatan gampong dapat dilihat di halaman Galeri. Di sana Anda dapat menikmati koleksi foto-foto kegiatan dan keindahan gampong kami.',
      keywords: ['galeri', 'foto', 'gambar', 'dokumentasi', 'gambar', 'lihat']
    },
    {
      q: 'Apa visi gampong?',
      a: 'Visi dan misi Gampong Muenye Pirak dapat dilihat di halaman Profil > Visi & Misi. Kami berkomitmen membangun gampong yang maju, sejahtera, dan berbudaya.',
      keywords: ['visi', 'misi', 'tujuan', 'cita', 'harapan', 'program']
    },
    {
      q: 'Siapa perangkat gampong?',
      a: 'Informasi tentang struktur organisasi dan perangkat gampong dapat dilihat di halaman Profil > Struktur Organisasi.',
      keywords: ['perangkat', 'struktur', 'organisasi', 'aparatur', 'kepala', 'geuchik', 'sekretaris', 'tuha peut']
    }
  ];

  // ── Boot ─────────────────────────────────
  loadFAQs();

  // ── Load FAQs from API ───────────────────
  async function loadFAQs() {
    try {
      const baseUrl = (window.BASE_URL || window.location.origin).replace(/\/$/, '');
      const res  = await fetch(`${baseUrl}/api/assistant`, { signal: AbortSignal.timeout(5000) });
      const data = await res.json();
      faqs        = data.faqs        || FALLBACK_FAQS;
      greeting    = data.greeting    || '';
      villageName = data.village_name || '';
    } catch (_) {
      faqs = FALLBACK_FAQS;
    }
    buildSuggestions();
  }

  // ── Toggle ───────────────────────────────
  toggleBtn.addEventListener('click', () => {
    isOpen ? closePanel() : openPanel();
  });
  closeBtn?.addEventListener('click', closePanel);
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && isOpen) closePanel(); });

  function openPanel() {
    isOpen = true;
    panel.classList.add('is-open');
    panel.setAttribute('aria-hidden', 'false');
    toggleBtn.setAttribute('aria-expanded', 'true');
    input?.focus();
    if (badge) badge.hidden = true;

    if (!hasGreeted) {
      hasGreeted = true;
      setTimeout(() => sendBotMessage(
        greeting || `Halo! Saya Si Meung 🦉, asisten Gampong ${villageName || 'Muenye Pirak'}. Ada yang bisa saya bantu?`
      ), 400);
    }
  }

  function closePanel() {
    isOpen = false;
    panel.classList.remove('is-open');
    panel.setAttribute('aria-hidden', 'true');
    toggleBtn.setAttribute('aria-expanded', 'false');
    stopTTS();
  }

  // ── Send Message ─────────────────────────
  sendBtn?.addEventListener('click', handleSend);
  input?.addEventListener('keydown', e => { if (e.key === 'Enter') handleSend(); });

  function handleSend() {
    const text = input?.value.trim();
    if (!text) return;
    input.value = '';
    appendUserMsg(text);
    showTyping();
    setTimeout(() => {
      removeTyping();
      const answer = findAnswer(text);
      sendBotMessage(answer);
    }, 800 + Math.random() * 500);
  }

  // ── Keyword Matching ──────────────────────
  function findAnswer(query) {
    const q = query.toLowerCase().replace(/[^a-z0-9\s]/g, '');
    const words = q.split(/\s+/).filter(w => w.length > 1);

    let bestFaq = null;
    let bestScore = 0;

    faqs.forEach(faq => {
      let score = 0;
      const keywords = (faq.keywords || []).map(k => k.toLowerCase());
      words.forEach(w => {
        if (keywords.some(k => k.includes(w) || w.includes(k))) score++;
      });
      if (score > bestScore) { bestScore = score; bestFaq = faq; }
    });

    if (bestScore > 0 && bestFaq) return bestFaq.a;

    // Generic fallbacks
    if (/halo|hai|hi|hello|selamat/i.test(query)) {
      return `Halo juga! Saya Si Meung 🦉, siap membantu informasi tentang Gampong ${villageName || 'Muenye Pirak'}. Apa yang ingin Anda ketahui?`;
    }
    if (/terima kasih|makasih|thanks/i.test(query)) {
      return 'Sama-sama! Senang bisa membantu. Ada pertanyaan lain? 😊';
    }

    return `Maaf, saya belum memiliki informasi tentang itu. Silahkan hubungi kami langsung melalui halaman <a href="/kontak" style="color:var(--primary)">Kontak</a> atau WhatsApp untuk bantuan lebih lanjut.`;
  }

  // ── Message Rendering ─────────────────────
  function appendUserMsg(text) {
    const div = document.createElement('div');
    div.className = 'chat-msg user';
    div.innerHTML = `<div class="chat-bubble">${escapeHtml(text)}</div>`;
    messages?.appendChild(div);
    scrollToBottom();
  }

  function sendBotMessage(text) {
    const div = document.createElement('div');
    div.className = 'chat-msg bot';
    const msgId = 'msg-' + Date.now();
    div.innerHTML = `
      <div class="chat-msg-avatar">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" aria-hidden="true">
          <ellipse cx="24" cy="30" rx="14" ry="15" fill="#1B3A6B"/>
          <ellipse cx="24" cy="24" rx="12" ry="13" fill="#2D5AA0"/>
          <circle cx="19" cy="20" r="4.5" fill="white"/>
          <circle cx="29" cy="20" r="4.5" fill="white"/>
          <circle cx="19" cy="20" r="2.5" fill="#C9870C"/>
          <circle cx="29" cy="20" r="2.5" fill="#C9870C"/>
          <circle cx="19.8" cy="19.2" r="1.2" fill="#0D1B2A"/>
          <circle cx="29.8" cy="19.2" r="1.2" fill="#0D1B2A"/>
          <path d="M22 24 L24 27 L26 24 Q24 22.5 22 24Z" fill="#C9870C"/>
        </svg>
      </div>
      <div class="chat-bubble" id="${msgId}">
        ${text}
        <button class="chat-tts-btn" aria-label="Bacakan" data-msg-id="${msgId}" title="Dengarkan">
          <i class="bi bi-volume-up-fill"></i>
        </button>
      </div>`;
    messages?.appendChild(div);
    scrollToBottom();

    // TTS button listener
    div.querySelector('.chat-tts-btn')?.addEventListener('click', function () {
      const bubble = document.getElementById(msgId);
      const plainText = bubble ? bubble.innerText.replace(/[🦉😊]/g, '') : text;
      toggleTTS(plainText, this);
    });
  }

  // ── Typing Indicator ──────────────────────
  function showTyping() {
    const div = document.createElement('div');
    div.className = 'chat-msg bot';
    div.id = 'typing-indicator';
    div.innerHTML = `
      <div class="chat-msg-avatar">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" aria-hidden="true" width="18" height="18">
          <ellipse cx="24" cy="24" rx="12" ry="13" fill="#1a7a45"/>
          <circle cx="19" cy="20" r="3" fill="#C9870C"/>
          <circle cx="29" cy="20" r="3" fill="#C9870C"/>
        </svg>
      </div>
      <div class="typing-indicator">
        <span></span><span></span><span></span>
      </div>`;
    messages?.appendChild(div);
    scrollToBottom();
  }
  function removeTyping() {
    document.getElementById('typing-indicator')?.remove();
  }

  // ── Quick Suggestions ─────────────────────
  function buildSuggestions() {
    if (!suggestions) return;
    suggestions.innerHTML = '';
    const chips = faqs.slice(0, 4);
    chips.forEach(faq => {
      const btn = document.createElement('button');
      btn.className = 'suggestion-chip';
      btn.textContent = faq.q.length > 32 ? faq.q.substring(0, 32) + '…' : faq.q;
      btn.addEventListener('click', () => {
        if (!isOpen) openPanel();
        appendUserMsg(faq.q);
        showTyping();
        setTimeout(() => { removeTyping(); sendBotMessage(faq.a); }, 700);
        suggestions.innerHTML = '';
      });
      suggestions.appendChild(btn);
    });
  }

  // ── TTS ───────────────────────────────────
  function toggleTTS(text, btn) {
    if (ttsUtterance && speechSynthesis.speaking) {
      speechSynthesis.cancel();
      ttsUtterance = null;
      document.querySelectorAll('.chat-tts-btn').forEach(b => {
        b.classList.remove('speaking');
        b.innerHTML = '<i class="bi bi-volume-up-fill"></i>';
      });
      if (btn.dataset.active) return;
    }

    if (!('speechSynthesis' in window)) {
      alert('Browser Anda tidak mendukung fitur Text-to-Speech.');
      return;
    }

    ttsUtterance = new SpeechSynthesisUtterance(text);
    ttsUtterance.lang  = 'id-ID';
    ttsUtterance.rate  = 0.88;
    ttsUtterance.pitch = 1.05;
    ttsUtterance.volume = 0.95;
    // Prefer Indonesian voice if available
    const voices = speechSynthesis.getVoices();
    const idVoice = voices.find(v => v.lang.startsWith('id')) || voices.find(v => v.lang.startsWith('en'));
    if (idVoice) ttsUtterance.voice = idVoice;

    ttsUtterance.onstart = () => {
      btn.classList.add('speaking');
      btn.innerHTML = '<i class="bi bi-stop-fill"></i>';
      btn.dataset.active = '1';
    };
    ttsUtterance.onend = () => {
      btn.classList.remove('speaking');
      btn.innerHTML = '<i class="bi bi-volume-up-fill"></i>';
      delete btn.dataset.active;
      ttsUtterance = null;
    };
    ttsUtterance.onerror = () => {
      btn.classList.remove('speaking');
      btn.innerHTML = '<i class="bi bi-volume-up-fill"></i>';
      ttsUtterance = null;
    };

    speechSynthesis.speak(ttsUtterance);
  }

  function stopTTS() {
    if (speechSynthesis.speaking) {
      speechSynthesis.cancel();
      ttsUtterance = null;
      document.querySelectorAll('.chat-tts-btn').forEach(b => {
        b.classList.remove('speaking');
        b.innerHTML = '<i class="bi bi-volume-up-fill"></i>';
      });
    }
  }

  // ── Helpers ───────────────────────────────
  function scrollToBottom() {
    if (messages) messages.scrollTop = messages.scrollHeight;
  }

  function escapeHtml(text) {
    const d = document.createElement('div');
    d.textContent = text;
    return d.innerHTML;
  }
})();
