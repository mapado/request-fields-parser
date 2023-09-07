# Changelog

## 3.0.2

- Fixes merge issue with mix of true & Fields for the same key

## 3.0.1

- Fixes typing issue with doctrine/lexer 2 [#8](https://github.com/mapado/request-fields-parser/pull/8) by [@jdeniau](https://github.com/jdeniau)

## 3.0.0

### Changed

- [BREAKING] return type of `Parser::parse()` is an instance of [`Mapado\RequestFieldsParser\Fields`](https://github.com/mapado/request-fields-parser/blob/964eccafa7df2f9b4d833fae306f9afa2d2ea663/src/Fields.php) instead of an array. It does implements `ArrayAccess` and `IteratorAggregate` so you still can access data as array, but you cannot use array methods on it.
  You can convert the result to an array by calling the `toArray()` function.
  You can convert an array to a Fields using `Fields::fromArray()`.
  You can also call the `keys()` method to get all keys.
  You can cast it to a string to get the same string as a parsable string.
- [BREAKING] Drop support for PHP < 8.1 [#2](https://github.com/mapado/request-fields-parser/pull/2)
- [BREAKING] Drop support for doctrine/lexer < 2.0 and allow doctrine/lexer 3 [#3](https://github.com/mapado/request-fields-parser/pull/3) and [#4](https://github.com/mapado/request-fields-parser/pull/4)

## 2.0.0

### Changed

- [Breaking] Added compatibility for PHP 8.1
