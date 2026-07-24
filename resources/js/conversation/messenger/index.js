import SupplierMessengerSidebar from './sidebar';

import ConversationMessages from '../messages';
import ConversationComposer from '../composer';
import ConversationApi from '../api';
import SupportRequestDrawer 
from '../support-request';



class SupplierMessenger
{

    constructor()
{
    this.api = new ConversationApi();

    const conversationList =
        document.getElementById(
            'conversation-list'
        );

        console.log(1);
    this.conversationsUrl =
        conversationList.dataset.url;

    this.deleteMessageUrl =
        conversationList.dataset.deleteMessageUrl ?? null;


    this.sidebar =
        new SupplierMessengerSidebar(
            this.api,
            this.openConversation.bind(this),
            this.conversationsUrl
        );


    this.messages =
        new ConversationMessages(
            'conversation-messages',
            {
                isAdmin: !!this.deleteMessageUrl,

                deleteMessageUrl: this.deleteMessageUrl,

                api: this.api,
            }
        );


    this.composer = new ConversationComposer({
        api: this.api,
        messages: this.messages,

        onMessageSent: (message) => {

            this.lastMessageId = message.id;

            this.sidebar.load();

        },
    });

        
        this.currentConversation = null;
        this.createSupportRequestDrawer =
    new SupportRequestDrawer(
        this.api,
        {
            onCreated: async (response) => {
                await this.sidebar.load();
                await this.openConversation(
                    response.conversation.id
                );
                            }
        }
    );

        this.initSupportDrawer();

        this.initDeleteConversation();

        this.initNoticeDrawer();

        this.pollTimer = null;
        this.lastMessageId = 0;

        window.addEventListener('beforeunload', () => {
            this.stopPolling();
        });

      

    }



    async init()
    {
        this.showEmptyHeader();

        await this.sidebar.load();
    }



    async openConversation(id)
    {

         this.stopPolling();
         

        try {

             console.log('BASE URL =', this.conversationsUrl);
        console.log('ID =', id);
        console.log('URL =', `${this.conversationsUrl}/${id}`);
        

            const response =
    await this.api.request(
        `${this.conversationsUrl}/${id}`
    );



            this.currentConversation =
                response.conversation;



            this.messages.render(
                response.messages ?? []
            );

            if (response.messages.length) {

                this.lastMessageId =
                    response.messages[
                        response.messages.length - 1
                    ].id;

            } else {

                this.lastMessageId = 0;

            }

            this.updateHeader(
                response.header,
                response.has_support,
                response.conversation.type === 'private',
                response.conversation.type
            );



            this.composer.setConversation(id);
            
            

            this.setConversationStatus(
                response.conversation.status
            );


            this.renderConversationActions(
                response.conversation
            );
                        

            await this.api.markAsRead(
              `${this.conversationsUrl}/${id}`
            );

            await this.sidebar.load();

            this.sidebar.setActive(id);

            this.startPolling();


        } catch(error) {

            console.error(error);

        }





    }

    async toggleConversationStatus(conversation)
{
    const closing =
        conversation.status === 'active';

    window.confirmModal.open({

        type: closing ? 'danger' : 'success',

        title: closing
            ? 'Close conversation'
            : 'Reopen conversation',

        message: closing
            ? 'Close this conversation?'
            : 'Reopen this conversation?',

        description: closing
            ? 'The conversation will remain in history and can be reopened later.'
            : 'Participants will be able to send messages again.',

        confirmText: closing
            ? 'Close'
            : 'Reopen',

        onConfirm: async () => {

            try {

                const response =
                    await this.api.request(
                        `${this.conversationsUrl}/${conversation.id}/${closing ? 'close' : 'reopen'}`,
                        'POST'
                    );

                    console.log(response);

                this.currentConversation =
                    response.conversation;

                this.setConversationStatus(
                    response.conversation.status
                );

                this.renderConversationActions(
                    response.conversation
                );

                await this.sidebar.load();

            } catch (e) {

                console.error(e);

            }

        }

    });
}


