export default class SupplierMessengerSidebar
{


    constructor(api, openCallback, conversationsUrl)
{
    this.api = api;

    this.openCallback =
        openCallback;

    this.conversationsUrl =
        conversationsUrl;

    this.container =
        document.getElementById(
            'conversation-list'
        );

    this.activeConversationId = null;
}



    async load()
    {

        console.log('Messenger loading');

        const response =
    await this.api.request(
        this.conversationsUrl
    );

            console.log(
        'API response:',
        response
    );
    

        this.render(
            response.conversations ?? []
        );

    }




    render(conversations)
    {

        if(!this.container)
            return;



        this.container.innerHTML = '';



        conversations.forEach(
            conversation=>{


                this.container.insertAdjacentHTML(
                    'beforeend',
                    this.template(conversation)
                );


            }
        );



        this.bind();

        if (this.activeConversationId) {
        this.setActive(this.activeConversationId);
}

    }




    bind()
    {

        this.container
            .querySelectorAll(
                '[data-conversation-id]'
            )
            .forEach(button=>{


                button.addEventListener(
                    'click',
                    ()=>{

                        this.setActive(
                            button.dataset.conversationId
                        );

                        this.openCallback(
                            button.dataset.conversationId
                        );


                    }
                );


            });

    }


setActive(conversationId)
{

    this.activeConversationId = conversationId;

    this.container
        .querySelectorAll('.conversation-item')
        .forEach(item => {

            item.classList.remove(
                'bg-stone-100',
                
            );

            item.classList.add(
                
            );
        });

    const active = this.container.querySelector(
        `[data-conversation-id="${conversationId}"]`
    );

    if (!active) {
        return;
    }

    active.classList.remove(
        'border-l-transparent'
    );

    active.classList.add(
        'bg-stone-100',
        'border-l-stone-900'
    );
}

    template(conversation)
{
    const unread =
        (conversation.unread ?? 0) > 0;

    return `

    <button
        data-conversation-id="${conversation.id}"
        class="
            conversation-item
            w-full
            px-5
            py-4
            border-b
            border-stone-100
            hover:bg-stone-50
            text-left
            flex
            gap-3
            transition
        "
    >

        <img
            src="${conversation.header?.avatar ?? '/images/no-image.png'}"
            class="
                w-10
                h-10
                rounded-full
                object-cover
                shrink-0
            "
        >

        <div class="flex-1 min-w-0">

            <div class="flex items-start justify-between gap-3">

                <div
                    class="
                        flex-1
                        min-w-0
                        text-sm
                        truncate
                        ${unread
                            ? 'font-semibold text-stone-900'
                            : 'font-medium text-stone-900'}
                    "
                >
                    ${conversation.header?.title ?? 'Conversation'}
                </div>

                <span
                    class="
                        shrink-0
                        text-xs
                        text-stone-400
                    "
                >
                    ${conversation.updated_at ?? ''}
                </span>

            </div>

            <div
                class="
                    mt-1
                    flex
                    items-center
                    gap-2
                    min-w-0
                "
            >

                <span
                    class="
                        text-xs
                        text-stone-500
                        truncate
                        flex-1
                    "
                >
                    ${conversation.header?.subtitle ?? 'Conversation'}
                </span>

                ${
                    conversation.has_support
                    ? `
                        <span
                            class="
                                inline-flex
                                items-center
                                px-2
                                py-0.5
                                rounded-full
                                text-[10px]
                                font-semibold
                                bg-amber-100
                                text-amber-700
                                shrink-0
                            "
                        >
                            Support
                        </span>
                    `
                    : ''
                }

                ${
                    unread
                    ? `
                        <span
                            class="
                                w-2
                                h-2
                                rounded-full
                                bg-blue-600
                                shrink-0
                            "
                            title="Unread"
                        ></span>
                    `
                    : ''
                }

            </div>

            <div
                class="
                    mt-2
                    text-sm
                    truncate
                    ${unread
                        ? 'font-medium text-stone-700'
                        : 'text-stone-500'}
                "
            >
                ${conversation.last_message ?? 'No messages'}
            </div>

        </div>

    </button>

    `;
}


}