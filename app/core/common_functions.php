<?php
// the file will be load by composer autoloader.

/**
 * 共通関数群
 */

/**
 * HTMLエスケープの短縮形
 * @param string $text
 * @return string
 */
function h(string $text): string
{
  return htmlentities($text, ENT_QUOTES, \Fc2blog\Config::get('INTERNAL_ENCODING'));
}

/**
 * マルチバイト対応のtruncate
 * @param string $text
 * @param int $length
 * @param string $etc
 * @return string
 */
function t(string $text, int $length = 10, string $etc = '...'): string
{
  if (!$length) {
    return '';
  }
  if (mb_strlen($text, \Fc2blog\Config::get('INTERNAL_ENCODING')) > $length) {
    return mb_substr($text, 0, $length, \Fc2blog\Config::get('INTERNAL_ENCODING')) . $etc;
  }
  return $text;
}

/**
 * 対象の内容が空文字列の場合代替内容を返却する
 * @param $text
 * @param $default
 * @return string
 */
function d(string $text, $default): string
{
  if ($text === null || $text === '') {
    return $default;
  }
  return $text;
}

/**
 * t,hのエイリアス
 * @param $text
 * @param int $length
 * @param string $etc
 * @return string
 */
function th(string $text, int $length = 10, string $etc = '...'): string
{
  return h(t($text, $length, $etc));
}

/**
 * URLのエンコードエイリアス
 * @param $text
 * @return string
 */
function ue(string $text): string
{
  return rawurlencode($text);
}

/**
 * 日付のフォーマット変更
 * @param $date
 * @param string $format
 * @return false|string
 */
function df($date, $format = 'Y/m/d H:i:s'): string
{
  return date($format, strtotime($date));
}

/**
 * GettextのWrapper
 * @param $msg
 * @return string
 */
function __($msg): string
{
  return _($msg);
}
