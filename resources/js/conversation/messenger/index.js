import SupplierMessengerSidebar from './sidebar';

import ConversationMessages from '../messages';
import ConversationComposer from '../composer';
import ConversationApi from '../api';



class SupplierMessenger
{

    constructor()
    {
        this.api = new ConversationApi();

        this.conversationsUrl =
    document
        .getElementById('conversation-list')
        .dataset.url;


        this.sidebar =
    new SupplierMessengerSidebar(
        this.api,
        this.openConversation.bind(this),
        this.conversationsUrl
    );


        this.messages =
            new ConversationMessages(
                'conversation-messages'
            );


        this.composer = new ConversationComposer({
            api: this.api,
            messages: this.messages,
        });


        this.currentConversation = null;
    }



    async init()
    {
        await this.sidebar.load();
    }



    async openConversation(id)
    {

        try {


            const response =
    await this.api.request(
        `${this.conversationsUrl}/${id}`
    );

console.log(response.header);

            this.currentConversation =
                response.conversation;



            this.messages.render(
                response.messages ?? []
            );

            this.updateHeader(
                response.header
            );



            this.composer.setConversation(id);


        } catch(error) {

            console.error(error);

        }

    }



    updateHeader(header)
{

    console.log(header);


    if (!header) {
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