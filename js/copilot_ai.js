document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('copilot-assistant');
    if (!root) {
        return;
    }

    const toggle = document.getElementById('copilot-assistant-toggle');
    const panel = document.getElementById('copilot-assistant-panel');
    const closeButton = document.getElementById('copilot-assistant-close');
    const form = document.getElementById('copilot-assistant-form');
    const input = document.getElementById('copilot-assistant-input');
    const submitButton = document.getElementById('copilot-assistant-submit');
    const messages = document.getElementById('copilot-assistant-messages');
    const historyButton = document.getElementById('copilot-assistant-history');
    const clearButton = document.getElementById('copilot-assistant-clear');
    const deleteButton = document.getElementById('copilot-assistant-delete');
    const historyPanel = document.getElementById('copilot-assistant-history-panel');
    const apiUrl = root.dataset.api || '/php/api/copilot_ai.php';
    const csrfToken = root.dataset.csrf || '';
    let previousResponseId = null;

    const addMessage = (role, text) => {
        if (!messages) {
            return;
        }

        const item = document.createElement('div');
        item.className = `copilot-assistant__message copilot-assistant__message--${role}`;
        item.textContent = text;
        messages.appendChild(item);
        messages.scrollTop = messages.scrollHeight;
    };

    const togglePanel = () => {
        if (!panel) {
            return;
        }

        panel.hidden = !panel.hidden;
        if (toggle) {
            toggle.setAttribute('aria-expanded', String(!panel.hidden));
        }
        if (!panel.hidden && input) {
            input.focus();
        }
    };

    if (toggle) {
        toggle.addEventListener('click', togglePanel);
    }

    if (closeButton) {
        closeButton.addEventListener('click', () => {
            if (panel) {
                panel.hidden = true;
            }
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    if (historyButton) {
        historyButton.addEventListener('click', async () => {
            if (!historyPanel) return;
            historyPanel.hidden = false;
            historyPanel.textContent = 'Cargando historial...';
            try {
                const response = await fetch(`${apiUrl}?accion=historial`, { credentials: 'same-origin' });
                const data = await response.json();
                if (!response.ok || data.success !== true) throw new Error(data.message || 'No se pudo cargar el historial.');
                historyPanel.innerHTML = '';
                if (!data.items?.length) {
                    historyPanel.textContent = 'Todavía no hay chats anteriores.';
                    return;
                }
                data.items.reverse().forEach((item) => {
                    const entry = document.createElement('article');
                    entry.className = 'copilot-assistant__history-entry';
                    const question = document.createElement('p');
                    question.textContent = `Vos: ${item.mensaje}`;
                    const answer = document.createElement('p');
                    answer.textContent = `IA: ${item.respuesta}`;
                    entry.append(question, answer);
                    historyPanel.appendChild(entry);
                });
            } catch (error) {
                historyPanel.textContent = error.message;
            }
        });
    }

    if (deleteButton) {
        deleteButton.addEventListener('click', async () => {
            if (!window.confirm('¿Querés eliminar todo tu historial del asistente?')) return;
            const response = await fetch(apiUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: new URLSearchParams({ accion: 'borrar_historial', csrf_token: csrfToken }),
            });
            const data = await response.json();
            if (!response.ok || data.success !== true) {
                window.alert(data.message || 'No se pudo eliminar el historial.');
                return;
            }
            previousResponseId = null;
            if (historyPanel) {
                historyPanel.hidden = true;
                historyPanel.textContent = '';
            }
        });
    }

    if (clearButton) {
        clearButton.addEventListener('click', () => {
            previousResponseId = null;
            if (input) input.value = '';
            if (historyPanel) {
                historyPanel.hidden = true;
                historyPanel.textContent = '';
            }
            if (messages) {
                messages.innerHTML = '<div class="copilot-assistant__message copilot-assistant__message--bot">Hola 👋 Soy el asistente virtual de Sigtur Salto. ¿En qué puedo ayudarte?</div>';
            }
            if (input) input.focus();
        });
    }

    if (form) {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (!input || !submitButton) {
                return;
            }

            const question = input.value.trim();
            if (!question) {
                return;
            }

            addMessage('user', question);
            input.value = '';
            input.disabled = true;
            submitButton.disabled = true;
            submitButton.textContent = 'Pensando...';

            const waitingMessage = document.createElement('div');
            waitingMessage.className = 'copilot-assistant__message copilot-assistant__message--bot copilot-assistant__message--loading';
            waitingMessage.textContent = 'Estoy pensando la mejor respuesta...';
            messages.appendChild(waitingMessage);
            messages.scrollTop = messages.scrollHeight;

            try {
                const requestBody = { message: question };
                if (previousResponseId) {
                    requestBody.previous_response_id = previousResponseId;
                }

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                    body: new URLSearchParams(requestBody),
                });

                const data = await response.json();
                if (data?.response_id) {
                    previousResponseId = data.response_id;
                }
                const answer = data?.answer || data?.message || 'No pude responder esta consulta en este momento.';
                const lastBotMessage = messages.querySelector('.copilot-assistant__message--loading');
                if (lastBotMessage) {
                    lastBotMessage.classList.remove('copilot-assistant__message--loading');
                    lastBotMessage.textContent = answer;
                } else {
                    addMessage('bot', answer);
                }
            } catch (error) {
                const lastBotMessage = messages.querySelector('.copilot-assistant__message--loading');
                if (lastBotMessage) {
                    lastBotMessage.classList.remove('copilot-assistant__message--loading');
                    lastBotMessage.textContent = 'No pude responder ahora mismo. Probá con otra pregunta.';
                } else {
                    addMessage('bot', 'No pude responder ahora mismo. Probá con otra pregunta.');
                }
            } finally {
                input.disabled = false;
                submitButton.disabled = false;
                submitButton.textContent = 'Enviar';
                input.focus();
            }
        });
    }
});
