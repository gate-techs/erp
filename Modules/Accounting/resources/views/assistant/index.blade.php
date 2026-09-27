@extends('layouts/contentNavbarLayout')

@section('title', __('Smart Financial Assistant & Guide') . ' - ' . config('variables.templateName'))

@section('content')
<div class="ai-chat-app-container">
    
    <!-- Standard ERP Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="bx bx-bot text-primary fs-3"></i>
                <span>{{ __('المساعد المالي والمرشد الذكي') }}</span>
                <span class="badge bg-label-primary rounded-pill small fw-normal py-1 px-2" style="font-size: 0.72rem;">
                    <i class="bx bx-check-shield me-1 text-success"></i>GateTech AI
                </span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.78rem;">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('لوحة التحكم') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('accounting.dashboard') }}">{{ __('المحاسبة المالية') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('المساعد المالي الذكي') }}</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-label-success rounded-pill d-none d-sm-inline-flex align-items-center gap-1 py-2 px-3">
                <span class="ai-live-dot"></span>
                <span>{{ __('متصل بالنظام المالي لحظياً') }}</span>
            </span>
            <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 py-2 px-3 shadow-2xs" data-bs-toggle="offcanvas" data-bs-target="#offcanvasChatHistory" aria-controls="offcanvasChatHistory">
                <i class="bx bx-history fs-5"></i>
                <span>{{ __('سجل المحادثات') }}</span>
            </button>
        </div>
    </div>

    <!-- Main Full-Width Chat Card (بالعرض عادي) -->
    <div class="card border-0 shadow-sm ai-chat-card overflow-hidden d-flex flex-column">
        
        <!-- Top Toolbar Header Bar -->
        <div class="card-header bg-white border-bottom p-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                
                <!-- Left / Start: Bot Identity -->
                <div class="d-flex align-items-center gap-3">
                    <div class="position-relative flex-shrink-0">
                        <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm ai-avatar-glow">
                            <i class="bx bx-bot fs-3"></i>
                        </div>
                        <span class="ai-status-indicator"></span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="mb-0 fw-bold text-dark">{{ __('المساعد المالي والمحاسبي الذكي') }}</h6>
                            <span class="badge bg-label-success rounded-pill px-2 py-0" style="font-size: 0.68rem;">
                                {{ __('متصل ومباشر') }}
                            </span>
                        </div>
                        <small class="text-muted d-block" style="font-size: 0.74rem;">
                            {{ __('المساعد الذكي للعمليات المحاسبية والتقارير المالية المعتمدة') }}
                        </small>
                    </div>
                </div>

                <!-- Right / End: Fast Actions -->
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-sm btn-primary rounded-pill d-flex align-items-center gap-1 py-1 px-3 ai-btn-new-chat" id="btn-new-chat">
                        <i class="bx bx-plus-circle font-16"></i>
                        <span class="d-none d-sm-inline">{{ __('محادثة جديدة') }}</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-icon btn-outline-secondary rounded-circle" id="btn-export-chat" title="{{ __('تصدير المحادثة') }}">
                        <i class="bx bx-download"></i>
                    </button>
                </div>

            </div>
        </div>

        <!-- Suggested Chips Bar at the Top (أسئلة مقترحة في الأعلى) -->
        <div class="px-3 py-2 bg-light-subtle border-bottom" id="ai-suggestions-bar">
            <div class="d-flex align-items-center gap-2 overflow-auto py-1 no-scrollbar" id="ai-quick-prompts-list">
                <span class="text-muted small fw-semibold flex-shrink-0 d-flex align-items-center gap-1" style="font-size: 0.74rem;">
                    <i class="bx bx-sparkles text-warning"></i>
                    <span>{{ __('أسئلة مقترحة:') }}</span>
                </span>
                @foreach ($screenContext['suggestions'] ?? [] as $prompt)
                    <button type="button" class="btn btn-xs btn-outline-primary bg-white text-nowrap rounded-pill py-1 px-3 ai-prompt-chip" style="font-size: 0.74rem;">
                        {{ $prompt }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Financial Facts Bar (Compact) -->
        @if (!empty($financialFacts) && !isset($financialFacts['error']))
        <div class="bg-body border-bottom px-3 py-1 d-flex flex-wrap align-items-center justify-content-between gap-2" style="font-size: 0.75rem;">
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted d-flex align-items-center gap-1">
                    <i class="bx bx-pulse text-success"></i>
                    <strong class="text-dark">{{ __('السيولة النقدية المتاحة:') }}</strong>
                    <span class="fw-bold text-primary">{{ number_format($financialFacts['total_liquidity'] ?? 0, 2) }} {{ $financialFacts['currency'] ?? '' }}</span>
                </span>
                @if (($financialFacts['pending_actions']['draft_entries'] ?? 0) > 0)
                <span class="badge bg-label-warning rounded-pill py-0 px-2">
                    {{ $financialFacts['pending_actions']['draft_entries'] }} {{ __('قيود مسودة') }}
                </span>
                @endif
                @if (($financialFacts['pending_actions']['draft_recs'] ?? 0) > 0)
                <span class="badge bg-label-danger rounded-pill py-0 px-2">
                    {{ $financialFacts['pending_actions']['draft_recs'] }} {{ __('تسويات معلقة') }}
                </span>
                @endif
            </div>
            <span class="text-muted small d-none d-md-inline" style="font-size: 0.72rem;">
                <i class="bx bx-check-shield text-success me-1"></i>{{ __('بيانات مالية حية ومقيدة بالنظام') }}
            </span>
        </div>
        @endif

        <!-- Main Messages Stream Area (Full Width) -->
        <div class="flex-grow-1 overflow-auto p-4 ai-messages-feed" id="ai-chat-messages">
            
            <!-- Default Welcome Bot Bubble in Chat Flow -->
            <div id="ai-initial-bot-msg" class="d-flex align-items-start gap-2 mb-3">
                <div class="avatar avatar-xs bg-primary text-white rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center mt-1 shadow-2xs">
                    <i class="bx bx-bot"></i>
                </div>
                <div class="ai-msg-bubble-bot p-3 shadow-sm flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                        <span class="fw-bold small text-primary d-flex align-items-center gap-1" style="font-size: 0.76rem;">
                            <i class="bx bx-check-shield text-success"></i>GateTech Financial AI
                        </span>
                        <span class="text-muted" style="font-size: 0.65rem;">{{ __('الآن') }}</span>
                    </div>
                    
                    <div class="ai-markdown-content text-dark mb-0">
                        {{ __('مرحباً بك! أنا مساعدك المالي والمحاسبي الذكي لنظام GateTech ERP. يمكنك سؤالي عن أرصدة السيولة، فحص قيود اليومية، تقارير الأرباح والمصروفات، أو خطوات العمليات المحاسبية. كيف يمكنني خدمتك اليوم؟') }}
                    </div>
                </div>
            </div>

            <!-- Dynamic Chat Messages Container -->
            <div id="ai-chat-dynamic-flow" class="d-flex flex-column gap-3"></div>

            <!-- Thinking / Typing Indicator -->
            <div id="ai-thinking-indicator" class="d-none align-items-center gap-3 p-3 my-2 bg-white rounded-3 shadow-sm border" style="max-width: 440px;">
                <div class="avatar avatar-xs bg-primary text-white rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center">
                    <i class="bx bx-bot"></i>
                </div>
                <div class="d-flex flex-column gap-1">
                    <span class="small fw-semibold text-primary" style="font-size: 0.8rem;">
                        {{ __('جاري فحص السجلات المحاسبية وصياغة الإجابة...') }}
                    </span>
                    <div class="typing-wave">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modern Chat Composer Input Bar -->
        <div class="p-3 bg-white border-top ai-composer-wrap">
            <form id="ai-assistant-form" class="position-relative">
                <div class="d-flex align-items-end gap-2 p-2 rounded-4 border bg-body shadow-sm ai-input-box-focus">
                    
                    <!-- Voice Input Button (Speech to text) -->
                    <button type="button" 
                            id="btn-voice-input" 
                            class="btn btn-sm btn-icon text-muted rounded-circle flex-shrink-0 mb-1" 
                            title="{{ __('الإملاء الصوتي المباشر') }}">
                        <i class="bx bx-microphone fs-5"></i>
                    </button>

                    <!-- Expanding Textarea -->
                    <textarea id="ai-user-question-input" 
                              class="form-control border-0 bg-transparent shadow-none p-2 ai-textarea" 
                              rows="1" 
                              placeholder="{{ __('اكتب سؤالك أو استفسارك المحاسبي هنا...') }}" 
                              autocomplete="off" 
                              required></textarea>

                    <!-- Send Button -->
                    <button type="submit" 
                            id="btn-ai-send" 
                            class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center p-0 flex-shrink-0 ai-btn-send shadow-sm" 
                            title="{{ __('إرسال') }}">
                        <i class="bx bx-paper-plane fs-5"></i>
                    </button>
                </div>
            </form>

            <!-- Composer Helper Footer -->
            <div class="d-flex justify-content-between align-items-center mt-2 px-2">
                <span class="text-muted" style="font-size: 0.72rem;">
                    <i class="bx bx-shield-quarter me-1 text-success"></i>
                    {{ __('الإجابات مقيّدة بسجلات وبيانات النظام والقواعد المحاسبية المعتمدة.') }}
                </span>
                <span class="text-muted d-none d-sm-inline" style="font-size: 0.72rem;">
                    {{ __('اضغط Enter للإرسال، و Shift + Enter لسطر جديد') }}
                </span>
            </div>
        </div>

    </div>
</div>

<!-- Offcanvas Drawer for History -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasChatHistory" aria-labelledby="offcanvasChatHistoryLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title d-flex align-items-center gap-2" id="offcanvasChatHistoryLabel">
            <i class="bx bx-history text-primary fs-4"></i>
            <span>{{ __('سجل المحادثات') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0 d-flex flex-column h-100">
        
        <div class="p-3 border-bottom bg-light-subtle">
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">
                    {{ __('المحادثات السابقة') }}
                </span>
                <button type="button" class="btn btn-link btn-xs p-0 text-muted" id="btn-clear-all-sessions" style="font-size: 0.72rem;">
                    {{ __('مسح السجل') }}
                </button>
            </div>
        </div>

        <div class="flex-grow-1 overflow-auto p-3" id="ai-sessions-list-wrap">
            <div class="list-group list-group-flush" id="ai-sessions-list">
                <!-- Injected via JS -->
            </div>
        </div>

        @if (!empty($financialFacts) && !isset($financialFacts['error']))
        <div class="p-3 border-top bg-light-subtle">
            <div class="small fw-bold text-dark mb-2 d-flex align-items-center gap-1">
                <i class="bx bx-pulse text-success fs-5"></i>
                <span>{{ __('ملخص السيولة اللحظي') }}</span>
            </div>
            <div class="bg-white p-2 rounded-3 border small">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">{{ __('إجمالي السيولة') }}:</span>
                    <strong class="text-primary">{{ number_format($financialFacts['total_liquidity'] ?? 0, 2) }} {{ $financialFacts['currency'] ?? '' }}</strong>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

<style>
/* Chat Card Full-Width Styling */
.ai-chat-card {
    height: calc(100vh - 190px);
    min-height: 620px;
    background: #fff;
    border-radius: 16px !important;
}

.ai-avatar-glow {
    background: linear-gradient(135deg, var(--bs-primary) 0%, #3b82f6 100%);
    box-shadow: 0 4px 14px rgba(var(--bs-primary-rgb), 0.35);
}

.ai-status-indicator {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 11px;
    height: 11px;
    background-color: #10b981;
    border: 2px solid #fff;
    border-radius: 50%;
}

.ai-live-dot {
    width: 8px;
    height: 8px;
    background-color: #10b981;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: aiDotPulse 2s infinite;
}

@keyframes aiDotPulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1.1); box-shadow: 0 0 0 5px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.ai-btn-new-chat {
    background: linear-gradient(135deg, var(--bs-primary) 0%, #4f46e5 100%) !important;
    border: none;
    transition: all 0.25s ease;
}

.ai-btn-new-chat:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(var(--bs-primary-rgb), 0.3) !important;
}

/* Messages Stream */
.ai-messages-feed {
    background: #f8fafc;
    scroll-behavior: smooth;
}

.ai-msg-bubble-user {
    background: linear-gradient(135deg, var(--bs-primary) 0%, #4338ca 100%);
    color: #ffffff;
    border-radius: 16px 16px 4px 16px;
    box-shadow: 0 2px 8px rgba(var(--bs-primary-rgb), 0.2);
    max-width: 78%;
}

.ai-msg-bubble-bot {
    background: #ffffff;
    color: #1e293b;
    border-radius: 16px 16px 16px 4px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(0, 0, 0, 0.06);
    max-width: 88%;
}

[data-bs-theme="dark"] .ai-msg-bubble-bot {
    background: #1e293b;
    color: #f1f5f9;
    border-color: #334155;
}

.ai-prompt-chip {
    transition: all 0.2s ease;
    border-color: rgba(var(--bs-primary-rgb), 0.3);
}

.ai-prompt-chip:hover {
    background-color: var(--bs-primary) !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}

/* Typing Indicator Animation */
.typing-wave {
    display: flex;
    align-items: center;
    gap: 4px;
}

.typing-wave span {
    width: 6px;
    height: 6px;
    background: var(--bs-primary);
    border-radius: 50%;
    animation: typingBlink 1.4s infinite both;
}

.typing-wave span:nth-child(2) { animation-delay: 0.2s; }
.typing-wave span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typingBlink {
    0%, 80%, 100% { opacity: 0.2; transform: scale(0.8); }
    40% { opacity: 1; transform: scale(1.2); }
}

/* Modern Composer */
.ai-textarea {
    resize: none;
    max-height: 140px;
    font-size: 0.88rem;
    line-height: 1.45;
}

.ai-input-box-focus:focus-within {
    border-color: var(--bs-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--bs-primary-rgb), 0.15) !important;
}

.ai-btn-send {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--bs-primary) 0%, #4338ca 100%) !important;
    border: none;
    transition: transform 0.2s ease;
}

.ai-btn-send:hover {
    transform: scale(1.06);
}

.ai-btn-send:disabled {
    opacity: 0.6;
    transform: none;
}

/* Formatted Content inside Bot Message */
.ai-markdown-content {
    font-size: 0.88rem;
    line-height: 1.6;
}

.ai-markdown-content h4, 
.ai-markdown-content h5, 
.ai-markdown-content h6 {
    color: var(--bs-primary);
    margin-top: 0.75rem;
    margin-bottom: 0.35rem;
    font-weight: 700;
}

.ai-markdown-content table {
    width: 100%;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
    border-collapse: collapse;
    font-size: 0.82rem;
}

.ai-markdown-content table th, 
.ai-markdown-content table td {
    padding: 6px 10px;
    border: 1px solid #e2e8f0;
}

.ai-markdown-content table th {
    background-color: #f8fafc;
    font-weight: 600;
}

.ai-markdown-content pre {
    background: #0f172a;
    color: #e2e8f0;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 0.8rem;
    overflow-x: auto;
    position: relative;
    margin: 8px 0;
}

.ai-markdown-content code:not(pre code) {
    background: rgba(var(--bs-primary-rgb), 0.08);
    color: var(--bs-primary);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.82rem;
}

.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const chatMessagesFeed = document.getElementById('ai-chat-messages');
    const dynamicFlow = document.getElementById('ai-chat-dynamic-flow');
    const initialBotMsg = document.getElementById('ai-initial-bot-msg');
    const questionInput = document.getElementById('ai-user-question-input');
    const assistantForm = document.getElementById('ai-assistant-form');
    const thinkingIndicator = document.getElementById('ai-thinking-indicator');
    const btnSend = document.getElementById('btn-ai-send');
    const btnNewChat = document.getElementById('btn-new-chat');
    const btnClearActive = document.getElementById('btn-clear-active-chat');
    const btnClearAllSessions = document.getElementById('btn-clear-all-sessions');
    const btnExport = document.getElementById('btn-export-chat');
    const btnPrint = document.getElementById('btn-print-chat');
    const btnVoice = document.getElementById('btn-voice-input');
    const promptsList = document.getElementById('ai-quick-prompts-list');
    const sessionsList = document.getElementById('ai-sessions-list');

    let currentRoute = @json($currentRouteName);
    let currentUrl = @json($currentUrl);
    let currentSessionId = 'session_' + Date.now();
    let currentMessages = [];

    // Auto-resize for textarea
    questionInput.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = (Math.min(this.scrollHeight, 130)) + 'px';
    });

    // Enter to submit, Shift+Enter for newline
    questionInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            assistantForm.dispatchEvent(new Event('submit'));
        }
    });

    // Quick prompt chip click (from top Suggested: bar)
    document.addEventListener('click', function (e) {
        const chip = e.target.closest('.ai-prompt-chip');
        if (chip) {
            const promptText = chip.innerText.trim();
            if (promptText) {
                questionInput.value = promptText;
                submitQuestion(promptText);
            }
        }
    });

    // Voice Input (Web Speech Recognition)
    if (btnVoice) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (SpeechRecognition) {
            const recognition = new SpeechRecognition();
            recognition.lang = 'ar-SA';
            recognition.continuous = false;
            recognition.interimResults = false;

            let isRecording = false;

            btnVoice.addEventListener('click', function () {
                if (isRecording) {
                    recognition.stop();
                    return;
                }
                try {
                    recognition.start();
                    isRecording = true;
                    btnVoice.classList.add('text-danger', 'ai-dot-pulse');
                } catch (e) {}
            });

            recognition.onresult = function (event) {
                const transcript = event.results[0][0].transcript;
                questionInput.value = (questionInput.value ? questionInput.value + ' ' : '') + transcript;
                questionInput.dispatchEvent(new Event('input'));
            };

            recognition.onend = function () {
                isRecording = false;
                btnVoice.classList.remove('text-danger', 'ai-dot-pulse');
            };
        } else {
            btnVoice.style.display = 'none';
        }
    }

    // Submit question form
    assistantForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const text = questionInput.value.trim();
        if (!text) return;
        submitQuestion(text);
    });

    // New Chat button
    btnNewChat.addEventListener('click', function () {
        saveCurrentSessionToStorage();
        currentSessionId = 'session_' + Date.now();
        currentMessages = [];
        dynamicFlow.innerHTML = '';
        if (initialBotMsg) initialBotMsg.classList.remove('d-none');
        loadSessionsList();
    });

    // Clear active chat button
    if (btnClearActive) {
        btnClearActive.addEventListener('click', function () {
            currentMessages = [];
            dynamicFlow.innerHTML = '';
            if (initialBotMsg) initialBotMsg.classList.remove('d-none');
            saveCurrentSessionToStorage();
        });
    }

    // Clear all history
    if (btnClearAllSessions) {
        btnClearAllSessions.addEventListener('click', function () {
            if (confirm(@json(__('هل أنت متأكد من رغبتك في مسح سجل المحادثات بالكامل؟')))) {
                localStorage.removeItem('gate_ai_accounting_sessions');
                currentMessages = [];
                dynamicFlow.innerHTML = '';
                if (initialBotMsg) initialBotMsg.classList.remove('d-none');
                loadSessionsList();
            }
        });
    }

    // Export Chat as Text / Markdown
    if (btnExport) {
        btnExport.addEventListener('click', function () {
            if (currentMessages.length === 0) {
                alert(@json(__('لا توجد رسائل لتصديرها.')));
                return;
            }
            let exportContent = "# " + @json(__('GateTech ERP - سجل محادثة المساعد المالي الذكي')) + "\n";
            exportContent += "التاريخ: " + new Date().toLocaleString('ar-SA') + "\n\n";
            currentMessages.forEach(m => {
                const role = m.role === 'user' ? @json(__('أنت')) : @json(__('المساعد المالي'));
                exportContent += `### [${role}] (${m.time || ''}):\n${m.content}\n\n---\n\n`;
            });

            const blob = new Blob([exportContent], { type: 'text/markdown;charset=utf-8' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `financial_ai_chat_${Date.now()}.md`;
            link.click();
        });
    }

    // Print Chat
    if (btnPrint) {
        btnPrint.addEventListener('click', function () {
            window.print();
        });
    }

    // Submit Question Core
    function submitQuestion(question) {
        questionInput.value = '';
        questionInput.style.height = 'auto';

        const userMsg = {
            role: 'user',
            content: question,
            time: formatTime(new Date())
        };
        currentMessages.push(userMsg);
        appendUserMessage(userMsg);

        thinkingIndicator.classList.remove('d-none');
        thinkingIndicator.classList.add('d-flex');
        btnSend.disabled = true;
        scrollToBottom();

        const historyPayload = currentMessages.map(m => ({ role: m.role, content: m.content }));

        fetch(@json(route('accounting.ai-assistant.ask')), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                question: question,
                current_route: currentRoute,
                current_url: currentUrl,
                history: historyPayload
            })
        })
        .then(response => response.json())
        .then(data => {
            thinkingIndicator.classList.add('d-none');
            thinkingIndicator.classList.remove('d-flex');
            btnSend.disabled = false;

            const answer = data.answer || @json(__('لم يتم استلام أي رد من النظام.'));
            const botMsg = {
                role: 'assistant',
                content: answer,
                time: formatTime(new Date()),
                success: data.success !== false
            };
            currentMessages.push(botMsg);
            appendBotMessage(botMsg);

            // Update suggestions in top bar if new ones came
            if (data.suggestions && data.suggestions.length > 0) {
                updateSuggestionsChips(data.suggestions);
            }

            saveCurrentSessionToStorage();
            scrollToBottom();
        })
        .catch(err => {
            thinkingIndicator.classList.add('d-none');
            thinkingIndicator.classList.remove('d-flex');
            btnSend.disabled = false;

            const errorMsg = {
                role: 'assistant',
                content: @json(__('حدث خطأ أثناء الاتصال بالمساعد: ')) + err.message,
                time: formatTime(new Date()),
                success: false
            };
            currentMessages.push(errorMsg);
            appendBotMessage(errorMsg);
            scrollToBottom();
        });
    }

    function appendUserMessage(msg) {
        const wrap = document.createElement('div');
        wrap.className = 'd-flex justify-content-end align-items-start gap-2 mb-3';
        wrap.innerHTML = `
            <div class="ai-msg-bubble-user p-3 shadow-sm">
                <div class="small mb-1 text-white">${escapeHtml(msg.content).replace(/\n/g, '<br>')}</div>
                <div class="text-white-50 text-end" style="font-size: 0.65rem;">${msg.time}</div>
            </div>
            <div class="avatar avatar-xs bg-label-primary rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center mt-1">
                <i class="bx bx-user"></i>
            </div>
        `;
        dynamicFlow.appendChild(wrap);
        scrollToBottom();
    }

    function appendBotMessage(msg) {
        const wrap = document.createElement('div');
        wrap.className = 'd-flex align-items-start gap-2 mb-3';

        const avatarBg = msg.success ? 'bg-primary' : 'bg-warning';
        const formattedHtml = formatMarkdown(msg.content);

        wrap.innerHTML = `
            <div class="avatar avatar-xs ${avatarBg} text-white rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center mt-1 shadow-2xs">
                <i class="bx bx-bot"></i>
            </div>
            <div class="ai-msg-bubble-bot p-3 shadow-sm flex-grow-1">
                <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                    <span class="fw-bold small text-primary d-flex align-items-center gap-1" style="font-size: 0.76rem;">
                        <i class="bx bx-check-shield text-success"></i>GateTech Financial AI
                    </span>
                    <span class="text-muted" style="font-size: 0.65rem;">${msg.time}</span>
                </div>
                
                <div class="ai-markdown-content text-dark mb-2">${formattedHtml}</div>

                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="btn btn-xs btn-icon btn-outline-secondary rounded-pill btn-copy-msg" title="${@json(__('نسخ الرد'))}">
                            <i class="bx bx-copy"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-icon btn-outline-secondary rounded-pill btn-speak-msg" title="${@json(__('قراءة صوتية'))}">
                            <i class="bx bx-volume-full"></i>
                        </button>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="btn btn-xs btn-icon btn-outline-secondary rounded-pill btn-thumb-up" title="${@json(__('مفيد'))}">
                            <i class="bx bx-like"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-icon btn-outline-secondary rounded-pill btn-thumb-down" title="${@json(__('غير مفيد'))}">
                            <i class="bx bx-dislike"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Wire Copy Button
        const copyBtn = wrap.querySelector('.btn-copy-msg');
        copyBtn.addEventListener('click', function () {
            navigator.clipboard.writeText(msg.content).then(() => {
                copyBtn.innerHTML = '<i class="bx bx-check text-success"></i>';
                setTimeout(() => copyBtn.innerHTML = '<i class="bx bx-copy"></i>', 2000);
            });
        });

        // Wire Text-to-Speech Button
        const speakBtn = wrap.querySelector('.btn-speak-msg');
        speakBtn.addEventListener('click', function () {
            if ('speechSynthesis' in window) {
                if (window.speechSynthesis.speaking) {
                    window.speechSynthesis.cancel();
                    speakBtn.innerHTML = '<i class="bx bx-volume-full"></i>';
                    return;
                }
                const cleanText = msg.content.replace(/[*_#`\[\]]/g, '');
                const utterance = new SpeechSynthesisUtterance(cleanText);
                utterance.lang = 'ar-SA';
                utterance.onend = () => speakBtn.innerHTML = '<i class="bx bx-volume-full"></i>';
                speakBtn.innerHTML = '<i class="bx bx-stop text-danger"></i>';
                window.speechSynthesis.speak(utterance);
            }
        });

        // Wire Feedback Buttons
        const thumbUp = wrap.querySelector('.btn-thumb-up');
        const thumbDown = wrap.querySelector('.btn-thumb-down');
        thumbUp.addEventListener('click', function () {
            thumbUp.classList.toggle('text-success');
            thumbDown.classList.remove('text-danger');
        });
        thumbDown.addEventListener('click', function () {
            thumbDown.classList.toggle('text-danger');
            thumbUp.classList.remove('text-success');
        });

        dynamicFlow.appendChild(wrap);
        scrollToBottom();
    }

    function updateSuggestionsChips(suggestions) {
        if (!promptsList) return;
        promptsList.innerHTML = `
            <span class="text-muted small fw-semibold flex-shrink-0 d-flex align-items-center gap-1" style="font-size: 0.74rem;">
                <i class="bx bx-sparkles text-warning"></i>
                <span>${@json(__('أسئلة مقترحة:'))}</span>
            </span>
        `;
        suggestions.forEach(s => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-xs btn-outline-primary bg-white text-nowrap rounded-pill py-1 px-3 ai-prompt-chip';
            btn.style.fontSize = '0.74rem';
            btn.innerText = s;
            promptsList.appendChild(btn);
        });
    }

    function scrollToBottom() {
        setTimeout(() => {
            chatMessagesFeed.scrollTop = chatMessagesFeed.scrollHeight;
        }, 60);
    }

    function formatTime(date) {
        return date.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' });
    }

    function escapeHtml(string) {
        const entityMap = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
        return String(string).replace(/[&<>"']/g, s => entityMap[s]);
    }

    function formatMarkdown(text) {
        if (!text) return '';
        let escaped = escapeHtml(text);

        // Code blocks ```...```
        escaped = escaped.replace(/```([a-zA-Z0-9]*)\n([\s\S]*?)```/g, function (match, lang, code) {
            return `<pre><code class="language-${lang}">${code.trim()}</code></pre>`;
        });

        // Inline code `...`
        escaped = escaped.replace(/`([^`]+)`/g, '<code>$1</code>');

        // Bold & Italic
        escaped = escaped.replace(/\*\*\*(.*?)\*\*\*/g, '<strong><em>$1</em></strong>');
        escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        escaped = escaped.replace(/\*(.*?)\*/g, '<em>$1</em>');

        // Headers
        escaped = escaped.replace(/^### (.*$)/gim, '<h6 class="fw-bold text-primary mt-2 mb-1">$1</h6>');
        escaped = escaped.replace(/^## (.*$)/gim, '<h5 class="fw-bold text-primary mt-3 mb-1">$1</h5>');
        escaped = escaped.replace(/^# (.*$)/gim, '<h5 class="fw-bold text-primary mt-3 mb-1">$1</h5>');

        // Blockquotes / Alerts
        escaped = escaped.replace(/^> (.*$)/gim, '<div class="alert alert-primary py-2 px-3 my-2 small border-start border-4 border-primary">$1</div>');

        // Unordered list items
        escaped = escaped.replace(/^\* (.*$)/gim, '<div class="d-flex align-items-start gap-2 mb-1"><i class="bx bx-check-circle text-primary mt-1 font-14"></i><span>$1</span></div>');
        escaped = escaped.replace(/^- (.*$)/gim, '<div class="d-flex align-items-start gap-2 mb-1"><i class="bx bx-check-circle text-primary mt-1 font-14"></i><span>$1</span></div>');

        // Numbered list items
        escaped = escaped.replace(/^(\d+)\. (.*$)/gim, '<div class="d-flex align-items-start gap-2 mb-1"><span class="badge bg-label-primary rounded-circle p-1" style="min-width: 20px; font-size: 0.68rem;">$1</span><span>$2</span></div>');

        // Links [text](url)
        escaped = escaped.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" class="text-primary fw-semibold text-decoration-underline" target="_blank">$1 <i class="bx bx-link-external font-12"></i></a>');

        // Linebreaks
        escaped = escaped.replace(/\n/g, '<br>');

        return escaped;
    }

    // Local Storage Session Management
    function saveCurrentSessionToStorage() {
        if (currentMessages.length === 0) return;
        let sessions = JSON.parse(localStorage.getItem('gate_ai_accounting_sessions') || '[]');
        const firstUserQuestion = currentMessages.find(m => m.role === 'user')?.content || @json(__('محادثة'));
        const snippet = firstUserQuestion.length > 35 ? firstUserQuestion.substring(0, 35) + '...' : firstUserQuestion;

        const sessionIndex = sessions.findIndex(s => s.id === currentSessionId);
        const sessionData = {
            id: currentSessionId,
            title: snippet,
            route: currentRoute,
            date: new Date().toLocaleDateString('ar-SA', { month: 'short', day: 'numeric' }),
            messages: currentMessages
        };

        if (sessionIndex >= 0) {
            sessions[sessionIndex] = sessionData;
        } else {
            sessions.unshift(sessionData);
            if (sessions.length > 15) sessions.pop();
        }

        localStorage.setItem('gate_ai_accounting_sessions', JSON.stringify(sessions));
        loadSessionsList();
    }

    function loadSessionsList() {
        if (!sessionsList) return;
        const sessions = JSON.parse(localStorage.getItem('gate_ai_accounting_sessions') || '[]');
        sessionsList.innerHTML = '';

        if (sessions.length === 0) {
            sessionsList.innerHTML = `
                <div class="text-muted text-center py-3 small" style="font-size: 0.72rem;">
                    ${@json(__('لا توجد محادثات سابقة.'))}
                </div>
            `;
            return;
        }

        sessions.forEach(s => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2 rounded-2 mb-1 border-0 ' + (s.id === currentSessionId ? 'active bg-label-primary' : '');
            item.style.fontSize = '0.75rem';
            item.innerHTML = `
                <div class="d-flex align-items-center gap-2 text-truncate me-1">
                    <i class="bx bx-message-rounded-dots text-primary font-14"></i>
                    <span class="text-truncate fw-medium">${escapeHtml(s.title)}</span>
                </div>
                <span class="text-muted flex-shrink-0" style="font-size: 0.65rem;">${s.date}</span>
            `;

            item.addEventListener('click', function () {
                currentSessionId = s.id;
                currentMessages = s.messages || [];
                dynamicFlow.innerHTML = '';
                if (initialBotMsg) initialBotMsg.classList.add('d-none');
                currentMessages.forEach(m => {
                    if (m.role === 'user') appendUserMessage(m);
                    else appendBotMessage(m);
                });
                loadSessionsList();

                // Close offcanvas if open
                const offcanvasEl = document.getElementById('offcanvasChatHistory');
                const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                if (offcanvas) offcanvas.hide();
            });

            sessionsList.appendChild(item);
        });
    }

    loadSessionsList();
});
</script>
@endsection
