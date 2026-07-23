import ConversationApi from './api';
import ConversationMessages from './messages';
import ConversationComposer from './composer';

export default class ConversationDrawer
{
    constructor()
    {
        this.api = new ConversationApi();

        this.drawer = document.getElementById('conversation-drawer');
        this.conversationsUrl = this.drawer.dataset.messagesUrl;
        this.overlay = document.getElementById('conversation-overlay');
        this.closeButton = document.getElementById('close-conversation');

        this.title = document.getElementById('conversation-title');
        this.avatar = document.getElementById('conversation-avatar');
        this.status = document.getElementById('conversation-status');

        this.messages = new ConversationMessages();

        this.composer = new ConversationComposer({
            api: this.api,
            messages: this.messages,
            drawer: this.drawer,
        });

        this.bind();

        this.currentConversation = null;
        this.pollTimer = null;
        this.lastMessageId = 0;

        window.addEventListener('beforeunload', () => {
            this.stopPolling();
        });
    }

    bind()
    {
        document.addEventListener('click', (e) => {

            const button = e.target.closest('.open-conversation');

            if (!button) {
                return;
            }

            e.preventDefault();

            this.open(
                button.dataset.subjectType,
                button.dataset.subjectId
            );

        });

        this.closeButton?.addEventListener(
            'click',
            () => this.close()
        );

        this.overlay?.addEventListener(
            'click',
            () => this.close()
        );

        document.addEventListener('keydown', (e) => {

            if (e.key === 'Escape') {
                this.close();
            }

        });
    }

    async open(subjectType, subjectId)
    {
        try {

            this.stopPolling();

            const data =
                await this.api.openConversation(
                    subjectType,
                    subjectId
                );


                

            this.currentConversation = data.conversation;

            this.drawer.dataset.subjectType = subjectType;
            this.drawer.dataset.subjectId = subjectId;
            this.drawer.dataset.conversationId =
                data.conversation.id;

            this.renderHeader(data.header);

            this.messages.render(data.messages);

            if (data.messages.length) {

                this.lastMessageId =
                    data.messages[data.messages.length - 1].id;

            } else {

                this.lastMessageId = 0;

            }


            this.drawer.classList.remove('translate-x-full');
            this.overlay.classList.remove('hidden');

            this.composer.setConversation(
                data.conversation.id
            );

            this.setConversationStatus(
                data.conversation.status
            );

            


            this.startPolling();

        }
        catch (e) {

            console.error(e);

            alert('Unable to open conversation.');

        }
    }

    close()
    {
        this.stopPolling();

        this.drawer.classList.add('translate-x-full');
        this.overlay.classList.add('hidden');

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


    setConversationStatus(status)
    {

        


        const input = document.getElementById('conversation-input');
        const button = document.getElementById('conversation-send');

         


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


    renderHeader(header)
    {
        this.title.textContent =
            header.title ?? '';

        this.status.textContent =
            header.online
                ? 'Online'
                : (header.last_seen ?? '');

        this.avatar.src =
            header.avatar ??
            '/images/default-avatar.png';
    }
}