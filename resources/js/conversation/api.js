export default class ConversationApi
{
    constructor()
    {
        this.csrf = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');
    }

    /**
     * Универсальный запрос.
     */
    async request(url, method = 'GET', data = null)
    {
        const options = {
            method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrf,
            },
        };

        if (data !== null) {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(url, options);

        if (!response.ok) {

            let error = {};

            try {
                error = await response.json();
            } catch (_) {}

            throw new Error(
                error.message || `HTTP ${response.status}`
            );
        }

        return await response.json();
    }

    /**
     * Открыть или создать Conversation.
     */
    async openConversation(subjectType, subjectId)
    {
        return this.request(
            '/conversations/open',
            'POST',
            {
                subject_type: subjectType,
                subject_id: subjectId,
            }
        );
    }

    /**
     * Отправить сообщение.
     */
    async sendMessage(conversationId, message, media = [])
    {
        return this.request(
            '/conversations/message',
            'POST',
            {
                conversation_id: conversationId,
                message,
                media,
            }
        );
    }

    /**
     * Получить сообщения.
     */
    async loadMessages(conversationId)
    {
        return this.request(
            `/conversations/${conversationId}/messages`
        );
    }

    /**
     * Отметить Conversation прочитанным.
     */
    async markAsRead(conversationUrl)
    {
        return this.request(
            `${conversationUrl}/read`,
            'POST'
        );
    }

    /**
     * Request Support.
     */
    async requestSupport(conversationUrl, reason)
{
    return this.request(
        `${conversationUrl}/support`,
        'POST',
        {
            reason: reason,
        }
    );
}







    /**
     * Загрузить вложения.
     */
    async uploadAttachments(files)
    {
        const formData = new FormData();

        files.forEach(file => {
            formData.append('files[]', file);
        });

        const response = await fetch(
            '/media/upload',
            {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrf,
                    'Accept': 'application/json',
                },
                body: formData,
            }
        );

        if (!response.ok) {
            throw new Error('Upload failed');
        }

        return await response.json();
    }

    /**
     * Удалить сообщение.
     */
    async deleteMessage(messageId)
    {
        return this.request(
            `/conversations/messages/${messageId}`,
            'DELETE'
        );
    }

    /**
     * Обновить сообщение.
     */
    async updateMessage(messageId, message)
    {
        return this.request(
            `/conversations/messages/${messageId}`,
            'PATCH',
            {
                message,
            }
        );
    }
}