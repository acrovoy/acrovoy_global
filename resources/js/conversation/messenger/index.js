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
       
        this.conversationsUrl = document.getElementById('conversation-list')
        .dataset.url;


        this.sidebar =
        new SupplierMessengerSidebar(
            this.api,
            this.openConversation.bind(this),
            this.conversationsUrl
        );


        this.messages = new ConversationMessages('conversation-messages');

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
                response.conversation.type === 'private'
            );



            this.composer.setConversation(id);
            
            

            this.setConversationStatus(
                response.conversation.status
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

    setConversationStatus(status)
    {
        const input = document.getElementById('conversation-input');

        const button =
            document.querySelector(
                '#conversation-form button[type="submit"]'
            );

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



    updateHeader(header, hasSupport = false, isSupport = false)
{

    


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

    if (isSupport || hasSupport) {

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