    setConversationStatus(status)
    {
        const input = document.getElementById('conversation-input');

        const closeButton =
            document.getElementById(
                'conversation-close'
            );

        if (closeButton) {

            if (status === 'active') {

                closeButton.disabled = false;

                closeButton.classList.remove(
                    'opacity-50',
                    'cursor-not-allowed'
                );

            } else {

                closeButton.disabled = true;

                closeButton.classList.add(
                    'opacity-50',
                    'cursor-not-allowed'
                );

            }

        }


        const button =
            document.querySelector(
                '#conversation-form button[type="submit"]'
            );


            const form =
    document.getElementById('conversation-form');

    if (this.currentConversation?.type === 'notice') {

    if (form) {
        form.classList.add('hidden');
    }

    return;
}

if (form) {
    form.classList.remove('hidden');
}



        if (!input || !button) {
            return;
        }

        const active = status === 'active';

        input.disabled = !active;
        button.disabled = !active;

        if (active) {

            input.placeholder = 'Write a message...';

            button.classList.remove(
                'opacity-50',
                'cursor-not-allowed'
            );

        } else {

            input.placeholder =
                'This conversation has been closed.';

            button.classList.add(
                'opacity-50',
                'cursor-not-allowed'
            );

        }
    }



    initSupportDrawer()
{

    const drawer =
        document.getElementById(
            'request-support-drawer'
        );

    if (!drawer) {
        return;
    }


    drawer
        .querySelectorAll('[data-close-support]')
        .forEach(button => {

            button.onclick = () => {

                drawer.classList.add('hidden');

            };

        });


    const submit =
        document.getElementById(
            'request-support-submit'
        );

    if (submit) {

        submit.onclick = async () => {

            const reason =
                document.getElementById(
                    'support-reason'
                ).value;

                

            const response = await this.api.requestSupport(
                `${this.conversationsUrl}/${this.currentConversation.id}`,
                reason
            );

            drawer.classList.add('hidden');

            document.getElementById('support-reason').value = '';

            this.messages.append(response.message);

            this.lastMessageId = response.message.id;

            await this.sidebar.load();

        };

    }

}

 
initDeleteConversation()
{
    const button =
        document.getElementById(
            'conversation-delete'
        );

    if (!button) {
        return;
    }

    button.onclick = () => {

        if (!this.currentConversation) {
            return;
        }

        window.confirmModal.open({

            type: 'danger',

            title: 'Delete conversation',

            message:
                'Delete this conversation?',

            description:
                'This action is permanent. The conversation, all messages and all participants will be permanently deleted.',

            confirmText:
                'Delete',

            onConfirm: async () => {

                try {

                    await this.api.request(
                        `${this.conversationsUrl}/${this.currentConversation.id}`,
                        'DELETE'
                    );

                    this.currentConversation = null;

                    this.messages.render([]);

                    this.showEmptyHeader();

                    document.getElementById(
                        'conversation-header-title'
                    )?.classList.add('hidden');

                    document.getElementById(
                        'conversation-header-subtitle'
                    )?.classList.add('hidden');

                    document.getElementById(
                        'conversation-header-avatar'
                    )?.classList.add('hidden');

                    document.getElementById(
                        'conversation-close'
                    )?.classList.add('hidden');

                    document.getElementById(
                        'conversation-toggle-status'
                    )?.classList.add('hidden');

                    button.classList.add('hidden');

                    await this.sidebar.load();

                } catch (e) {

                    console.error(e);

                }

            }

        });

    };
}


initNoticeDrawer()
{

      console.log('NOTICE INIT');

    const drawer =
        document.getElementById(
            'create-notice-drawer'
        );

    if (!drawer) {
        return;
    }

    const open =
        document.getElementById(
            'conversation-create-notice'
        );

    if (open) {

        open.onclick = () => {

            drawer.classList.remove('hidden');

        };

    }

    drawer
        .querySelectorAll('[data-close-notice]')
        .forEach(button => {

            button.onclick = () => {

                drawer.classList.add('hidden');

            };

        });

        const submit =
    document.getElementById('submit-notice');

if (submit) {

    submit.onclick = async () => {

    const title =
        document.getElementById('notice-title').value.trim();

    const subtitle =
        document.getElementById('notice-subtitle').value.trim();

    const description =
        document.getElementById('notice-description').value.trim();

    if (!title || !description) {
        return;
    }

    try {

        const response =
            await this.api.createNotice({

                title,
                subtitle,
                description,

            });

            console.log(response);

        drawer.classList.add('hidden');

        document.getElementById('notice-title').value = '';
        document.getElementById('notice-subtitle').value = '';
        document.getElementById('notice-description').value = '';

        await this.sidebar.load();

        await this.openConversation(
            response.conversation.id
        );

    } catch (e) {

        console.error(e);

    }

};

}


}

renderConversationActions(conversation)
{
    const button =
        document.getElementById(
            'conversation-toggle-status'
        );

    if (!button) {
        return;
    }

    if (conversation.status === 'active') {

        button.innerHTML = `
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
        d="M6 18L18 6M6 6l12 12"
    />
</svg>

Close conversation
`;

        button.className = `
    inline-flex
    items-center
    gap-2
    px-3
    py-2
    rounded-lg
    border
    border-red-200
    bg-white
    text-red-600
    text-xs
    font-medium
    hover:bg-red-50
    transition
`;

    } else {

        button.innerHTML = `
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
        d="M5 13l4 4L19 7"
    />
</svg>

Reopen conversation
`;

        button.className = `
    inline-flex
    items-center
    gap-2
    px-3
    py-2
    rounded-lg
    border
    border-green-200
    bg-white
    text-green-600
    text-xs
    font-medium
    hover:bg-green-50
    transition
`;

    }

    button.onclick = () =>
        this.toggleConversationStatus(conversation);
}

showEmptyHeader()
{

    

    const support =
        document.getElementById(
            'conversation-request-support'
        );

         

    if (!support) {
        return;
    }


    support.classList.remove('hidden');


    support.onclick = (event) => {

    

    this.createSupportRequestDrawer.open();

};



}


startPolling()
{

    if (this.pollTimer) {
        return;
    }

    this.pollTimer = setInterval(async () => {

        if (!this.currentConversation) {
            return;
        }

        try {

           

            const response =
                await this.api.request(
                    `${this.conversationsUrl}/${this.currentConversation.id}/messages/new?after=${this.lastMessageId}`
                );

            (response.messages ?? []).forEach(message => {
            

                this.messages.append(message);

                this.lastMessageId = message.id;

            });

        } catch (e) {

            console.error(e);

        }

    }, 3000);

}

stopPolling()
{

    clearInterval(this.pollTimer);

    this.pollTimer = null;

}



