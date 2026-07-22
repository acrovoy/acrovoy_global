import ConversationApi from './api';

export default class ConversationComposer
{
    constructor(options = {})
{
    this.api = options.api || new ConversationApi();

    this.messages = options.messages;

    this.drawer = options.drawer ?? null;

    this.onMessageSent = options.onMessageSent ?? null;

    this.conversationId = null;

    this.form = document.getElementById('conversation-form');

    this.input = document.getElementById('conversation-input');

    this.sendButton = this.form?.querySelector(
        'button[type="submit"]'
    );

    this.bind();

    this.disable();
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


               

                if (this.onMessageSent) {
    this.onMessageSent(response.message);
}

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


            if (this.onMessageSent) {
                this.onMessageSent(response.message);
            }


        }
        catch (e) {

            console.error(e);

            alert('Unable to send message.');

        }
        finally {

    if (this.conversationId) {
        this.enable();
        this.input.focus();
    }

}
    }

    disable()
{
    if (this.sendButton) {
        this.sendButton.disabled = true;
    }

    if (this.input) {
        this.input.disabled = true;
        this.input.placeholder =
            'Select a conversation to start messaging...';
    }
}

    enable()
{
    if (this.sendButton) {
        this.sendButton.disabled = false;
    }

    if (this.input) {
        this.input.disabled = false;
        this.input.placeholder =
            'Write a message...';
    }
}

    setConversation(conversationId)
{
    this.conversationId = conversationId;

    if (this.drawer) {
        this.drawer.dataset.conversationId = conversationId;
    }

    if (conversationId) {
        this.enable();
    } else {
        this.disable();
    }

}


}