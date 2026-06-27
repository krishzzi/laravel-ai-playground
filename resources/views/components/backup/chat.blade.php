<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Chat · Clean Workspace</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
</head>
<body>
<div class="app">
    <!-- Header minimal -->
    <div class="chat-header">
        <div class="logo-area">
            <div class="avatar-icon">
                <i class="fas fa-comment-dots"></i>
            </div>
            <div class="title">
                <h2>AI Assistant</h2>
                <p>Ready to help · always online</p>
            </div>
        </div>
        <div class="header-actions">
            <button class="icon-btn" id="clearBtn" title="Clear chat">
                <i class="fas fa-eraser"></i>
            </button>
            <button class="icon-btn" id="scrollDownBtn" title="Scroll to bottom">
                <i class="fas fa-arrow-down"></i>
            </button>
        </div>
    </div>

    <!-- Chat messages area -->
    <div class="chat-main" id="chatMain">
        <div class="messages-container" id="messagesContainer">
            <div class="message assistant">
                <div class="msg-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <div class="bubble">
                        👋 Hello! I'm your AI assistant. Ask me anything — from coding help to creative ideas.
                    </div>
                    <div class="timestamp">Just now</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Input area -->
    <div class="input-area">
        <div class="input-wrapper">
            <textarea id="message" placeholder="Type your message..." rows="1"></textarea>
            <button class="send-btn" id="sendBtn" type="button">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3.1.6/dist/purify.min.js"></script>

