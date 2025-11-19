<?php

namespace Sanovskiy\Utility;
enum NamingConvention: string
{
    case UPPER_CAMEL = 'UpperCamelCase';
    case LOWER_CAMEL = 'lowerCamelCase';
    case SNAKE = 'snake_case';
    case SCREAMING_SNAKE = 'SCREAMING_SNAKE_CASE';
    case KEBAB = 'kebab-case';
    case SCREAMING_KEBAB = 'SCREAMING-KEBAB-CASE';
    case DOT = 'dot.case';
    case TRAIN = 'Train-Case';
}