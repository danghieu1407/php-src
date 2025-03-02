--TEST--
Test DateInterval::__serialize and DateInterval::__unserialize
--FILE--
<?php
date_default_timezone_set("Europe/London");

// the 15:30 gets ignored, as it's not a "relative" interval.
// See: https://github.com/php/php-src/issues/8458
$d = DateInterval::createFromDateString('next weekday 15:30');
echo "Original object:\n";
var_dump($d);

echo "\n\nSerialised object:\n";
$s = serialize($d);
var_dump($s);

echo "\n\nUnserialised object:\n";
$e = unserialize($s);
var_dump($e);

echo "\n\nCalling __serialize manually:\n";
var_dump($d->__serialize());

echo "\n\nCalling __unserialize manually:\n";
$d = new DateInterval('P2Y4DT6H8M');
$d->__unserialize(
	[
		'from_string' => true,
		'date_string' => 'next weekday 15:30',
	]
);
var_dump($d);

echo "\n\nUsed serialised interval:\n";
$now = new DateTimeImmutable("2022-04-22 16:25:11 BST");
var_dump($now->add($e));
var_dump($now->sub($e));
?>
--EXPECTF--
Original object:
object(DateInterval)#1 (%d) {
  ["from_string"]=>
  bool(true)
  ["date_string"]=>
  string(18) "next weekday 15:30"
}


Serialised object:
string(92) "O:12:"DateInterval":2:{s:11:"from_string";b:1;s:11:"date_string";s:18:"next weekday 15:30";}"


Unserialised object:
object(DateInterval)#2 (2) {
  ["from_string"]=>
  bool(true)
  ["date_string"]=>
  string(18) "next weekday 15:30"
}


Calling __serialize manually:
array(2) {
  ["from_string"]=>
  bool(true)
  ["date_string"]=>
  string(18) "next weekday 15:30"
}


Calling __unserialize manually:
object(DateInterval)#3 (2) {
  ["from_string"]=>
  bool(true)
  ["date_string"]=>
  string(18) "next weekday 15:30"
}


Used serialised interval:
object(DateTimeImmutable)#4 (3) {
  ["date"]=>
  string(26) "2022-04-25 16:25:11.000000"
  ["timezone_type"]=>
  int(2)
  ["timezone"]=>
  string(3) "BST"
}

Warning: DateTimeImmutable::sub(): Only non-special relative time specifications are supported for subtraction in %s on line %d
object(DateTimeImmutable)#4 (3) {
  ["date"]=>
  string(26) "2022-04-22 16:25:11.000000"
  ["timezone_type"]=>
  int(2)
  ["timezone"]=>
  string(3) "BST"
}
