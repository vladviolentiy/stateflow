<?php

namespace Flow\Core\Services;

/**
 * @pure
 */
final readonly class TextStatService
{
    public int $length;
    /** @var array<int, int>  */
    public array $freqTable;

    public function __construct(
        private string $inputData,
    ) {
        $this->length = strlen($this->inputData);
        $this->freqTable = count_chars($this->inputData, 1);
    }

    /**
     * @return float
     */
    public function getAsciiProbability(): float
    {
        $ascii_count = 0;
        for ($i = 0; $i < $this->length; $i++) {
            $char = ord($this->inputData[$i]);
            if ($char >= 32 && $char <= 126) { // ASCII-символы
                $ascii_count++;
            }
        }

        return $ascii_count / $this->length;
    }

    /**
     * @return float
     */
    public function calculateEntropy(): float
    {
        $entropy = 0.0;
        foreach ($this->freqTable as $count) {
            $probability = $count / $this->length;
            $entropy -= $probability * log($probability, 2); // Формула энтропии
        }

        return $entropy;
    }
}
