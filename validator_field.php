<?php
namespace Bx
{
  /**
   * Валидатор отдельного поля.
   */
  class ValidatorField
  {
    /**
     * Коллекция встроенных правил валидации, где ключ - имя правила,
     * а значение - количество принимаемых правилом аргументов.
     * @var array
     */
    protected static $defaultRules = array(
      'required' => 1,
      'email' => 1,
      'float' => 1,
      'integer' => 1,
      'digits' => 1,
      'min' => 3,
      'max' => 3,
      'between' => 4,
      'minLength' => 2,
      'maxLength' => 2,
      'length' => 2,
      'matches' => 3,
      'notMatches' => 3,
      'startsWith' => 2,
      'notStartsWith' => 2,
      'endsWith' => 2,
      'notEndsWith' => 2,
      'ip' => 1,
      'url' => 1,
      'date' => 1,
      'minDate' => 3,
      'maxDate' => 3,
      'ccnum' => 1,
      'oneOf' => 2,
    );

    /**
     * Имя свойства, которое будет валидироваться.
     * @var string
     */
    protected $property;

    /**
     * Ссылка на экземпляр валидатора.
     * @var \Bx\Vendor\Validator
     */
    protected $validator;

    /**
     * Создает экземпляр класса
     * @param string               $property  Имя свойства для валидации.
     * @param \Bx\Vendor\Validator $validator Валидатор.
     */
    public function __construct($property, $validator)
    {
      $this->validator = $validator;
      $this->property = $property;
    }



    /**
     * Вызывает метод валидатора с указанным набором параметров.
     * @param  string $func Имя метода.
     * @param  array  $args Аргументы.
     * @return mixed        Результат работы метода.
     */
    protected function invoke($func, $args)
    {
      $params = array($this->validator, $func);
      return call_user_func_array($params, $args);
    }

    /**
     * Вызывает функцию валидатора, подменяя аргументы вызова в зависимости от
     * имени функции.
     * @param  string $func Имя функции.
     * @param  array  $args Исходные аргументы вызова.
     * @return void
     */
    protected function execute($func, $args)
    {
      $rules = array_keys(self::$defaultRules);
      $isRule = in_array($func, $rules);

      if ($isRule) {
        $countMax = self::$defaultRules[$func];
        $count = count($args);
        
        $isAppend = $countMax > $count;

        $isHack = $countMax > $count + 1;
        if ($isHack) $args[] = true;

        if ($isAppend) {
          $message = Validator::getMessage($func);
          $args[] = $message;
        }
      }

      return $this->invoke($func, $args);
    }



    /**
     * Массив сохраненных правил валидации поля.
     * @var array
     */
    protected $rules = array();

    /**
     * Запоминает правило валидации поля.
     * @param  string $func Имя функции.
     * @param  array  $args Арументы вызова функции.
     * @return void
     */
    protected function push($func, $args)
    {
      $rule = array($func, $args);
      $this->rules[] = $rule;
    }

    /**
     * Выполняется при вызове необъявленного в классе метода.
     * @param  string $name Имя метода.
     * @param  array  $args Аргументы вызова метода.
     * @return mixed        Результат работы метода.
     */
    public function __call($name, $args)
    {
      $this->push($name, $args);
      return $this;
    }



    /**
     * Вызывает валидацию свойства.
     * @return void
     */
    public function validate()
    {
      foreach ($this->rules as $rule) {
        $func = $rule[0];
        $args = $rule[1];
        $this->execute($func, $args);
      }

      $this->validator->validate($this->property);
    }
  }
}