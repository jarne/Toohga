<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 02.10.17
 * Time: 19:20
 */

namespace jarne\toohga\service;

class DecimalConverter {
    /**
     * Convert a string to a number
     *
     * @param string $string
     * @return int|null
     */
    public static function stringToNumber(string $string): ?int {
        $number = 0;

        if(!(strlen($string) > 0)) {
            return null;
        }

        for($i = 0; $i < strlen($string); $i++) {
            if(($numberCharacter = self::characterToNumber(strtoupper($string[$i]))) === null) {
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
    public static function numberToString(int $number): ?string {
        $string = "";

        if($number === 0) {
            return self::numberToCharacter($number);
        }

        while($number !== 0) {
            if(($character = self::numberToCharacter($number % 33)) === null) {
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
    private static function characterToNumber(string $character): ?int {
        switch($character) {
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
            case "A":
                return 9;
            case "B":
                return 10;
            case "C":
                return 11;
            case "D":
                return 12;
            case "E":
                return 13;
            case "F":
                return 14;
            case "G":
                return 15;
            case "H":
                return 16;
            case "J":
                return 17;
            case "K":
                return 18;
            case "L":
                return 19;
            case "M":
                return 20;
            case "N":
                return 21;
            case "P":
                return 22;
            case "Q":
                return 23;
            case "R":
                return 24;
            case "S":
                return 25;
            case "T":
                return 26;
            case "U":
                return 27;
            case "V":
                return 28;
            case "W":
                return 29;
            case "X":
                return 30;
            case "Y":
                return 31;
            case "Z":
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
    private static function numberToCharacter(int $number): ?string {
        switch($number) {
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
                return "A";
            case 10:
                return "B";
            case 11:
                return "C";
            case 12:
                return "D";
            case 13:
                return "E";
            case 14:
                return "F";
            case 15:
                return "G";
            case 16:
                return "H";
            case 17:
                return "J";
            case 18:
                return "K";
            case 19:
                return "L";
            case 20:
                return "M";
            case 21:
                return "N";
            case 22:
                return "P";
            case 23:
                return "Q";
            case 24:
                return "R";
            case 25:
                return "S";
            case 26:
                return "T";
            case 27:
                return "U";
            case 28:
                return "V";
            case 29:
                return "W";
            case 30:
                return "X";
            case 31:
                return "Y";
            case 32:
                return "Z";
        }

        return null;
    }
}