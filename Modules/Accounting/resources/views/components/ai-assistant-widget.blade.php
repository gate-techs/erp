@php
    $currentRouteName = Route::currentRouteName();
    $currentUrl = request()->fullUrl();
    $assistantService = app(\Modules\Accounting\Services\FinancialAssistantService::class);
    $screenContext = $assistantService->resolveScreenContext($currentRouteName, $currentUrl);
@endphp

<!-- AI Financial Assistant standalone page -->
<div class="card border shadow-sm overflow-hidden ai-assistant-page">
    
    <!-- Drawer Header -->
    <div class="bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                <i class="bx bx-bot fs-4"></i>
            </div>
            <div>
                <h6 class="mb-0 text-body fw-bold d-flex align-items-center gap-2" id="aiFinancialAssistantLabel">
                    <span>{{ __('Financial Assistant') }}</span>
                </h6>
                <small class="text-muted d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                    <span class="ai-online-dot"></span>{{ __('Online · Scoped to system records') }}
                </small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-1">
            <button type="button" class="btn btn-sm btn-icon btn-light" id="btn-clear-ai-chat" title="{{ __('Clear Chat') }}">
                <i class="bx bx-trash"></i>
            </button>
            <a href="{{ route('settings.ai.index') }}" 
               class="btn btn-sm btn-icon btn-light" 
               title="{{ __('AI Model Settings & Task Routing') }}" 
               target="_blank">
                <i class="bx bx-cog fs-5"></i>
            </a>
        </div>
    </div>

    <!-- Active Screen Bar -->
    <div class="bg-light border-bottom px-4 py-2 d-flex align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2 text-truncate">
            <i class="bx bx-window-alt text-primary font-16"></i>
            <span class="small fw-semibold text-truncate" id="ai-current-screen-title">
                {{ $screenContext['title'] ?? __('Accounting System') }}
            </span>
        </div>
        <span class="text-muted" style="font-size: 0.72rem;"><i class="bx bx-lock-alt me-1"></i>{{ __('Private') }}</span>
    </div>

    <!-- Drawer Body / Chat Stream -->
    <div class="p-4 d-flex flex-column ai-chat-body">
        
        <!-- Messages Area -->
        <div id="ai-chat-messages" class="flex-grow-1 overflow-auto pe-1">
            
            <!-- Welcome Bot Card -->
            <div class="ai-msg ai-msg-bot mb-3">
                <div class="d-flex gap-2 align-items-start">
                    <div class="avatar avatar-xs bg-primary text-white rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center">
                        <i class="bx bx-bot"></i>
                    </div>
                    <div class="ai-bot-bubble p-3 text-dark flex-grow-1" style="max-width: 85%;">
                        <p class="mb-2 small fw-semibold text-primary">
                            {{ __('Welcome! I am your AI Financial Assistant and guide for GateTech ERP.') }}
                        </p>
                        <p class="mb-2 small text-muted">
                            {{ $screenContext['summary'] ?? __('I can answer any financial inquiry based on your records or guide you step-by-step through this screen.') }}
                        </p>
                        
                        <!-- Screen Actions Quick Summary -->
                        @if (!empty($screenContext['actions']))
                        <div class="border-top pt-2 mt-2">
                            <span class="badge bg-label-info mb-1" style="font-size: 0.7rem;">{{ __('Key tasks for this screen:') }}</span>
                            <ul class="mb-0 ps-3 small text-muted" style="font-size: 0.78rem;">
                                @foreach (array_slice($screenContext['actions'], 0, 3) as $act)
                                    <li>{{ $act }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dynamic Quick Questions Chips -->
            <div class="mb-3 ps-4 pe-2" id="ai-quick-prompts-wrap">
                <div class="small fw-semibold text-muted mb-2 d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                    <i class="bx bx-bulb text-warning"></i>
                    <span>{{ __('Quick questions and suggestions for this screen:') }}</span>
                </div>
                <div class="d-flex flex-wrap gap-1" id="ai-quick-prompts-list">
                    @foreach ($screenContext['suggestions'] ?? [] as $prompt)
                        <button type="button" class="btn btn-sm btn-outline-primary bg-white text-start py-1 px-2 ai-prompt-chip" style="font-size: 0.74rem; border-radius: 12px;">
                            {{ $prompt }}
                        </button>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Thinking / Loading Indicator -->
        <div id="ai-thinking-indicator" class="d-none align-items-center gap-2 p-2 mb-2 bg-white rounded-3 shadow-sm border">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <span class="small text-muted" style="font-size: 0.8rem;">{{ __('AI Assistant is checking records and drafting response...') }}</span>
        </div>

    </div>

    <!-- Drawer Footer / Input Box -->
    <div class="p-3 bg-white border-top">
        <form id="ai-assistant-form" class="d-flex gap-2 align-items-center">
            <input type="text" 
                   id="ai-user-question-input" 
                   class="form-control py-3 px-3 rounded-3" 
                   placeholder="{{ __('Ask about this screen or request a financial summary...') }}" 
                   autocomplete="off" 
                   required>
            <button type="submit" 
                    id="btn-ai-send" 
                    class="btn btn-primary rounded-3 d-flex align-items-center justify-content-center p-0 flex-shrink-0" 
                    style="width: 46px; height: 46px;" 
                    title="{{ __('Send') }}">
                <i class="bx bx-paper-plane font-16"></i>
            </button>
        </form>
        <div class="d-flex justify-content-between align-items-center mt-2 px-1">
            <span class="text-muted" style="font-size: 0.68rem;">
                <i class="bx bx-shield-quarter me-1 text-success"></i>{{ __('Strictly scoped to accounting records') }}
            </span>
            <span></span>
        </div>
    </div>
</div>

<style>
.ai-sparkle-dot {
    width: 9px;
    height: 9px;
    background-color: #28c76f;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 0 0 rgba(40, 199, 111, 0.7);
    animation: aiPulse 2s infinite;
}
.ai-assistant-page {
    width: min(100%, 980px);
    height: calc(100vh - 145px);
    min-height: 560px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
}
.ai-chat-body {
    flex: 1 1 auto;
    min-height: 0;
    background: #f7f8fa;
}
.ai-chat-body #ai-chat-messages {
    min-height: 0;
    max-height: none;
}
.ai-online-dot {
    width: 7px;
    height: 7px;
    display: inline-block;
    border-radius: 50%;
    background: #28c76f;
}
.ai-bot-bubble {
    background: #fff;
    border: 1px solid #e8e8ec;
    border-radius: 4px 16px 16px 16px;
    box-shadow: 0 2px 6px rgba(40, 40, 60, .04);
}
.ai-msg-user .card {
    border-radius: 16px 4px 16px 16px !important;
    box-shadow: none !important;
}
@keyframes aiPulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 199, 111, 0.7); }
    70% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(40, 199, 111, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 199, 111, 0); }
}
.ai-msg-user .card {
    background-color: var(--bs-primary) !important;
    color: #fff !important;
}
.ai-msg-user .card p {
    color: #fff !important;
}
.ai-prompt-chip {
    transition: all 0.2s ease-in-out;
}
.ai-prompt-chip:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(var(--bs-primary-rgb), 0.15);
}
#ai-chat-messages pre {
    white-space: pre-wrap;
    word-break: break-word;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const chatMessages = document.getElementById('ai-chat-messages');
    const questionInput = document.getElementById('ai-user-question-input');
    const assistantForm = document.getElementById('ai-assistant-form');
    const thinkingIndicator = document.getElementById('ai-thinking-indicator');
    const btnSend = document.getElementById('btn-ai-send');
    const btnClear = document.getElementById('btn-clear-ai-chat');
    const promptsList = document.getElementById('ai-quick-prompts-list');

    let chatHistory = [];
    const currentRoute = @json($currentRouteName);
    const currentUrl = @json($currentUrl);

    // Click on quick prompt chips
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

    // Form submit
    assistantForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const text = questionInput.value.trim();
        if (!text) return;
        submitQuestion(text);
    });

    // Clear chat
    btnClear.addEventListener('click', function () {
        chatHistory = [];
        const messages = chatMessages.querySelectorAll('.ai-dynamic-msg');
        messages.forEach(m => m.remove());
    });

    function submitQuestion(question) {
        questionInput.value = '';
        appendUserMessage(question);
        
        chatHistory.push({ role: 'user', content: question });

        thinkingIndicator.classList.remove('d-none');
        thinkingIndicator.classList.add('d-flex');
        btnSend.disabled = true;
        scrollToBottom();

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
                history: chatHistory
            })
        })
        .then(response => response.json())
        .then(data => {
            thinkingIndicator.classList.add('d-none');
            thinkingIndicator.classList.remove('d-flex');
            btnSend.disabled = false;

            const answer = data.answer || @json(__('No response received from the system.'));
            chatHistory.push({ role: 'assistant', content: answer });
            appendBotMessage(answer, data.success);

            // Update suggestions if new ones came
            if (data.suggestions && data.suggestions.length > 0 && promptsList) {
                promptsList.innerHTML = '';
                data.suggestions.forEach(s => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-sm btn-outline-primary bg-white text-start py-1 px-2 ai-prompt-chip';
                    btn.style.fontSize = '0.74rem';
                    btn.style.borderRadius = '12px';
                    btn.innerText = s;
                    promptsList.appendChild(btn);
                });
            }

            scrollToBottom();
        })
        .catch(err => {
            thinkingIndicator.classList.add('d-none');
            thinkingIndicator.classList.remove('d-flex');
            btnSend.disabled = false;
            appendBotMessage(@json(__('An error occurred while connecting to the server: ')) + err.message, false);
            scrollToBottom();
        });
    }

    function appendUserMessage(text) {
        const wrap = document.createElement('div');
        wrap.className = 'ai-msg ai-msg-user ai-dynamic-msg mb-3 d-flex justify-content-end';
        wrap.innerHTML = `
            <div class="card border-0 shadow-sm p-3 rounded-3 text-white" style="max-width: 85%; border-bottom-left-radius: 4px !important;">
                <p class="mb-0 small">${escapeHtml(text)}</p>
            </div>
        `;
        chatMessages.appendChild(wrap);
        scrollToBottom();
    }

    function appendBotMessage(markdownText, isSuccess) {
        const wrap = document.createElement('div');
        wrap.className = 'ai-msg ai-msg-bot ai-dynamic-msg mb-3';
        
        const avatarBg = isSuccess ? 'bg-primary' : 'bg-warning';
        const formattedHtml = formatMarkdown(markdownText);

        wrap.innerHTML = `
            <div class="d-flex gap-2 align-items-start">
                <div class="avatar avatar-xs ${avatarBg} text-white rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center">
                    <i class="bx bx-bot"></i>
                </div>
                <div class="card border-0 shadow-sm p-3 rounded-3 bg-white text-dark flex-grow-1" style="max-width: 90%;">
                    <div class="small ai-content-body">${formattedHtml}</div>
                </div>
            </div>
        `;
        chatMessages.appendChild(wrap);
        scrollToBottom();
    }

    function scrollToBottom() {
        setTimeout(() => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 50);
    }

    function escapeHtml(string) {
        const entityMap = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
        return String(string).replace(/[&<>"']/g, s => entityMap[s]);
    }

    function formatMarkdown(text) {
        if (!text) return '';
        let escaped = escapeHtml(text);

        // Bold
        escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        // Italic
        escaped = escaped.replace(/\*(.*?)\*/g, '<em>$1</em>');
        // Headers
        escaped = escaped.replace(/^### (.*$)/gim, '<h6 class="fw-bold text-primary mt-2 mb-1">$1</h6>');
        escaped = escaped.replace(/^## (.*$)/gim, '<h6 class="fw-bold text-primary mt-2 mb-1">$1</h6>');
        // Unordered list items
        escaped = escaped.replace(/^\* (.*$)/gim, '• $1<br>');
        escaped = escaped.replace(/^- (.*$)/gim, '• $1<br>');
        // Numbered lists
        escaped = escaped.replace(/^(\d+)\. (.*$)/gim, '<span class="badge bg-label-primary rounded-circle p-1 me-1">$1</span> $2<br>');
        // Links [text](url)
        escaped = escaped.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" class="text-primary fw-semibold text-decoration-underline" target="_blank">$1</a>');
        // Linebreaks
        escaped = escaped.replace(/\n/g, '<br>');

        return escaped;
    }
});
</script>
