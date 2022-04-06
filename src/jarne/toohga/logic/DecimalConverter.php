<?php

/**
 * Toohga | decimal string converter
 */

namespace jarne\toohga\logic;

class DecimalConverter
{
    /**
     * Convert a string to a number
     *
     * @param string $string
     * @return int|null
     */
    public static function stringToNumber(string $string): ?int
    {
        $number = 0;

        if (!(strlen($string) > 0)) {
            return null;
        }

        for ($i = 0; $i < strlen($string); $i++) {
            if (($numberCharacter = self::characterToNumber(strtolower($string[$i]))) === null) {
                return null;
            }

            $number += $numberCharacter * (33 ** (strlen($string) - ($i + 1)));
        }

        return $number;
    }

    /**
     * Convert a number to a string
     *
     * @param int $number
     * @return string|null
     */
    public static function numberToString(int $number): ?string
    {
        $string = "";

        if ($number === 0) {
            return self::numberToCharacter($number);
        }

        while ($number !== 0) {
            if (($character = self::numberToCharacter($number % 33)) === null) {
                return null;
            }

            $string = $character . $string;

            $number = intval($number / 33);
        }

        return $string;
    }

    /**
     * Convert a character to a number
     *
     * @param string $character
     * @return int|null
     */
    private static function characterToNumber(string $character): ?int
    {
        switch ($character) {
            case "1":
                return 0;
            case "2":
                return 1;
            case "3":
                return 2;
            case "4":
                return 3;
            case "5":
                return 4;
            case "6":
                return 5;
            case "7":
                return 6;
            case "8":
                return 7;
            case "9":
                return 8;
            case "a":
                return 9;
            case "b":
                return 10;
            case "c":
                return 11;
            case "d":
                return 12;
            case "e":
                return 13;
            case "f":
                return 14;
            case "g":
                return 15;
            case "h":
                return 16;
            case "j":
                return 17;
            case "k":
                return 18;
            case "l":
                return 19;
            case "m":
                return 20;
            case "n":
                return 21;
            case "p":
                return 22;
            case "q":
                return 23;
            case "r":
                return 24;
            case "s":
                return 25;
            case "t":
                return 26;
            case "u":
                return 27;
            case "v":
                return 28;
            case "w":
                return 29;
            case "x":
                return 30;
            case "y":
                return 31;
            case "z":
                return 32;
        }

        return null;
    }

    /**
     * Convert a number to a character
     *
     * @param int $number
     * @return string|null
     */
    private static function numberToCharacter(int $number): ?string
    {
        switch ($number) {
            case 0:
                return "1";
            case 1:
                return "2";
            case 2:
                return "3";
            case 3:
                return "4";
            case 4:
                return "5";
            case 5:
                return "6";
            case 6:
                return "7";
            case 7:
                return "8";
            case 8:
                return "9";
            case 9:
                return "a";
            case 10:
                return "b";
            case 11:
                return "c";
            case 12:
                return "d";
            case 13:
                return "e";
            case 14:
                return "f";
            case 15:
                return "g";
            case 16:
                return "h";
            case 17:
                return "j";
            case 18:
                return "k";
            case 19:
                return "l";
            case 20:
                return "m";
            case 21:
                return "n";
            case 22:
                return "p";
            case 23:
                return "q";
            case 24:
                return "r";
            case 25:
                return "s";
            case 26:
                return "t";
            case 27:
                return "u";
            case 28:
                return "v";
            case 29:
                return "w";
            case 30:
                return "x";
            case 31:
                return "y";
            case 32:
                return "z";
        }

        return null;
    }
}
