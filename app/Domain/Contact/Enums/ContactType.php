<?php

namespace App\Domain\Contact\Enums;

enum ContactType: string
{
    case Phone = 'phone';
    case Email = 'email';

    case Telegram = 'telegram';
    case Whatsapp = 'whatsapp';
    case Viber = 'viber';
    case Signal = 'signal';

    case Website = 'website';

    case Linkedin = 'linkedin';
    case Facebook = 'facebook';
    case Instagram = 'instagram';
    case X = 'x';

    /**
     * Human readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Phone     => 'Phone',
            self::Email     => 'Email',
            self::Telegram  => 'Telegram',
            self::Whatsapp  => 'WhatsApp',
            self::Viber     => 'Viber',
            self::Signal    => 'Signal',
            self::Website   => 'Website',
            self::Linkedin  => 'LinkedIn',
            self::Facebook  => 'Facebook',
            self::Instagram => 'Instagram',
            self::X         => 'X (Twitter)',
        };
    }

    /**
     * Heroicons name (или название иконки из твоей библиотеки).
     */
    public function icon(): string
{
    return match ($this) {
        self::Phone     => 'phone',
        self::Email     => 'email',

        self::Telegram  => 'telegram',
        self::Whatsapp  => 'whatsapp',
        self::Viber     => 'viber',
        self::Signal    => 'signal',

        self::Website   => 'website',

        self::Linkedin  => 'linkedin',
        self::Facebook  => 'facebook',
        self::Instagram => 'instagram',
        self::X         => 'x',
    };
}

    /**
     * Placeholder для формы.
     */
    public function placeholder(): string
    {
        return match ($this) {

            self::Phone =>
                '+380501234567',

            self::Email =>
                'info@company.com',

            self::Telegram =>
                'acrovoy',

            self::Whatsapp,
            self::Viber,
            self::Signal =>
                '+380501234567',

            self::Website =>
                'https://company.com',

            self::Linkedin =>
                'https://linkedin.com/company/company',

            self::Facebook =>
                'https://facebook.com/company',

            self::Instagram =>
                'company',

            self::X =>
                'company',
        };
    }

    /**
     * Форматирует отображаемое значение.
     */
    public function format(string $value): string
    {
        return match ($this) {

            self::Telegram =>
                '@' . ltrim($value, '@'),

            self::Instagram,
            self::X =>
                '@' . ltrim($value, '@'),

            default =>
                $value,
        };
    }

    /**
     * Генерирует ссылку.
     */
    public function url(string $value): ?string
    {
        return match ($this) {

            self::Phone =>
                'tel:' . $value,

            self::Email =>
                'mailto:' . $value,

            self::Telegram =>
                'https://t.me/' . ltrim($value, '@'),

            self::Whatsapp =>
                'https://wa.me/' . preg_replace('/\D/', '', $value),

            self::Viber =>
                'viber://chat?number=' . preg_replace('/\D/', '', $value),

            self::Signal =>
                'https://signal.me/#p/' . preg_replace('/\D/', '', $value),

            self::Website,
            self::Linkedin,
            self::Facebook =>
                $value,

            self::Instagram =>
                'https://instagram.com/' . ltrim($value, '@'),

            self::X =>
                'https://x.com/' . ltrim($value, '@'),
        };
    }

    /**
     * Laravel validation rules.
     */
    public function validationRules(): array
    {
        return match ($this) {

            self::Phone => [
                'required',
                'string',
                'max:30',
            ],

            self::Email => [
                'required',
                'email',
                'max:255',
            ],

            self::Telegram,
            self::Instagram,
            self::X => [
                'required',
                'string',
                'max:100',
            ],

            self::Whatsapp,
            self::Viber,
            self::Signal => [
                'required',
                'string',
                'max:30',
            ],

            self::Website,
            self::Linkedin,
            self::Facebook => [
                'required',
                'url',
                'max:255',
            ],
        };
    }

    /**
     * Список для Select.
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn (self $type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ])
            ->values()
            ->all();
    }

    public function defaults(): array
{
    return [
        'placeholder' => $this->placeholder(),
        'label' => match ($this) {

            self::Phone     => 'Mobile',
            self::Email     => 'Work',

            self::Telegram  => 'Telegram',
            self::Whatsapp  => 'WhatsApp',
            self::Viber     => 'Viber',
            self::Signal    => 'Signal',

            self::Website   => 'Website',

            self::Linkedin  => 'LinkedIn',
            self::Facebook  => 'Facebook',
            self::Instagram => 'Instagram',
            self::X         => 'X',

        },
    ];
}

public static function frontend(): array
{
    return collect(self::cases())
        ->mapWithKeys(fn (self $type) => [

            $type->value => $type->defaults(),

        ])
        ->toArray();
}

}