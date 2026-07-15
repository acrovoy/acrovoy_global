import ConversationApi from './api';
import ConversationMessages from './messages';
import ConversationComposer from './composer';

export default class ConversationDrawer
{
    constructor()
    {
        this.api = new ConversationApi();

        this.drawer = document.getElementById('conversation-drawer');
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

            const data =
                await this.api.openConversation(
                    subjectType,
                    subjectId
                );

            this.drawer.dataset.subjectType = subjectType;
            this.drawer.dataset.subjectId = subjectId;
            this.drawer.dataset.conversationId =
                data.conversation.id;

            this.renderHeader(data.header);

            this.messages.render(data.messages);

            this.drawer.classList.remove('translate-x-full');
            this.overlay.classList.remove('hidden');

            this.composer.setConversation(
                data.conversation.id
            );

        }
        catch (e) {

            console.error(e);

            alert('Unable to open conversation.');

        }
    }

    close()
    {
        this.drawer.classList.add('translate-x-full');
        this.overlay.classList.add('hidden');
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