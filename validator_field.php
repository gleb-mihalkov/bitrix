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
      if (is_string($func)) {
        $params = array($this->validator, $func);
        return call_user_func_array($params, $args);
      }

      $message = array_pop($args);
      return $this->validator->callback($func, $message, $args);
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
      $handler = Validator::getRule($func);
      $isRule = $handler || isset(self::$defaultRules[$func]);

      if ($isRule) $args = $this->getArgs($func, $args, $handler);
      if ($handler) $func = $handler;

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



    /**
     * Получает аргументы для передачи в функцию, если нужно, подменяя сообщение
     * об ошибке сообщением по умолчанию.
     * @param  string   $rule    $func   Имя правила.
     * @param  array    $args    Исходный массив аргументов.
     * @param  callable $handler Обработчик правила.
     * @return array             Новый массив аргументов c обязательным аргументом
     *                           $message в конце списка.
     */
    protected function getArgs($rule, $args, $handler = null)
    {
      $isHandler = $handler != null;

      if (!$isHandler) {
        $handler = array($this->validator, $rule);
      }

      $params = self::reflectParams($handler, $rule);
      $paramsCount = count($params);
      $argsCount = count($args);

      $diff = $paramsCount - $argsCount;

      if ($diff > 0) {
        $message = Validator::getMessage($rule);

        $start = $paramsCount - $diff;
        $last = $paramsCount - 1;

        if ($isHandler) {
          $start += 1;
          $last += 1;
        }

        for ($i = $start; $i < $paramsCount; $i++) {
          $isDefault = $i !== $last;
          $value = null;

          if ($isDefault) {
            $param = $params[$i];
            $isOpt = $param->isOptional();

            if ($isOpt) {
              $value = $param->getDefaultValue();
            }
          }
          else {
            $value = $message;
          }

          $args[] = $value;
        }

        if ($isHandler) {
          $args[] = $message;
        }
      }

      self::renderMessage($args);
      return $args;
    }

    /**
     * Кэш рефлексии параметров функций.
     * @var array
     */
    protected static $params = array();

    /**
     * Возвращает массив описаний аргументов функции.
     * @param  string|callable $func Функция.
     * @param  string          $key  Ключ для кэширования.
     * @return array                 Массив описаний аргументов.
     */
    protected static function reflectParams($func, $key)
    {
      $isCache = isset(self::$params[$key]);
      if ($isCache) return self::$params[$key];

      $reflection = is_array($func)
        ? new \ReflectionMethod($func[0], $func[1])
        : new \ReflectionFunction($func);

      $params = $reflection->getParameters();
      return self::$params[$key] = $params;
    }



    /**
     * Если нужно, преобразует в строку сообщение об ошибке, заданное
     * в строке аргументов для вызова правила валидации.
     * @param  array  $args Массив аргументов, передаваемых в правило.
     * @return string       Сообщение об ошибке.
     */
    protected static function renderMessage(&$args)
    {
      $count = count($args);
      $index = $count - 1;
      $message = $args[$index];

      if (!is_callable($message)) return;

      $temp = $args;
      array_pop($temp);

      $text = call_user_func_array($message, $temp);
      $args[$index] = $text;
    }
  }
}