<script>
    // DOM elements
    const messagesContainer = document.getElementById('messagesContainer');
    const chatMain = document.getElementById('chatMain');
    const messageInput = document.getElementById('message');
    const sendBtn = document.getElementById('sendBtn');
    const clearBtn = document.getElementById('clearBtn');
    const scrollDownBtn = document.getElementById('scrollDownBtn');

    let activeEventSource = null;
    let isStreamingActive = false;
    let currentStreamBubble = null;
    let typingRowElement = null;

    // Helper: get current time string
    function getCurrentTime() {
        return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    // Scroll to bottom of chat
    function scrollToBottom(behavior = 'smooth') {
        chatMain.scrollTo({
            top: chatMain.scrollHeight,
            behavior: behavior
        });
    }

    // Remove typing indicator if exists
    function removeTypingIndicator() {
        if (typingRowElement && typingRowElement.parentNode) {
            typingRowElement.remove();
            typingRowElement = null;
        }
    }

    // Show "Assistant is typing..." animation
    function showTypingIndicator() {
        removeTypingIndicator();
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message assistant';
        typingDiv.id = 'typingIndicatorMsg';
        typingDiv.innerHTML = `
            <div class="msg-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="message-content">
                <div class="typing-indicator">
                    <div class="dots">
                        <span></span><span></span><span></span>
                    </div>
                    <span class="typing-text">Thinking</span>
                </div>
            </div>
        `;
        messagesContainer.appendChild(typingDiv);
        typingRowElement = typingDiv;
        scrollToBottom();
    }

    // Add a new user or assistant message (without streaming)
    function addStaticMessage(role, text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${role}`;
        const avatarIcon = role === 'assistant' ? '<i class="fas fa-robot"></i>' : '<i class="fas fa-user"></i>';
        const bubbleContent = role === 'assistant' ? DOMPurify.sanitize(marked.parse(text)) : escapeHtml(text);
        messageDiv.innerHTML = `
            <div class="msg-avatar">
                ${avatarIcon}
            </div>
            <div class="message-content">
                <div class="bubble">${bubbleContent}</div>
                <div class="timestamp">${getCurrentTime()}</div>
            </div>
        `;
        messagesContainer.appendChild(messageDiv);
        scrollToBottom();
        return messageDiv.querySelector('.bubble');
    }

    // Helper to escape HTML for user messages
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        }).replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, function(c) {
            return c;
        });
    }

    // Create an empty assistant bubble for streaming
    function createStreamingAssistantBubble() {
        removeTypingIndicator();
        const messageDiv = document.createElement('div');
        messageDiv.className = `message assistant`;
        messageDiv.innerHTML = `
            <div class="msg-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="message-content">
                <div class="bubble" data-raw=""></div>
                <div class="timestamp">${getCurrentTime()}</div>
            </div>
        `;
        messagesContainer.appendChild(messageDiv);
        const bubble = messageDiv.querySelector('.bubble');
        bubble.innerHTML = '<span class="stream-cursor"></span>';
        bubble.dataset.raw = '';
        scrollToBottom();
        return bubble;
    }

    // Append text chunk to streaming bubble with Markdown rendering
    function appendStreamChunk(bubble, chunk) {
        let currentRaw = bubble.dataset.raw || '';
        currentRaw += chunk;
        bubble.dataset.raw = currentRaw;
        let renderedHtml = DOMPurify.sanitize(marked.parse(currentRaw));
        if (isStreamingActive) {
            // add cursor at the end while still streaming
            if (!renderedHtml.endsWith('<span class="stream-cursor"></span>')) {
                renderedHtml = renderedHtml + '<span class="stream-cursor"></span>';
            }
        }
        bubble.innerHTML = renderedHtml;
        scrollToBottom();
    }

    // Finish streaming: clean up, remove cursor, enable inputs
    function finishStreaming() {
        if (activeEventSource) {
            try { activeEventSource.close(); } catch(e) {}
            activeEventSource = null;
        }
        if (isStreamingActive) {
            isStreamingActive = false;
            if (currentStreamBubble) {
                let finalHtml = currentStreamBubble.innerHTML.replace('<span class="stream-cursor"></span>', '');
                currentStreamBubble.innerHTML = finalHtml;
                // if bubble is empty, set fallback
                if (!currentStreamBubble.dataset.raw || currentStreamBubble.dataset.raw.trim() === '') {
                    currentStreamBubble.innerHTML = '✨ No response received.';
                    currentStreamBubble.dataset.raw = 'No response received.';
                }
                currentStreamBubble = null;
            }
        }
        removeTypingIndicator();
        // enable UI
        sendBtn.disabled = false;
        messageInput.disabled = false;
        messageInput.focus();
    }

    // Parse Vercel / data protocol stream chunks (supports 0:"text" format)
    function parseStreamChunk(data) {
        if (!data || data === '[DONE]') return null;
        // handle 0:"content"
        const quotedMatch = data.match(/^0:"(.*)"$/);
        if (quotedMatch) {
            try {
                return JSON.parse(`"${quotedMatch[1]}"`);
            } catch(e) {
                return quotedMatch[1];
            }
        }
        // handle 0:plain text (no quotes)
        const plainMatch = data.match(/^0:(.*)$/);
        if (plainMatch) {
            try {
                const parsed = JSON.parse(plainMatch[1]);
                if (typeof parsed === 'string') return parsed;
                return plainMatch[1];
            } catch(e) {
                return plainMatch[1];
            }
        }
        // try JSON
        try {
            const obj = JSON.parse(data);
            if (typeof obj === 'string') return obj;
            if (obj.text) return obj.text;
            if (obj.content) return obj.content;
            if (obj.delta) return obj.delta;
            return null;
        } catch(e) {
            return data.length > 1 ? data : null;
        }
    }

    // Start SSE stream
    function startStreaming(userMessage) {
        // abort any existing stream
        if (activeEventSource) {
            activeEventSource.close();
            activeEventSource = null;
        }
        finishStreaming(); // ensure clean state

        isStreamingActive = true;
        showTypingIndicator();

        // Create the assistant bubble that will be filled with streaming content
        currentStreamBubble = createStreamingAssistantBubble();

        const url = new URL('{{ route('chat.stream') }}', window.location.origin);
        url.searchParams.set('message', userMessage);

        activeEventSource = new EventSource(url.toString());

        activeEventSource.onmessage = (event) => {
            // remove typing indicator on first data chunk
            if (typingRowElement) removeTypingIndicator();
            const chunkText = parseStreamChunk(event.data);
            if (chunkText && typeof chunkText === 'string' && chunkText.length > 0) {
                if (currentStreamBubble) {
                    appendStreamChunk(currentStreamBubble, chunkText);
                }
            } else if (event.data === '[DONE]') {
                finishStreaming();
            }
        };

        activeEventSource.onerror = (err) => {
            if (currentStreamBubble && (!currentStreamBubble.dataset.raw || currentStreamBubble.dataset.raw.length === 0)) {
                currentStreamBubble.innerHTML = '⚠️ Connection error. Please try again.';
                currentStreamBubble.dataset.raw = 'Connection error.';
            }
            finishStreaming();
        };

        // Handle custom finish/done events
        activeEventSource.addEventListener('finish', () => finishStreaming());
        activeEventSource.addEventListener('done', () => finishStreaming());

        // safety timeout (60 seconds)
        setTimeout(() => {
            if (isStreamingActive && activeEventSource) {
                if (currentStreamBubble && (!currentStreamBubble.dataset.raw || currentStreamBubble.dataset.raw.length < 3)) {
                    appendStreamChunk(currentStreamBubble, " [timeout]");
                }
                finishStreaming();
            }
        }, 60000);
    }

    // Send a new user message
    function sendMessage() {
        const text = messageInput.value.trim();
        if (!text) return;
        if (isStreamingActive) {
            // if streaming, cancel current stream gracefully
            if (activeEventSource) {
                activeEventSource.close();
                activeEventSource = null;
            }
            finishStreaming();
        }

        // Add user message to UI
        addStaticMessage('user', text);

        // Clear input & resize
        messageInput.value = '';
        adjustTextareaHeight();

        // Disable UI during streaming
        sendBtn.disabled = true;
        messageInput.disabled = true;

        // start AI stream
        startStreaming(text);
    }

    // Clear entire conversation (keep only a clean welcome message)
    function clearConversation() {
        if (isStreamingActive) {
            if (activeEventSource) {
                activeEventSource.close();
                activeEventSource = null;
            }
            finishStreaming();
        }
        // Remove all messages except a fresh one
        while (messagesContainer.firstChild) {
            messagesContainer.removeChild(messagesContainer.firstChild);
        }
        // add fresh assistant greeting
        const freshDiv = document.createElement('div');
        freshDiv.className = 'message assistant';
        freshDiv.innerHTML = `
            <div class="msg-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="message-content">
                <div class="bubble">✨ Conversation cleared. How can I help you today?</div>
                <div class="timestamp">${getCurrentTime()}</div>
            </div>
        `;
        messagesContainer.appendChild(freshDiv);
        scrollToBottom();
        sendBtn.disabled = false;
        messageInput.disabled = false;
        messageInput.focus();
    }

    // Auto-resize textarea
    function adjustTextareaHeight() {
        messageInput.style.height = 'auto';
        messageInput.style.height = Math.min(messageInput.scrollHeight, 140) + 'px';
    }

    // Event listeners
    messageInput.addEventListener('input', adjustTextareaHeight);
    messageInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!sendBtn.disabled && messageInput.value.trim()) {
                sendMessage();
            }
        }
    });

    sendBtn.addEventListener('click', () => {
        if (!sendBtn.disabled && messageInput.value.trim()) {
            sendMessage();
        }
    });

    clearBtn.addEventListener('click', clearConversation);
    scrollDownBtn.addEventListener('click', () => scrollToBottom('smooth'));

    // Focus on load
    window.addEventListener('load', () => {
        messageInput.focus();
        adjustTextareaHeight();
    });

    // If form is accidentally submitted, prevent
    document.querySelectorAll('form').forEach(f => f.addEventListener('submit', (e) => e.preventDefault()));
</script>
</body>
</html>
