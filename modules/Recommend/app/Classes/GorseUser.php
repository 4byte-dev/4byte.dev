<?php

namespace Modules\Recommend\Classes;

use JsonSerializable;

class GorseUser implements JsonSerializable
{
    /**
     * @param array<int, string> $labels
     */
    public function __construct(
        private readonly string $userId,
        private array $labels,
        private readonly ?string $comment
    ) {
    }

    /**
     * Get the user ID.
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * Get labels of the user.
     *
     * @return array<int, string> ["label1", "label2"]
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * Get comment of the user.
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Add a label to the user.
     */
    public function addLabel(string $label): void
    {
        $this->labels[] = $label;
    }

    /**
     * Remove a label from the user.
     */
    public function removeLabel(string $label): void
    {
        $this->labels = array_filter($this->labels, fn ($currentLabel) => $currentLabel !== $label);
    }

    /**
     * Serialize the user to an array.
     *
     * @return array{
     *     UserId: string,
     *     Labels: array<int, string>,
     *     Comment: string
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'UserId'    => $this->userId,
            'Labels'    => $this->labels,
            'Comment'   => $this->comment,
        ];
    }

    /**
     * Create a GorseUser from a JSON array.
     *
     * @param array{
     *     UserId: string,
     *     Labels: array<int, string>,
     *     Comment: string
     * } $json
     */
    public static function fromJSON(array $json): self
    {
        return new self(
            $json['UserId'],
            $json['Labels'],
            $json['Comment']
        );
    }
}
