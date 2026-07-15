<?php

namespace App\Domain\Conversation\Enums;

enum MessageType: string
{
    /**
     * Обычное текстовое сообщение.
     */
    case TEXT = 'text';

    /**
     * Системное сообщение.
     */
    case SYSTEM = 'system';

    /**
     * Изображение.
     * Зарезервировано на будущее.
     */
    case IMAGE = 'image';

    /**
     * Файлы.
     * Зарезервировано на будущее.
     */
    case FILE = 'file';

    /**
     * Голосовое сообщение.
     * Зарезервировано на будущее.
     */
    case AUDIO = 'audio';

    /**
     * Видео.
     * Зарезервировано на будущее.
     */
    case VIDEO = 'video';

    /**
     * Событие системы.
     */
    case EVENT = 'event';

    /**
     * Все значения enum.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Название для UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::TEXT => 'Text',
            self::SYSTEM => 'System',
            self::IMAGE => 'Image',
            self::FILE => 'File',
            self::AUDIO => 'Audio',
            self::VIDEO => 'Video',
            self::EVENT => 'Event',
        };
    }

    /**
     * Является ли сообщение системным.
     */
    public function isSystem(): bool
    {
        return in_array($this, [
            self::SYSTEM,
            self::EVENT,
        ], true);
    }

    /**
     * Пользовательское сообщение.
     */
    public function isUserMessage(): bool
    {
        return !$this->isSystem();
    }
}