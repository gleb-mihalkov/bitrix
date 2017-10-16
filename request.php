<?php
namespace Bx
{
  /**
   * Менеджер параметров запроса к серверу.
   */
  class Request implements \ArrayAccess
  {
    /**
     * Преобразованные параметры.
     * @var array
     */
    protected $values = array();

    /**
     * Маска параметров для быстрой проверки существования ключа.
     * @var array
     */
    protected $loaded = array();

    /**
     * Маска удаленных параметров для быстрой проверки на unset.
     * @var array
     */
    protected $removed = array();

    /**
     * Получает значение из параметров запроса или массива файлов.
     * @param  string $name Имя параметра.
     * @return mixed        Значение.
     */
    protected function getParam($name)
    {
      $isCache = isset($this->loaded[$name]);
      if ($isCache) return $this->values[$name];

      $key = self::getGlobalsKey($name);
      $value = null;

      if ($key) {
        $value = $GLOBALS[$key][$name];
        $isValue = $key !== '_FILES';

        $value = $isValue
          ? self::sanitizeValue($value)
          : self::sanitizeFile($value);
      }

      $this->values[$name] = $value;
      $this->loaded[$name] = true;

      return $value;
    }

    /**
     * Задает параметр.
     * @param string $name  Имя параметра.
     * @param mixed  $value Новое значение.
     */
    protected function setParam($name, $value)
    {
      $this->values[$name] = $value;
      $this->loaded[$name] = true;
    }

    /**
     * Показывает, существует ли параметр.
     * @param  string  $name Имя параметра.
     * @return boolean       True, если существует, иначе false.
     */
    protected function isParam($name)
    {
      return !isset($this->removed[$name]) && (
        isset($this->loaded[$name]) || self::getGlobalsKey($name)
      );
    }

    /**
     * Удаляет параметр.
     * @param  string $name Имя параметра.
     * @return void
     */
    protected function removeParam($name)
    {
      $this->removed[$name] = true;
      $this->values[$name] = null;
    }

    /**
     * Создает экземпляр класса.
     */
    protected function __construct() {}

    /**
     * Единственная сущность синглтона.
     * @var Bx\Request
     */
    protected static $instance;

    /**
     * Возвращает единственную сущность синглтона.
     * @return \Bx\Request Сущность.
     */
    public static function getInstance()
    {
      return self::$instance
        ? self::$instance
        : (self::$instance = new Request());
    }

    /**
     * Очищает значение параметра от потенциально возможных вставок кода XSS-атаки.
     * @param  mixed $value Значение.
     * @return mixed        Безопасное значение.
     */
    protected static function sanitizeValue($value)
    {
      if (is_scalar($value)) {
        $value = trim($value);
        $value = stripslashes($value);
        $value = htmlspecialchars($value);
        return $value;
      }

      foreach ($value as $key => $item) {
        $value[$key] = self::sanitizeValue($item);
      }

      return $value;
    }

    /**
     * Проверяет загруженный пользователем файл на наличие возможных уязвимостей.
     * @param  array  $file Файл из массива $_FILES.
     * @return array        Файл или null, если файл был признан вредоносным.
     */
    protected static function sanitizeFile($file)
    {
      // @todo: implement;
      return $file;
    }

    /**
     * Форматирует значение.
     * @param  mixed $value Значение свойства.
     * @return mixed        Значение.
     */
    protected static function formatValue($value)
    {
      if (is_scalar($value)) return $value;

      $isList = !empty($value) && array_keys($value) === range(0, count($value) - 1);

      if ($isList) {
        $isObject = true;
        $object = [];

        foreach ($value as $item) {
          $isValue = !is_array($item) || isset($item[0]) || count($item) !== 1;

          if ($isValue) {
            $isObject = false;
            break;
          }
          
          foreach ($item as $fieldName => $fieldValue) {
            $object[$fieldName] = $fieldValue;
            break;
          }
        }

        if ($isObject) {
          $value = $object;
        }
      }

      foreach ($value as $name => $item) {
        $value[$name] = self::formatValue($item);
      }

      return $value;
    }

    /**
     * Возвращает ключ массива, содержащего параметр, в массиве $GLOBALS.
     * @param  string $name Имя параметра.
     * @return string       Имя ключа в массиве $GLOBALS.
     */
    protected static function getGlobalsKey($name)
    {
      if (isset($_POST[$name])) return '_POST';
      if (isset($_GET[$name])) return '_GET';
      if (isset($_FILES[$name])) return '_FILES';
      return null;
    }



    /**
     * Получает значение по его имени.
     * @param  string $name Имя свойства.
     * @return mixed        Значение.
     */
    public function __get($name)
    {
      return $this->getParam($name);
    }

    /**
     * Задает значение свойству.
     * @param  string $name  Имя свойства.
     * @param  mixed  $value Значение.
     * @return void
     */
    public function __set($name, $value)
    {
      $this->setParam($name, $value);
    }

    /**
     * Вызывается, когда происходит вызов isset.
     * @param  string  $name Имя параметра.
     * @return boolean       True, если параметр существует, иначе false.
     */
    public function __isset($name)
    {
      return $this->isParam($name);
    }

    /**
     * Показывает, существует ли указанный параметр.
     * @param  string  $name Имя параметра.
     * @return boolean       True, если существует, иначе false.
     */
    public function offsetExists($name)
    {
      return $this->isParam($name);
    }

    /**
     * Получает значение параметра.
     * @param  string $name Имя параметра.
     * @return mixed        Значение.
     */
    public function offsetGet($name)
    {
      return $this->getParam($name);
    }

    /**
     * Задает значение параметра.
     * @param  string $name  Имя параметра.
     * @param  mixed  $value Значение.
     * @return void
     */
    public function offsetSet($name, $value)
    {
      $this->setParam($name, $value);
    }

    /**
     * Удаляет указанный параметр.
     * @param  string $name Имя параметра.
     * @return void
     */
    public function offsetUnset($name)
    {
      $this->removeParam($name);
    }
  }
}

namespace {

  /**
   * Возвращает объект с параметрами запроса.
   * @return \Bx\Request Объект с параметрами запроса.
   */
  function bx_request() {
    return \Bx\Request::getInstance();
  }
}