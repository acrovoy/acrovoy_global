export default class ConversationMessages
{
    constructor(containerId = 'conversation-messages')
    {
        this.container = document.getElementById(containerId);
    }

    /**
     * Полностью очистить чат.
     */
    clear()
    {
        this.container.innerHTML = '';
    }

    /**
     * Нарисовать список сообщений.
     */
    render(messages = [])
    {
        this.clear();

        messages.forEach(message => {
            this.append(message);
        });

        this.scrollToBottom();
    }

    /**
     * Добавить одно сообщение.
     */
    append(message)
    {
        this.container.insertAdjacentHTML(
            'beforeend',
            this.messageTemplate(message)
        );

        this.scrollToBottom();
    }

    /**
     * Добавить сообщение в начало
     * (для будущего Infinite Scroll).
     */
    prepend(message)
    {
        this.container.insertAdjacentHTML(
            'afterbegin',
            this.messageTemplate(message)
        );
    }

    /**
     * Удалить сообщение.
     */
    remove(messageId)
    {
        const element = this.container.querySelector(
            `[data-message-id="${messageId}"]`
        );

        if (element) {
            element.remove();
        }
    }

    /**
     * Обновить сообщение.
     */
    update(message)
    {
        const old = this.container.querySelector(
            `[data-message-id="${message.id}"]`
        );

        if (!old) {
            return;
        }

        old.outerHTML = this.messageTemplate(message);
    }

    /**
     * Скролл вниз.
     */
    scrollToBottom()
    {
        this.container.scrollTop = this.container.scrollHeight;
    }

    /**
     * HTML одного сообщения.
     */
    /**
 * HTML одного сообщения.
 */
messageTemplate(message)
{

    console.log(message);
    const mine = message.is_mine === true;

    const sender = message.sender ?? {};

    const avatar =
        sender.avatar ??
        '/images/default-avatar.png';

    const senderName =
        sender.name ??
        'Unknown';

    const position =
        sender.position ??
        '';

    const company =
        sender.company ??
        '';

    const created =
        message.created_at ??
        '';

    const text =
        message.message ??
        '';

    return `
        <div
            class="flex ${mine ? 'justify-end' : 'justify-start'} mb-4"
            data-message-id="${message.id}"
        >

            <div class="flex items-end gap-3 max-w-[80%]">

                ${
                    !mine
                        ? `
                            <img
                                src="${avatar}"
                                class="w-10 h-10 rounded-full object-cover shrink-0"
                            >
                        `
                        : ''
                }

                <div class="${mine ? 'items-end' : ''} flex flex-col">

                    ${
                        !mine
                            ? `
                                <div class="flex items-center gap-2 mb-1">

                                    <span class="font-semibold text-sm">
                                        ${senderName}
                                    </span>

                                    ${
                                        position
                                            ? `<span class="text-xs text-gray-500">${position}</span>`
                                            : ''
                                    }

                                    ${
                                        company
                                            ? `<span class="text-xs text-gray-400">${company}</span>`
                                            : ''
                                    }

                                </div>
                            `
                            : ''
                    }

                    <div
                        class="
                            max-w-full
                            rounded-2xl
                            px-4
                            py-3
                            text-sm
                            whitespace-pre-wrap
                            break-words
                            ${
                                mine
                                    ? 'bg-gray-900 text-white rounded-br-md'
                                    : 'bg-gray-100 text-gray-900 rounded-bl-md'
                            }
                        "
                    >
                        ${text}
                    </div>

                    <div
                        class="
                            mt-1
                            text-[11px]
                            text-gray-400
                            ${mine ? 'self-end' : ''}
                        "
                    >
                        ${created}
                    </div>

                </div>

            </div>

        </div>
    `;
}


}