    updateHeader(header, hasSupport = false, isSupport = false, conversationType = null)
{

    console.log('UPDATE HEADER DEBUG', {
    header,
    hasSupport,
    isSupport,
    conversationType
});


     const support =
        document.getElementById(
            'conversation-request-support'
        );



    if (!header) {


        if (support) {

            support.classList.remove('hidden');

            support.onclick = () => {

                this.createSupportRequestDrawer.open();

            };

        }


        return;
    }

    const title = document.getElementById(
        'conversation-header-title'
    );

    if (title) {
        title.classList.remove('hidden');
        title.innerText =
            header.title ?? '';
    }


    const closeButton =
        document.getElementById(
            'conversation-close'
        );

    if (closeButton) {

        closeButton.classList.remove('hidden');

    }

    const deleteButton =
    document.getElementById(
        'conversation-delete'
    );

if (deleteButton) {
    deleteButton.classList.remove('hidden');
}




    const subtitle = document.getElementById(
        'conversation-header-subtitle'
    );

    if (subtitle) {
        subtitle.classList.remove('hidden');
        subtitle.innerText =
            header.subtitle ?? '';
    }

    const avatar = document.getElementById(
        'conversation-header-avatar'
    );

    if (avatar && header.avatar) {

        avatar.src = header.avatar ?? '/images/default-avatar.png';
    avatar.classList.remove('hidden');
        // Если это <img>
        if (avatar.tagName === 'IMG') {

            avatar.src = header.avatar;

        } else {

            // Пока у тебя div — можно показать первую букву
            avatar.innerText =
                (header.avatar ?? '?')
                    .charAt(0)
                    .toUpperCase();

        }

    }

    const online = document.getElementById(
        'conversation-header-online'
    );

    if (online) {

        online.classList.remove('hidden');
        online.classList.toggle(
            'bg-green-500',
            !!header.contact?.online
        );

        online.classList.toggle(
            'bg-stone-300',
            !header.contact?.online
        );
        
    }


    

if (support) {

    if (
        isSupport ||
        hasSupport ||
        conversationType === 'notice'
    ) {

        support.classList.add('hidden');

    } else {

        support.classList.remove('hidden');

        support.onclick = () => {

            document
                .getElementById('request-support-drawer')
                ?.classList.remove('hidden');

        };

    }

}



    const link = document.getElementById(
    'conversation-header-link'
);

if (link) {

    if (header.url) {

        link.href = header.url;
        link.classList.remove('hidden');

    } else {

        link.classList.add('hidden');

    }
}


}

}



document.addEventListener(
    'DOMContentLoaded',
    ()=>{

        const app =
            new SupplierMessenger();


        app.init();

    }
);