export default class ConversationMessages
{
    constructor(
    containerId = 'conversation-messages',
    options = {}
)
{
    this.container =
        document.getElementById(containerId);

    this.options = {

        isAdmin: false,

        deleteMessageUrl: null,

        api: null,

        ...options,

        

    };

    this.bindEvents();
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


    bindEvents()
{
    this.container.addEventListener(
        'click',
        (event) => {

            const button =
                event.target.closest(
                    '[data-delete-message]'
                );

            if (!button) {
                return;
            }

            const wrapper =
                button.closest(
                    '[data-message-id]'
                );

            if (!wrapper) {
                return;
            }

            const id =
                wrapper.dataset.messageId;

            if (!id) {
                return;
            }

            if (!window.confirmModal) {
                return;
            }

            window.confirmModal.open({

                type: 'danger',

                title: 'Delete message',

                description:
                    'Permanent action',

                message:
                    'Delete this message?',

                confirmText:
                    'Delete',

                onConfirm: async () => {

                    try {

                        await this.options.api.request(
    this.options.deleteMessageUrl.replace(':id', id),
    'DELETE'
);

                        this.remove(id);

                    } catch (e) {

                        console.error(e);

                    }

                }

            });

        }
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

const systemText =
    (message.message ?? '')
        .replace(/\n/g, '<br>');


    if (message.type === 'system') {

    return `
        <div
            class="my-6"
            data-message-id="${message.id}"
        >

            <div
                class="
                    w-full
                    rounded-xl
                    border
                    border-amber-200
                    bg-amber-50
                    px-5
                    py-4
                "
            >

                <div
                    class="
                        flex
                        items-center
                        justify-between
                        mb-1
                    "
                >

                    <span
                        class="
                            text-[11px]
                            uppercase
                            tracking-wider
                            font-semibold
                            text-amber-700
                        "
                    >
                        Notice
                    </span>

                    

                </div>

                <div
                    class="
                        text-sm
                        leading-6
                        whitespace-pre-wrap
                        text-stone-700
                    "
                >
                    ${systemText}
                </div>

            </div>
<span
                        class="
                            text-[11px]
                            text-stone-400
                        "
                    >
                        ${message.created_at ?? ''}
                    </span>
        </div>
    `;
}


    
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
    class="
        flex
        ${mine ? 'justify-end' : 'justify-start'}
        mb-4
        group
    "
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

                <div
                    class="
                        ${mine ? 'items-end' : ''}
                        flex
                        flex-col
                        relative
                    "
                >

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
        flex
        items-center
        gap-2
        text-[11px]
        text-gray-400
        ${mine ? 'justify-end' : ''}
    "
>

    <span>
        ${created}
    </span>

    ${
        this.options.isAdmin
            ? `
                <button
                    type="button"
                    data-delete-message
                    class="
                        delete-message-btn
                        flex
                        items-center
                        justify-center
                        w-5
                        h-5
                        text-red-500
                        hover:text-red-700
                        transition-all
                        duration-150
                    "
                    title="Delete message"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 7L18.133 19.142A2 2 0 0116.138 21H7.862A2 2 0 015.867 19.142L5 7m5 4v6m4-6v6M9 7V4h6v3"
                        />
                    </svg>

                </button>
            `
            : ''
    }

</div>

                </div>

            </div>

        </div>
    `;
}


}

