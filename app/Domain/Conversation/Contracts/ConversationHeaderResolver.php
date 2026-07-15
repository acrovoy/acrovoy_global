<?php

namespace App\Domain\Conversation\Contracts;

use App\Domain\Conversation\Models\Conversation;

interface ConversationHeaderResolver
{
    /**
     * Может ли Resolver обработать данный Subject.
     */
    public function supports(string $subjectType): bool;

    /**
     * Построить Header Conversation.
     *
     * Структура:
     *
     * [
     *     'subject' => [
     *         'title' => string,
     *         'subtitle' => ?string,
     *         'avatar' => ?string,
     *     ],
     *
     *     'company' => [
     *         'id' => ?int,
     *         'name' => ?string,
     *         'logo' => ?string,
     *     ],
     *
     *     'contact' => [
     *         'id' => ?int,
     *         'name' => ?string,
     *         'avatar' => ?string,
     *         'position' => ?string,
     *         'online' => bool,
     *         'last_seen' => ?string,
     *     ],
     * ]
     */
    public function resolve(Conversation $conversation): array;
}