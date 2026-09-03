<?php

namespace App\Helpers;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use InvalidArgumentException;
use NumberFormatter;

class ConversionHelper
{
    /*
     * Convert an Enum Class to its array version
     */
    public function enumToArray($enumClass, string $field = 'value'): array
    {
        if (! in_array($field, ['name', 'value'])) {
            throw new InvalidArgumentException('The `field` arg must either be `value` or `name`');
        }

        return array_column($enumClass::cases(), $field);
    }

    /**
     * Convert a number to its ordinal value.
     * E.g. 2 => 2nd
     */
    public function numberToOrdinal(int $number): string
    {
        $formatter = new NumberFormatter('en-US', NumberFormatter::ORDINAL);

        return $formatter->format($number);
    }

    /**
     * Convert a string to a base64 QR code representation
     */
    public static function stringToBase64QrCode(
        string $data,
        int $size = 300,
        int $margin = 10,
        ?string $logoPath = null,
        string $label = ''
    ): string {
        $builder = new Builder(
            writer: new PngWriter,
            writerOptions: [],
            validateResult: false,
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: $size,
            margin: $margin,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            logoPath: $logoPath,
            logoResizeToWidth: 50,
            logoPunchoutBackground: true,
            labelText: $label,
            labelFont: new OpenSans(16),
            labelAlignment: LabelAlignment::Center
        );

        return $builder->build()->getDataUri();
    }
}
