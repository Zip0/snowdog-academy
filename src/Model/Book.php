<?php

namespace Snowdog\Academy\Model;

use DateInterval;
use DateTime;
use http\QueryString;

class Book
{
    private const DATETIME_FORMAT = 'Y-m-d H:i:s';

    private int $id;
    private string $title;
    private string $author;
    private string $isbn;
    private bool $borrowed;
    private ?string $borrowed_at;
    private int $days;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    public function isBorrowed(): bool
    {
        return $this->borrowed;
    }

    public function getReturnTime(): string
    {
        $dateTime = DateTime::createFromFormat(self::DATETIME_FORMAT, $this->borrowed_at);
        $dateTime->add(new DateInterval('P14D'));

        return $dateTime->format(self::DATETIME_FORMAT);
    }

    public function getDays(): int
    {
        return $this->days;
    }
}
