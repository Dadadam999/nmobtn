<?php
/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */
namespace nmobtn\Interfaces;

interface ITable
{
  public function __construct();
  public function Create();
  public function Drop();
}
