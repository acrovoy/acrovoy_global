<?php

namespace App\Domain\Conversation\Services;

use App\Domain\Conversation\Contracts\ConversationHeaderResolver;
use App\Domain\Conversation\Models\Conversation;
use RuntimeException;

class ConversationHeaderService
{
    /**
     * @var ConversationHeaderResolver[]
     */
    protected array $resolvers;

    public function __construct(iterable $resolvers = [])
    {
        $this->resolvers = [];

        foreach ($resolvers as $resolver) {
            $this->addResolver($resolver);
        }
    }

    /**
     * Зарегистрировать Resolver.
     */
    public function addResolver(
        ConversationHeaderResolver $resolver
    ): static {

        $this->resolvers[] = $resolver;

        return $this;
    }

    /**
     * Построить Header Conversation.
     *
     * @throws RuntimeException
     */
    public function resolve(
        Conversation $conversation
    ): array {

        foreach ($this->resolvers as $resolver) {
  
            if ($resolver->supports($conversation)) {

                return $resolver->resolve($conversation);

            }

        }

        throw new RuntimeException(
            sprintf(
                'Conversation Header Resolver not found for conversation [%s].',
                $conversation->id
            )
        );
    }

    /**
     * Есть ли Resolver.
     */
    public function hasResolver(
        Conversation $conversation
    ): bool {

        foreach ($this->resolvers as $resolver) {

            if ($resolver->supports($conversation)) {
                return true;
            }

        }

        return false;
    }

    /**
     * Вернуть все зарегистрированные Resolver'ы.
     */
    public function resolvers(): array
    {
        return $this->resolvers;
    }
}