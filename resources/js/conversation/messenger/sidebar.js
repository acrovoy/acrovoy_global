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

    console.log(
        'Conversation list:',
        this.container
    );
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


                        this.openCallback(
                            button.dataset.conversationId
                        );


                    }
                );


            });

    }




    template(conversation)
    {

        return `

        <button
            data-conversation-id="${conversation.id}"
            class="
                w-full
                px-5
                py-4
                border-b
                border-stone-100
                hover:bg-stone-50
                text-left
                flex
                gap-3
            "
        >


            <img
    src="${conversation.header?.avatar ?? '/images/no-image.png'}"
    class="
        w-10
        h-10
        rounded-full
        object-cover
    "
>



            <div
                class="flex-1 min-w-0"
            >


                <div
                    class="
                        flex
                        justify-between
                    "
                >

                    <span
                        class="
                            text-sm
                            font-medium
                            text-stone-900
                        "
                    >
                        ${conversation.header?.title ?? 'Conversation'}
                    </span>


                    <span
                        class="
                            text-xs
                            text-stone-400
                        "
                    >
                        ${conversation.updated_at ?? ''}
                    </span>


                </div>



                <div
                    class="
                        text-sm
                        text-stone-500
                        truncate
                        mt-1
                    "
                >
                    ${conversation.last_message ?? 'No messages'}
                </div>


            </div>


        </button>

        `;

    }


}