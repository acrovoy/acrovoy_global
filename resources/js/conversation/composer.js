import ConversationApi from './api';

export default class ConversationComposer
{
    constructor(options = {})
{
    this.api = options.api || new ConversationApi();

    this.messages = options.messages;

    this.drawer = options.drawer ?? null;

    this.conversationId = null;

    this.form = document.getElementById('conversation-form');

    this.input = document.getElementById('conversation-input');

    this.sendButton = this.form?.querySelector(
        'button[type="submit"]'
    );

    this.bind();
}

    bind()
    {
        if (!this.form) {
            return;
        }

        this.form.addEventListener(
            'submit',
            (e) => {

                e.preventDefault();

                this.send();

            }
        );

        this.input.addEventListener(
            'keydown',
            (e) => {

                /*
                 * Enter = отправить
                 * Shift + Enter = новая строка
                 */

                if (
                    e.key === 'Enter' &&
                    !e.shiftKey
                ) {
                    e.preventDefault();

                    this.send();
                }

            }
        );
    }

    async send()
    {
        const text = this.input.value.trim();

        if (!text) {
            return;
        }

        const conversationId = this.conversationId;



        if (!conversationId) {
            console.error(
                'Conversation ID not found.'
            );

            return;
        }

        this.disable();

        try {

            const response =
                await this.api.sendMessage(conversationId, text);

            /*
             * Очищаем textarea
             */

            this.input.value = '';

            /*
             * Добавляем сообщение в чат
             */

            if (
                this.messages &&
                response.message
            ) {

                this.messages.append(
                    response.message
                );

            }

        }
        catch (e) {

            console.error(e);

            alert('Unable to send message.');

        }
        finally {

            this.enable();

            this.input.focus();

        }
    }

    disable()
    {
        if (this.sendButton) {

            this.sendButton.disabled = true;

        }
    }

    enable()
    {
        if (this.sendButton) {

            this.sendButton.disabled = false;

        }
    }

    setConversation(conversationId)
{
    this.conversationId = conversationId;

    if (this.drawer) {
        this.drawer.dataset.conversationId = conversationId;
    }
}


}