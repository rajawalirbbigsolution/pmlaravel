<?php
 namespace Carbon; use Carbon\Exceptions\InvalidDateException; use Closure; use DatePeriod; use DateTime; use DateTimeInterface; use DateTimeZone; use InvalidArgumentException; use Symfony\Component\Translation\Loader\ArrayLoader; use Symfony\Component\Translation\Translator; use Symfony\Component\Translation\TranslatorInterface; class Carbon extends DateTime { const SUNDAY = 0; const MONDAY = 1; const TUESDAY = 2; const WEDNESDAY = 3; const THURSDAY = 4; const FRIDAY = 5; const SATURDAY = 6; protected static $days = array( self::SUNDAY => 'Sunday', self::MONDAY => 'Monday', self::TUESDAY => 'Tuesday', self::WEDNESDAY => 'Wednesday', self::THURSDAY => 'Thursday', self::FRIDAY => 'Friday', self::SATURDAY => 'Saturday', ); const YEARS_PER_CENTURY = 100; const YEARS_PER_DECADE = 10; const MONTHS_PER_YEAR = 12; const MONTHS_PER_QUARTER = 3; const WEEKS_PER_YEAR = 52; const DAYS_PER_WEEK = 7; const HOURS_PER_DAY = 24; const MINUTES_PER_HOUR = 60; const SECONDS_PER_MINUTE = 60; const RFC7231_FORMAT = 'D, d M Y H:i:s \G\M\T'; const DEFAULT_TO_STRING_FORMAT = 'Y-m-d H:i:s'; const MOCK_DATETIME_FORMAT = 'Y-m-d H:i:s.u'; public static $PHPIntSize = PHP_INT_SIZE; protected static $toStringFormat = self::DEFAULT_TO_STRING_FORMAT; protected static $weekStartsAt = self::MONDAY; protected static $weekEndsAt = self::SUNDAY; protected static $weekendDays = array( self::SATURDAY, self::SUNDAY, ); protected static $midDayAt = 12; protected static $regexFormats = array( 'd' => '(3[01]|[12][0-9]|0[1-9])', 'D' => '([a-zA-Z]{3})', 'j' => '([123][0-9]|[1-9])', 'l' => '([a-zA-Z]{2,})', 'N' => '([1-7])', 'S' => '([a-zA-Z]{2})', 'w' => '([0-6])', 'z' => '(36[0-5]|3[0-5][0-9]|[12][0-9]{2}|[1-9]?[0-9])', 'W' => '(5[012]|[1-4][0-9]|[1-9])', 'F' => '([a-zA-Z]{2,})', 'm' => '(1[012]|0[1-9])', 'M' => '([a-zA-Z]{3})', 'n' => '(1[012]|[1-9])', 't' => '(2[89]|3[01])', 'L' => '(0|1)', 'o' => '([1-9][0-9]{0,4})', 'Y' => '([1-9][0-9]{0,4})', 'y' => '([0-9]{2})', 'a' => '(am|pm)', 'A' => '(AM|PM)', 'B' => '([0-9]{3})', 'g' => '(1[012]|[1-9])', 'G' => '(2[0-3]|1?[0-9])', 'h' => '(1[012]|0[1-9])', 'H' => '(2[0-3]|[01][0-9])', 'i' => '([0-5][0-9])', 's' => '([0-5][0-9])', 'u' => '([0-9]{1,6})', 'v' => '([0-9]{1,3})', 'e' => '([a-zA-Z]{1,5})|([a-zA-Z]*\/[a-zA-Z]*)', 'I' => '(0|1)', 'O' => '([\+\-](1[012]|0[0-9])[0134][05])', 'P' => '([\+\-](1[012]|0[0-9]):[0134][05])', 'T' => '([a-zA-Z]{1,5})', 'Z' => '(-?[1-5]?[0-9]{1,4})', 'U' => '([0-9]*)', 'c' => '(([1-9][0-9]{0,4})\-(1[012]|0[1-9])\-(3[01]|[12][0-9]|0[1-9])T(2[0-3]|[01][0-9]):([0-5][0-9]):([0-5][0-9])[\+\-](1[012]|0[0-9]):([0134][05]))', 'r' => '(([a-zA-Z]{3}), ([123][0-9]|[1-9]) ([a-zA-Z]{3}) ([1-9][0-9]{0,4}) (2[0-3]|[01][0-9]):([0-5][0-9]):([0-5][0-9]) [\+\-](1[012]|0[0-9])([0134][05]))', ); protected static $testNow; protected static $translator; protected static $lastErrors; protected static $utf8 = false; protected static $monthsOverflow = true; protected static $yearsOverflow = true; public static function useMonthsOverflow($monthsOverflow = true) { static::$monthsOverflow = $monthsOverflow; } public static function resetMonthsOverflow() { static::$monthsOverflow = true; } public static function shouldOverflowMonths() { return static::$monthsOverflow; } public static function useYearsOverflow($yearsOverflow = true) { static::$yearsOverflow = $yearsOverflow; } public static function resetYearsOverflow() { static::$yearsOverflow = true; } public static function shouldOverflowYears() { return static::$yearsOverflow; } protected static function safeCreateDateTimeZone($object) { if ($object === null) { return new DateTimeZone(date_default_timezone_get()); } if ($object instanceof DateTimeZone) { return $object; } if (is_numeric($object)) { $tzName = timezone_name_from_abbr(null, $object * 3600, true); if ($tzName === false) { throw new InvalidArgumentException('Unknown or bad timezone ('.$object.')'); } $object = $tzName; } $tz = @timezone_open((string) $object); if ($tz === false) { throw new InvalidArgumentException('Unknown or bad timezone ('.$object.')'); } return $tz; } public function __construct($time = null, $tz = null) { $isNow = empty($time) || $time === 'now'; if (static::hasTestNow() && ($isNow || static::hasRelativeKeywords($time))) { $testInstance = clone static::getTestNow(); if (static::hasRelativeKeywords($time)) { $testInstance->modify($time); } if ($tz !== null && $tz !== static::getTestNow()->getTimezone()) { $testInstance->setTimezone($tz); } else { $tz = $testInstance->getTimezone(); } $time = $testInstance->format(static::MOCK_DATETIME_FORMAT); } $timezone = static::safeCreateDateTimeZone($tz); if ($isNow && !isset($testInstance) && ( version_compare(PHP_VERSION, '7.1.0-dev', '<') || version_compare(PHP_VERSION, '7.1.3-dev', '>=') && version_compare(PHP_VERSION, '7.1.4-dev', '<') ) ) { $dateTime = new DateTime('now', $timezone); $microTime = microtime(true) * 1000000 % 1000000; if ($microTime > 0) { $microTime = str_pad(strval($microTime), 6, '0', STR_PAD_LEFT); $time = $dateTime->format(static::DEFAULT_TO_STRING_FORMAT).'.'.$microTime; } } if (strpos((string) .1, '.') === false) { $locale = setlocale(LC_NUMERIC, '0'); setlocale(LC_NUMERIC, 'C'); } parent::__construct($time, $timezone); if (isset($locale)) { setlocale(LC_NUMERIC, $locale); } static::setLastErrors(parent::getLastErrors()); } public static function instance(DateTime $date) { if ($date instanceof static) { return clone $date; } return new static($date->format('Y-m-d H:i:s.u'), $date->getTimezone()); } public static function parse($time = null, $tz = null) { return new static($time, $tz); } public static function now($tz = null) { return new static(null, $tz); } public static function today($tz = null) { return static::now($tz)->startOfDay(); } public static function tomorrow($tz = null) { return static::today($tz)->addDay(); } public static function yesterday($tz = null) { return static::today($tz)->subDay(); } public static function maxValue() { if (self::$PHPIntSize === 4) { return static::createFromTimestamp(PHP_INT_MAX); } return static::create(9999, 12, 31, 23, 59, 59); } public static function minValue() { if (self::$PHPIntSize === 4) { return static::createFromTimestamp(~PHP_INT_MAX); } return static::create(1, 1, 1, 0, 0, 0); } public static function create($year = null, $month = null, $day = null, $hour = null, $minute = null, $second = null, $tz = null) { $now = static::hasTestNow() ? static::getTestNow() : static::now($tz); $defaults = array_combine(array( 'year', 'month', 'day', 'hour', 'minute', 'second', ), explode('-', $now->format('Y-n-j-G-i-s'))); $year = $year === null ? $defaults['year'] : $year; $month = $month === null ? $defaults['month'] : $month; $day = $day === null ? $defaults['day'] : $day; if ($hour === null) { $hour = $defaults['hour']; $minute = $minute === null ? $defaults['minute'] : $minute; $second = $second === null ? $defaults['second'] : $second; } else { $minute = $minute === null ? 0 : $minute; $second = $second === null ? 0 : $second; } $fixYear = null; if ($year < 0) { $fixYear = $year; $year = 0; } elseif ($year > 9999) { $fixYear = $year - 9999; $year = 9999; } $instance = static::createFromFormat('Y-n-j G:i:s', sprintf('%s-%s-%s %s:%02s:%02s', $year, $month, $day, $hour, $minute, $second), $tz); if ($fixYear !== null) { $instance->addYears($fixYear); } return $instance; } public static function createSafe($year = null, $month = null, $day = null, $hour = null, $minute = null, $second = null, $tz = null) { $fields = array( 'year' => array(0, 9999), 'month' => array(0, 12), 'day' => array(0, 31), 'hour' => array(0, 24), 'minute' => array(0, 59), 'second' => array(0, 59), ); foreach ($fields as $field => $range) { if ($$field !== null && (!is_int($$field) || $$field < $range[0] || $$field > $range[1])) { throw new InvalidDateException($field, $$field); } } $instance = static::create($year, $month, $day, $hour, $minute, $second, $tz); foreach (array_reverse($fields) as $field => $range) { if ($$field !== null && (!is_int($$field) || $$field !== $instance->$field)) { throw new InvalidDateException($field, $$field); } } return $instance; } public static function createFromDate($year = null, $month = null, $day = null, $tz = null) { return static::create($year, $month, $day, null, null, null, $tz); } public static function createMidnightDate($year = null, $month = null, $day = null, $tz = null) { return static::create($year, $month, $day, 0, 0, 0, $tz); } public static function createFromTime($hour = null, $minute = null, $second = null, $tz = null) { return static::create(null, null, null, $hour, $minute, $second, $tz); } public static function createFromFormat($format, $time, $tz = null) { if ($tz !== null) { $date = parent::createFromFormat($format, $time, static::safeCreateDateTimeZone($tz)); } else { $date = parent::createFromFormat($format, $time); } $lastErrors = parent::getLastErrors(); if ($date instanceof DateTime) { $instance = static::instance($date); $instance::setLastErrors($lastErrors); return $instance; } throw new InvalidArgumentException(implode(PHP_EOL, $lastErrors['errors'])); } private static function setLastErrors(array $lastErrors) { static::$lastErrors = $lastErrors; } public static function getLastErrors() { return static::$lastErrors; } public static function createFromTimestamp($timestamp, $tz = null) { return static::now($tz)->setTimestamp($timestamp); } public static function createFromTimestampMs($timestamp, $tz = null) { return static::createFromFormat('U.u', sprintf('%F', $timestamp / 1000)) ->setTimezone($tz); } public static function createFromTimestampUTC($timestamp) { return new static('@'.$timestamp); } public function copy() { return clone $this; } public function nowWithSameTz() { return static::now($this->getTimezone()); } protected function resolveCarbon(self $date = null) { return $date ?: $this->nowWithSameTz(); } public function __get($name) { static $formats = array( 'year' => 'Y', 'yearIso' => 'o', 'month' => 'n', 'day' => 'j', 'hour' => 'G', 'minute' => 'i', 'second' => 's', 'micro' => 'u', 'dayOfWeek' => 'w', 'dayOfYear' => 'z', 'weekOfYear' => 'W', 'daysInMonth' => 't', 'timestamp' => 'U', ); switch (true) { case isset($formats[$name]): return (int) $this->format($formats[$name]); case $name === 'weekOfMonth': return (int) ceil($this->day / static::DAYS_PER_WEEK); case $name === 'weekNumberInMonth': return (int) ceil(($this->day + $this->copy()->startOfMonth()->dayOfWeek - 1) / static::DAYS_PER_WEEK); case $name === 'age': return $this->diffInYears(); case $name === 'quarter': return (int) ceil($this->month / static::MONTHS_PER_QUARTER); case $name === 'offset': return $this->getOffset(); case $name === 'offsetHours': return $this->getOffset() / static::SECONDS_PER_MINUTE / static::MINUTES_PER_HOUR; case $name === 'dst': return $this->format('I') === '1'; case $name === 'local': return $this->getOffset() === $this->copy()->setTimezone(date_default_timezone_get())->getOffset(); case $name === 'utc': return $this->getOffset() === 0; case $name === 'timezone' || $name === 'tz': return $this->getTimezone(); case $name === 'timezoneName' || $name === 'tzName': return $this->getTimezone()->getName(); default: throw new InvalidArgumentException(sprintf("Unknown getter '%s'", $name)); } } public function __isset($name) { try { $this->__get($name); } catch (InvalidArgumentException $e) { return false; } return true; } public function __set($name, $value) { switch ($name) { case 'year': case 'month': case 'day': case 'hour': case 'minute': case 'second': list($year, $month, $day, $hour, $minute, $second) = explode('-', $this->format('Y-n-j-G-i-s')); $$name = $value; $this->setDateTime($year, $month, $day, $hour, $minute, $second); break; case 'timestamp': parent::setTimestamp($value); break; case 'timezone': case 'tz': $this->setTimezone($value); break; default: throw new InvalidArgumentException(sprintf("Unknown setter '%s'", $name)); } } public function year($value) { $this->year = $value; return $this; } public function month($value) { $this->month = $value; return $this; } public function day($value) { $this->day = $value; return $this; } public function hour($value) { $this->hour = $value; return $this; } public function minute($value) { $this->minute = $value; return $this; } public function second($value) { $this->second = $value; return $this; } public function setDate($year, $month, $day) { $this->modify('+0 day'); return parent::setDate($year, $month, $day); } public function setDateTime($year, $month, $day, $hour, $minute, $second = 0) { return $this->setDate($year, $month, $day)->setTime($hour, $minute, $second); } public function setTimeFromTimeString($time) { $time = explode(':', $time); $hour = $time[0]; $minute = isset($time[1]) ? $time[1] : 0; $second = isset($time[2]) ? $time[2] : 0; return $this->setTime($hour, $minute, $second); } public function timestamp($value) { return $this->setTimestamp($value); } public function timezone($value) { return $this->setTimezone($value); } public function tz($value) { return $this->setTimezone($value); } public function setTimezone($value) { parent::setTimezone(static::safeCreateDateTimeZone($value)); $this->getTimestamp(); return $this; } public static function getDays() { return static::$days; } public static function getWeekStartsAt() { return static::$weekStartsAt; } public static function setWeekStartsAt($day) { static::$weekStartsAt = $day; } public static function getWeekEndsAt() { return static::$weekEndsAt; } public static function setWeekEndsAt($day) { static::$weekEndsAt = $day; } public static function getWeekendDays() { return static::$weekendDays; } public static function setWeekendDays($days) { static::$weekendDays = $days; } public static function getMidDayAt() { return static::$midDayAt; } public static function setMidDayAt($hour) { static::$midDayAt = $hour; } public static function setTestNow($testNow = null) { static::$testNow = is_string($testNow) ? static::parse($testNow) : $testNow; } public static function getTestNow() { return static::$testNow; } public static function hasTestNow() { return static::getTestNow() !== null; } public static function hasRelativeKeywords($time) { if (strtotime($time) === false) { return false; } $date1 = new DateTime('2000-01-01T00:00:00Z'); $date1->modify($time); $date2 = new DateTime('2001-12-25T00:00:00Z'); $date2->modify($time); return $date1 != $date2; } protected static function translator() { if (static::$translator === null) { $translator = new Translator('en'); $translator->addLoader('array', new ArrayLoader()); static::$translator = $translator; static::setLocale('en'); } return static::$translator; } public static function getTranslator() { return static::translator(); } public static function setTranslator(TranslatorInterface $translator) { static::$translator = $translator; } public static function getLocale() { return static::translator()->getLocale(); } public static function setLocale($locale) { $locale = preg_replace_callback('/[-_]([a-z]{2,})/', function ($matches) { return '_'.call_user_func(strlen($matches[1]) > 2 ? 'ucfirst' : 'strtoupper', $matches[1]); }, strtolower($locale)); if (file_exists($filename = __DIR__.'/Lang/'.$locale.'.php')) { $translator = static::translator(); $translator->setLocale($locale); if ($translator instanceof Translator) { $translator->addResource('array', require $filename, $locale); } return true; } return false; } public static function setUtf8($utf8) { static::$utf8 = $utf8; } public function formatLocalized($format) { if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { $format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format); } $formatted = strftime($format, strtotime($this->toDateTimeString())); return static::$utf8 ? utf8_encode($formatted) : $formatted; } public static function resetToStringFormat() { static::setToStringFormat(static::DEFAULT_TO_STRING_FORMAT); } public static function setToStringFormat($format) { static::$toStringFormat = $format; } public function __toString() { return $this->format(static::$toStringFormat); } public function toDateString() { return $this->format('Y-m-d'); } public function toFormattedDateString() { return $this->format('M j, Y'); } public function toTimeString() { return $this->format('H:i:s'); } public function toDateTimeString() { return $this->format('Y-m-d H:i:s'); } public function toDayDateTimeString() { return $this->format('D, M j, Y g:i A'); } public function toAtomString() { return $this->format(static::ATOM); } public function toCookieString() { return $this->format(static::COOKIE); } public function toIso8601String() { return $this->toAtomString(); } public function toRfc822String() { return $this->format(static::RFC822); } public function toIso8601ZuluString() { return $this->copy()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z'); } public function toRfc850String() { return $this->format(static::RFC850); } public function toRfc1036String() { return $this->format(static::RFC1036); } public function toRfc1123String() { return $this->format(static::RFC1123); } public function toRfc2822String() { return $this->format(static::RFC2822); } public function toRfc3339String() { return $this->format(static::RFC3339); } public function toRssString() { return $this->format(static::RSS); } public function toW3cString() { return $this->format(static::W3C); } public function toRfc7231String() { return $this->copy() ->setTimezone('GMT') ->format(static::RFC7231_FORMAT); } public function toArray() { return array( 'year' => $this->year, 'month' => $this->month, 'day' => $this->day, 'dayOfWeek' => $this->dayOfWeek, 'dayOfYear' => $this->dayOfYear, 'hour' => $this->hour, 'minute' => $this->minute, 'second' => $this->second, 'micro' => $this->micro, 'timestamp' => $this->timestamp, 'formatted' => $this->format(self::DEFAULT_TO_STRING_FORMAT), 'timezone' => $this->timezone, ); } public function eq($date) { return $this == $date; } public function equalTo($date) { return $this->eq($date); } public function ne($date) { return !$this->eq($date); } public function notEqualTo($date) { return $this->ne($date); } public function gt($date) { return $this > $date; } public function greaterThan($date) { return $this->gt($date); } public function gte($date) { return $this >= $date; } public function greaterThanOrEqualTo($date) { return $this->gte($date); } public function lt($date) { return $this < $date; } public function lessThan($date) { return $this->lt($date); } public function lte($date) { return $this <= $date; } public function lessThanOrEqualTo($date) { return $this->lte($date); } public function between($date1, $date2, $equal = true) { if ($date1->gt($date2)) { $temp = $date1; $date1 = $date2; $date2 = $temp; } if ($equal) { return $this->gte($date1) && $this->lte($date2); } return $this->gt($date1) && $this->lt($date2); } public function closest($date1, $date2) { return $this->diffInSeconds($date1) < $this->diffInSeconds($date2) ? $date1 : $date2; } public function farthest($date1, $date2) { return $this->diffInSeconds($date1) > $this->diffInSeconds($date2) ? $date1 : $date2; } public function min($date = null) { $date = $this->resolveCarbon($date); return $this->lt($date) ? $this : $date; } public function minimum($date = null) { return $this->min($date); } public function max($date = null) { $date = $this->resolveCarbon($date); return $this->gt($date) ? $this : $date; } public function maximum($date = null) { return $this->max($date); } public function isWeekday() { return !$this->isWeekend(); } public function isWeekend() { return in_array($this->dayOfWeek, static::$weekendDays); } public function isYesterday() { return $this->toDateString() === static::yesterday($this->getTimezone())->toDateString(); } public function isToday() { return $this->toDateString() === $this->nowWithSameTz()->toDateString(); } public function isTomorrow() { return $this->toDateString() === static::tomorrow($this->getTimezone())->toDateString(); } public function isNextWeek() { return $this->weekOfYear === $this->nowWithSameTz()->addWeek()->weekOfYear; } public function isLastWeek() { return $this->weekOfYear === $this->nowWithSameTz()->subWeek()->weekOfYear; } public function isNextMonth() { return $this->month === $this->nowWithSameTz()->addMonthNoOverflow()->month; } public function isLastMonth() { return $this->month === $this->nowWithSameTz()->subMonthNoOverflow()->month; } public function isNextYear() { return $this->year === $this->nowWithSameTz()->addYear()->year; } public function isLastYear() { return $this->year === $this->nowWithSameTz()->subYear()->year; } public function isFuture() { return $this->gt($this->nowWithSameTz()); } public function isPast() { return $this->lt($this->nowWithSameTz()); } public function isLeapYear() { return $this->format('L') === '1'; } public function isLongYear() { return static::create($this->year, 12, 28, 0, 0, 0, $this->tz)->weekOfYear === 53; } public function isSameAs($format, $date = null) { $date = $date ?: static::now($this->tz); if (!($date instanceof DateTime) && !($date instanceof DateTimeInterface)) { throw new InvalidArgumentException('Expected DateTime (or instanceof) object as argument.'); } return $this->format($format) === $date->format($format); } public function isCurrentYear() { return $this->isSameYear(); } public function isSameYear($date = null) { return $this->isSameAs('Y', $date); } public function isCurrentMonth() { return $this->isSameMonth(); } public function isSameMonth($date = null, $ofSameYear = false) { return $this->isSameAs($ofSameYear ? 'Y-m' : 'm', $date); } public function isSameDay($date) { return $this->isSameAs('Y-m-d', $date); } public function isDayOfWeek($dayOfWeek) { return $this->dayOfWeek === $dayOfWeek; } public function isSunday() { return $this->dayOfWeek === static::SUNDAY; } public function isMonday() { return $this->dayOfWeek === static::MONDAY; } public function isTuesday() { return $this->dayOfWeek === static::TUESDAY; } public function isWednesday() { return $this->dayOfWeek === static::WEDNESDAY; } public function isThursday() { return $this->dayOfWeek === static::THURSDAY; } public function isFriday() { return $this->dayOfWeek === static::FRIDAY; } public function isSaturday() { return $this->dayOfWeek === static::SATURDAY; } public static function hasFormat($date, $format) { try { static::createFromFormat($format, $date); $regex = strtr( preg_quote($format, '/'), static::$regexFormats ); return (bool) preg_match('/^'.$regex.'$/', $date); } catch (InvalidArgumentException $e) { } return false; } public function addYears($value) { if ($this->shouldOverflowYears()) { return $this->addYearsWithOverflow($value); } return $this->addYearsNoOverflow($value); } public function addYear($value = 1) { return $this->addYears($value); } public function addYearsNoOverflow($value) { return $this->addMonthsNoOverflow($value * static::MONTHS_PER_YEAR); } public function addYearNoOverflow($value = 1) { return $this->addYearsNoOverflow($value); } public function addYearsWithOverflow($value) { return $this->modify((int) $value.' year'); } public function addYearWithOverflow($value = 1) { return $this->addYearsWithOverflow($value); } public function subYear($value = 1) { return $this->subYears($value); } public function subYears($value) { return $this->addYears(-1 * $value); } public function subYearNoOverflow($value = 1) { return $this->subYearsNoOverflow($value); } public function subYearsNoOverflow($value) { return $this->subMonthsNoOverflow($value * static::MONTHS_PER_YEAR); } public function subYearWithOverflow($value = 1) { return $this->subYearsWithOverflow($value); } public function subYearsWithOverflow($value) { return $this->subMonthsWithOverflow($value * static::MONTHS_PER_YEAR); } public function addQuarters($value) { return $this->addMonths(static::MONTHS_PER_QUARTER * $value); } public function addQuarter($value = 1) { return $this->addQuarters($value); } public function subQuarter($value = 1) { return $this->subQuarters($value); } public function subQuarters($value) { return $this->addQuarters(-1 * $value); } public function addCenturies($value) { return $this->addYears(static::YEARS_PER_CENTURY * $value); } public function addCentury($value = 1) { return $this->addCenturies($value); } public function subCentury($value = 1) { return $this->subCenturies($value); } public function subCenturies($value) { return $this->addCenturies(-1 * $value); } public function addMonths($value) { if (static::shouldOverflowMonths()) { return $this->addMonthsWithOverflow($value); } return $this->addMonthsNoOverflow($value); } public function addMonth($value = 1) { return $this->addMonths($value); } public function subMonth($value = 1) { return $this->subMonths($value); } public function subMonths($value) { return $this->addMonths(-1 * $value); } public function addMonthsWithOverflow($value) { return $this->modify((int) $value.' month'); } public function addMonthWithOverflow($value = 1) { return $this->addMonthsWithOverflow($value); } public function subMonthWithOverflow($value = 1) { return $this->subMonthsWithOverflow($value); } public function subMonthsWithOverflow($value) { return $this->addMonthsWithOverflow(-1 * $value); } public function addMonthsNoOverflow($value) { $day = $this->day; $this->modify((int) $value.' month'); if ($day !== $this->day) { $this->modify('last day of previous month'); } return $this; } public function addMonthNoOverflow($value = 1) { return $this->addMonthsNoOverflow($value); } public function subMonthNoOverflow($value = 1) { return $this->subMonthsNoOverflow($value); } public function subMonthsNoOverflow($value) { return $this->addMonthsNoOverflow(-1 * $value); } public function addDays($value) { return $this->modify((int) $value.' day'); } public function addDay($value = 1) { return $this->addDays($value); } public function subDay($value = 1) { return $this->subDays($value); } public function subDays($value) { return $this->addDays(-1 * $value); } public function addWeekdays($value) { $t = $this->toTimeString(); $this->modify((int) $value.' weekday'); return $this->setTimeFromTimeString($t); } public function addWeekday($value = 1) { return $this->addWeekdays($value); } public function subWeekday($value = 1) { return $this->subWeekdays($value); } public function subWeekdays($value) { return $this->addWeekdays(-1 * $value); } public function addWeeks($value) { return $this->modify((int) $value.' week'); } public function addWeek($value = 1) { return $this->addWeeks($value); } public function subWeek($value = 1) { return $this->subWeeks($value); } public function subWeeks($value) { return $this->addWeeks(-1 * $value); } public function addHours($value) { return $this->modify((int) $value.' hour'); } public function addHour($value = 1) { return $this->addHours($value); } public function subHour($value = 1) { return $this->subHours($value); } public function subHours($value) { return $this->addHours(-1 * $value); } public function addMinutes($value) { return $this->modify((int) $value.' minute'); } public function addMinute($value = 1) { return $this->addMinutes($value); } public function subMinute($value = 1) { return $this->subMinutes($value); } public function subMinutes($value) { return $this->addMinutes(-1 * $value); } public function addSeconds($value) { return $this->modify((int) $value.' second'); } public function addSecond($value = 1) { return $this->addSeconds($value); } public function subSeconds($value) { return $this->addSeconds(-1 * $value); } public function subSecond($value = 1) { return $this->subSeconds($value); } public function diffInYears($date = null, $absolute = true) { return (int) $this->diff($this->resolveCarbon($date), $absolute)->format('%r%y'); } public function diffInMonths($date = null, $absolute = true) { $date = $this->resolveCarbon($date); return $this->diffInYears($date, $absolute) * static::MONTHS_PER_YEAR + (int) $this->diff($date, $absolute)->format('%r%m'); } public function diffInWeeks($date = null, $absolute = true) { return (int) ($this->diffInDays($date, $absolute) / static::DAYS_PER_WEEK); } public function diffInDays($date = null, $absolute = true) { return (int) $this->diff($this->resolveCarbon($date), $absolute)->format('%r%a'); } public function diffInDaysFiltered(Closure $callback, $date = null, $absolute = true) { return $this->diffFiltered(CarbonInterval::day(), $callback, $date, $absolute); } public function diffInHoursFiltered(Closure $callback, $date = null, $absolute = true) { return $this->diffFiltered(CarbonInterval::hour(), $callback, $date, $absolute); } public function diffFiltered(CarbonInterval $ci, Closure $callback, $date = null, $absolute = true) { $start = $this; $end = $this->resolveCarbon($date); $inverse = false; if ($end < $start) { $start = $end; $end = $this; $inverse = true; } $period = new DatePeriod($start, $ci, $end); $values = array_filter(iterator_to_array($period), function (DateTime $date) use ($callback) { return call_user_func($callback, Carbon::instance($date)); }); $diff = count($values); return $inverse && !$absolute ? -$diff : $diff; } public function diffInWeekdays($date = null, $absolute = true) { return $this->diffInDaysFiltered(function (Carbon $date) { return $date->isWeekday(); }, $date, $absolute); } public function diffInWeekendDays($date = null, $absolute = true) { return $this->diffInDaysFiltered(function (Carbon $date) { return $date->isWeekend(); }, $date, $absolute); } public function diffInHours($date = null, $absolute = true) { return (int) ($this->diffInSeconds($date, $absolute) / static::SECONDS_PER_MINUTE / static::MINUTES_PER_HOUR); } public function diffInMinutes($date = null, $absolute = true) { return (int) ($this->diffInSeconds($date, $absolute) / static::SECONDS_PER_MINUTE); } public function diffInSeconds($date = null, $absolute = true) { $diff = $this->diff($this->resolveCarbon($date)); $value = $diff->days * static::HOURS_PER_DAY * static::MINUTES_PER_HOUR * static::SECONDS_PER_MINUTE + $diff->h * static::MINUTES_PER_HOUR * static::SECONDS_PER_MINUTE + $diff->i * static::SECONDS_PER_MINUTE + $diff->s; return $absolute || !$diff->invert ? $value : -$value; } public function secondsSinceMidnight() { return $this->diffInSeconds($this->copy()->startOfDay()); } public function secondsUntilEndOfDay() { return $this->diffInSeconds($this->copy()->endOfDay()); } public function diffForHumans(self $other = null, $absolute = false, $short = false, $parts = 1) { $isNow = $other === null; $interval = array(); $parts = min(6, max(1, (int) $parts)); $count = 1; $unit = $short ? 's' : 'second'; if ($isNow) { $other = $this->nowWithSameTz(); } $diffInterval = $this->diff($other); $diffIntervalArray = array( array('value' => $diffInterval->y, 'unit' => 'year', 'unitShort' => 'y'), array('value' => $diffInterval->m, 'unit' => 'month', 'unitShort' => 'm'), array('value' => $diffInterval->d, 'unit' => 'day', 'unitShort' => 'd'), array('value' => $diffInterval->h, 'unit' => 'hour', 'unitShort' => 'h'), array('value' => $diffInterval->i, 'unit' => 'minute', 'unitShort' => 'min'), array('value' => $diffInterval->s, 'unit' => 'second', 'unitShort' => 's'), ); foreach ($diffIntervalArray as $diffIntervalData) { if ($diffIntervalData['value'] > 0) { $unit = $short ? $diffIntervalData['unitShort'] : $diffIntervalData['unit']; $count = $diffIntervalData['value']; if ($diffIntervalData['unit'] === 'day' && $count >= static::DAYS_PER_WEEK) { $unit = $short ? 'w' : 'week'; $count = (int) ($count / static::DAYS_PER_WEEK); $interval[] = static::translator()->transChoice($unit, $count, array(':count' => $count)); $numOfDaysCount = (int) ($diffIntervalData['value'] - ($count * static::DAYS_PER_WEEK)); if ($numOfDaysCount > 0 && count($interval) < $parts) { $unit = $short ? 'd' : 'day'; $count = $numOfDaysCount; $interval[] = static::translator()->transChoice($unit, $count, array(':count' => $count)); } } else { $interval[] = static::translator()->transChoice($unit, $count, array(':count' => $count)); } } if (count($interval) >= $parts) { break; } } if (count($interval) === 0) { $count = 1; $unit = $short ? 's' : 'second'; $interval[] = static::translator()->transChoice($unit, $count, array(':count' => $count)); } $time = implode(' ', $interval); unset($diffIntervalArray, $interval); if ($absolute) { return $time; } $isFuture = $diffInterval->invert === 1; $transId = $isNow ? ($isFuture ? 'from_now' : 'ago') : ($isFuture ? 'after' : 'before'); if ($parts === 1) { $key = $unit.'_'.$transId; $count = isset($count) ? $count : 1; if ($key !== static::translator()->transChoice($key, $count)) { $time = static::translator()->transChoice($key, $count, array(':count' => $count)); } } return static::translator()->trans($transId, array(':time' => $time)); } public function startOfDay() { return $this->setTime(0, 0, 0); } public function endOfDay() { return $this->setTime(23, 59, 59); } public function startOfMonth() { return $this->setDateTime($this->year, $this->month, 1, 0, 0, 0); } public function endOfMonth() { return $this->setDateTime($this->year, $this->month, $this->daysInMonth, 23, 59, 59); } public function startOfQuarter() { $month = ($this->quarter - 1) * static::MONTHS_PER_QUARTER + 1; return $this->setDateTime($this->year, $month, 1, 0, 0, 0); } public function endOfQuarter() { return $this->startOfQuarter()->addMonths(static::MONTHS_PER_QUARTER - 1)->endOfMonth(); } public function startOfYear() { return $this->setDateTime($this->year, 1, 1, 0, 0, 0); } public function endOfYear() { return $this->setDateTime($this->year, 12, 31, 23, 59, 59); } public function startOfDecade() { $year = $this->year - $this->year % static::YEARS_PER_DECADE; return $this->setDateTime($year, 1, 1, 0, 0, 0); } public function endOfDecade() { $year = $this->year - $this->year % static::YEARS_PER_DECADE + static::YEARS_PER_DECADE - 1; return $this->setDateTime($year, 12, 31, 23, 59, 59); } public function startOfCentury() { $year = $this->year - ($this->year - 1) % static::YEARS_PER_CENTURY; return $this->setDateTime($year, 1, 1, 0, 0, 0); } public function endOfCentury() { $year = $this->year - 1 - ($this->year - 1) % static::YEARS_PER_CENTURY + static::YEARS_PER_CENTURY; return $this->setDateTime($year, 12, 31, 23, 59, 59); } public function startOfWeek() { while ($this->dayOfWeek !== static::$weekStartsAt) { $this->subDay(); } return $this->startOfDay(); } public function endOfWeek() { while ($this->dayOfWeek !== static::$weekEndsAt) { $this->addDay(); } return $this->endOfDay(); } public function startOfHour() { return $this->setTime($this->hour, 0, 0); } public function endOfHour() { return $this->setTime($this->hour, 59, 59); } public function startOfMinute() { return $this->setTime($this->hour, $this->minute, 0); } public function endOfMinute() { return $this->setTime($this->hour, $this->minute, 59); } public function midDay() { return $this->setTime(self::$midDayAt, 0, 0); } public function next($dayOfWeek = null) { if ($dayOfWeek === null) { $dayOfWeek = $this->dayOfWeek; } return $this->startOfDay()->modify('next '.static::$days[$dayOfWeek]); } private function nextOrPreviousDay($weekday = true, $forward = true) { $step = $forward ? 1 : -1; do { $this->addDay($step); } while ($weekday ? $this->isWeekend() : $this->isWeekday()); return $this; } public function nextWeekday() { return $this->nextOrPreviousDay(); } public function previousWeekday() { return $this->nextOrPreviousDay(true, false); } public function nextWeekendDay() { return $this->nextOrPreviousDay(false); } public function previousWeekendDay() { return $this->nextOrPreviousDay(false, false); } public function previous($dayOfWeek = null) { if ($dayOfWeek === null) { $dayOfWeek = $this->dayOfWeek; } return $this->startOfDay()->modify('last '.static::$days[$dayOfWeek]); } public function firstOfMonth($dayOfWeek = null) { $this->startOfDay(); if ($dayOfWeek === null) { return $this->day(1); } return $this->modify('first '.static::$days[$dayOfWeek].' of '.$this->format('F').' '.$this->year); } public function lastOfMonth($dayOfWeek = null) { $this->startOfDay(); if ($dayOfWeek === null) { return $this->day($this->daysInMonth); } return $this->modify('last '.static::$days[$dayOfWeek].' of '.$this->format('F').' '.$this->year); } public function nthOfMonth($nth, $dayOfWeek) { $date = $this->copy()->firstOfMonth(); $check = $date->format('Y-m'); $date->modify('+'.$nth.' '.static::$days[$dayOfWeek]); return $date->format('Y-m') === $check ? $this->modify($date) : false; } public function firstOfQuarter($dayOfWeek = null) { return $this->setDate($this->year, $this->quarter * static::MONTHS_PER_QUARTER - 2, 1)->firstOfMonth($dayOfWeek); } public function lastOfQuarter($dayOfWeek = null) { return $this->setDate($this->year, $this->quarter * static::MONTHS_PER_QUARTER, 1)->lastOfMonth($dayOfWeek); } public function nthOfQuarter($nth, $dayOfWeek) { $date = $this->copy()->day(1)->month($this->quarter * static::MONTHS_PER_QUARTER); $lastMonth = $date->month; $year = $date->year; $date->firstOfQuarter()->modify('+'.$nth.' '.static::$days[$dayOfWeek]); return ($lastMonth < $date->month || $year !== $date->year) ? false : $this->modify($date); } public function firstOfYear($dayOfWeek = null) { return $this->month(1)->firstOfMonth($dayOfWeek); } public function lastOfYear($dayOfWeek = null) { return $this->month(static::MONTHS_PER_YEAR)->lastOfMonth($dayOfWeek); } public function nthOfYear($nth, $dayOfWeek) { $date = $this->copy()->firstOfYear()->modify('+'.$nth.' '.static::$days[$dayOfWeek]); return $this->year === $date->year ? $this->modify($date) : false; } public function average($date = null) { return $this->addSeconds((int) ($this->diffInSeconds($this->resolveCarbon($date), false) / 2)); } public function isBirthday($date = null) { return $this->isSameAs('md', $date); } public function isLastOfMonth() { return $this->day === $this->daysInMonth; } public function serialize() { return serialize($this); } public static function fromSerialized($value) { $instance = @unserialize($value); if (!$instance instanceof static) { throw new InvalidArgumentException('Invalid serialized value.'); } return $instance; } public static function __set_state($array) { return static::instance(parent::__set_state($array)); } } 