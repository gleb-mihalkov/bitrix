<?php
namespace Bx
{
  /**
   * Абстрактное перечисление, использующее курсор в качестве источника данных.
   */
  abstract class Result implements \Iterator, \Countable
  {
    /**
     * Возвращает очередной элемент перечисления.
     * @return array
     */
    abstract protected function fetch();

    /**
     * Выполняет запрос на получение курсора для перечисления элементов.
     * @return mixed Курсор для перечисления элементов.
     */
    abstract protected function result();



    /**
     * Условия выборки элементов.
     * @var array
     */
    public $filter = array();

    /**
     * Порядок сортировки выборки.
     * @var array
     */
    public $order = array();

    /**
     * Максимальное количество элементов.
     * @var integer
     */
    public $limit = null;

    /**
     * Начальная позиция выборки.
     * @var integer
     */
    public $offset = null;



    /**
     * Задает условия выборки элементов.
     * @return \Bx\Result Клон объекта с дополненными условиями выборки.
     */
    public function where()
    {
      $child = $this->inherit();

      $args = func_get_args();
      self::_filters($args, $child->filter);
      
      return $child;
    }

    /**
     * Задает порядок сортировки элементов.
     * @return \Bx\Result Клон объекта с дополненными условиями выборки.
     */
    public function order()
    {
      $child = $this->inherit();
      
      $args = func_get_args();
      self::_orders($args, $child->order);
      
      return $child;
    }

    /**
     * Задает максимальное количество элементов и начальную позицию выборки.
     * @return \Bx\Result Клон объекта с дополненными условиями выборки.
     */
    public function limit()
    {
      $child = $this->inherit();
      
      $args = func_get_args();
      self::_limits($args, $child->limit, $child->offset);

      return $child;
    }



    /**
     * Возвращает массив элементов перечисления.
     * @return array Массив элементов.
     */
    public function toArray()
    {
      if ($this->_isLoaded) return $this->_list;
      
      $current = $this->_current;
      $index = $this->_index;

      while (true) {
        $this->next();
        if (!$this->valid()) break;
      }

      $this->_current = $current;
      $this->_index = $index;

      return $this->_list;
    }

    /**
     * Получает массив значений указанного свойства.
     * @param  string  $name    Свойство.
     * @param  boolean $isNulls Показывает, следует ли включать в массив результаты от элементов,
     *                          у которых данное свойство не задано.
     * @return array            Массив значений.
     */
    public function values($name, $isNulls = false)
    {
      $list = array();

      foreach ($this as $item) {
        $value = isset($item[$name]) ? $item[$name] : null;
        if (!$isNulls && $value === null) continue;

        $list[] = $value;
      }

      return $list;
    }

    /**
     * Группирует элементы по значению указанного свойства.
     * @param  string $name     Имя свойства, по значениям которого происходит группировка.
     * @param  string $prop     Имя свойства, значения которого будут выбираться в группы. Если не задано,
     *                          в группы включаются сами элементы.
     * @param  boolean $isNulls Показывает, следует ли включать в массив результаты от элементов,
     *                          у которых свойство-значение не задано.
     * @return array            Коллекция списков элементов, группированная по указанному свойству.
     */
    public function groups($name, $prop = null, $isNulls = false)
    {
      $list = array();

      foreach ($this as $item) {
        if (!isset($item[$name])) continue;

        $key = $item[$name];
        if (!isset($list[$key])) $list[$key] = array();
        
        $value = $prop ? (isset($item[$prop]) ? $item[$prop] : null) : $item;
        if (!$isNulls && $value === null) continue;

        $list[$key][] = $value;
      }

      return $list;
    }

    /**
     * Индексирует элементы по значению указанного свойства.
     * @param  string $name     Имя свойства, по значениям которого происходит индексация.
     * @param  string $prop     Имя свойства, значения которого будут выбираться в индекс. Если не задано,
     *                          в индекс включаются сами элементы.
     * @param  boolean $isNulls Показывает, следует ли включать в массив результаты от элементов,
     *                          у которых свойство-значение не задано.
     * @return array            Коллекция списков элементов, индексированная по указанному свойству.
     */
    public function index($name, $prop = null, $isNulls = false)
    {
      $list = array();

      foreach ($this as $item) {
        if (!isset($item[$name])) continue;

        $key = $item[$name];
        $value = $prop ? (isset($item[$prop]) ? $item[$prop] : null) : $item;

        if (!$isNulls && $value === null) continue;
        $list[$key] = $value;
      }

      return $list;
    }

    /**
     * Получает первый элемент из последовательности.
     * @param  string $prop     Имя свойства, значения которого будут выбрано. Если не задано,
     *                          функция вернет сам элемент.
     * @param  boolean $isNulls Показывает, следует ли включать в массив результаты от элементов,
     *                          у которых свойство-значение не задано.
     * @return array            Коллекция списков элементов, индексированная по указанному свойству.
     */
    public function first($prop = null, $isNulls = false)
    {
      foreach ($this as $item) {
        $value = $prop ? (isset($item[$prop]) ? $item[$prop] : null) : $item;
        if ($value === null && !$isNulls) continue;

        return $value;
      }

      return null;
    }



    /**
     * Курсор для перечисления элементов.
     * @var object
     */
    protected $_result = null;

    /**
     * Список загруженных элементов.
     * @var array
     */
    protected $_list = array();

    /**
     * Длина списка загруженных элементов.
     * @var integer
     */
    protected $_length = 0;

    /**
     * Текущий индекс элемента в списке.
     * @var integer
     */
    protected $_index = -1;

    /**
     * Текущий элемент.
     * @var array
     */
    protected $_current = null;

    /**
     * Показывает, были ли загружены все элементы.
     * @var boolean
     */
    protected $_isLoaded = false;

    /**
     * Родительское перечисление.
     * @var \Bx\Result
     */
    protected $_parent = null;



    /**
     * Перематывает перечисление на начало.
     * @return void
     */
    public function rewind()
    {
      $this->_current = null;
      $this->_index = -1;
      $this->next();
    }

    /**
     * Перемещает перечисление на одну позицию вперед.
     * @return void
     */
    public function next()
    {
      $this->_index += 1;

      $isFromList = $this->_index < $this->_length;

      if ($isFromList) {
        $this->_current = $this->_list[$this->_index];
        return;
      }

      if ($this->_isLoaded) {
        $this->_current = null;
        return;
      }

      $this->_current = $this->fetch();

      if (!$this->_current) {
        $this->_isLoaded = true;
        return;
      }

      $this->_list[] = $this->_current;
      $this->_length += 1;
    }

    /**
     * Получает текущий элемент перечисления.
     * @return array Текущий элемент.
     */
    public function current()
    {
      $current = $this->_current;
      return $current;
    }

    /**
     * Получает текущий ID текущего элемента перечисления.
     * @return string ID текущего элемента.
     */
    public function key()
    {
      $id = isset($this->_current['ID']) ? $this->_current['ID'] : null;
      return $id;
    }

    /**
     * Показывает, является ли текущая позиция перечисления корректной.
     * @return boolean True, если да, иначе false.
     */
    public function valid()
    {
      $isValid = !!$this->_current;
      return $isValid;
    }

    /**
     * Получает количество элементов в перечислении.
     * @return integer Количество элементов.
     */
    public function count()
    {
      $list = $this->toArray();
      return count($list);
    }

    /**
     * Возвращает новый объект перечисления, унаследованный от данного.
     * @return \Bx\Result Новое перечисление.
     */
    protected function inherit()
    {
      $child = clone $this;
      $child->_parent = $this;
      return $child;
    }

    /**
     * Возвращает дочернее перечисление, унаследованное от данного.
     * @return \Bx\Result Перечисление.
     */
    public function __clone()
    {
      $this->_list = array();
      $this->_length = 0;
      $this->_index = -1;
      $this->_current = null;
      $this->_isLoaded = false;

      $this->_result = null;
      $this->_parent = null;

      $this->limit = null;
      $this->offset = null;
    }

    /**
     * Получает значение при попытке обратиться к необъявленному свойству.
     * @param  string $name Имя свойства.
     * @return mixed        Значение.
     */
    public function __get($name)
    {
      if ($name === 'result') {
        if ($this->_result == null) $this->_result = $this->result();
        return $this->_result;
      }

      return null;
    }


    /**
     * Записывает условия выборки в массив.
     * @param array $list    Список условий выборки.
     * @param array &$filter Результирующий массив.
     */
    protected static function _filters($list, &$filter)
    {
      $conds = isset($list[0]) ? $list[0] : array();
      $isSingle = is_scalar($conds);
      
      if ($isSingle) {
        $isValue = isset($conds[1]);

        if ($isValue) {
          $conds = array();
          $conds[$list[0]] = $list[1];
        }
        else {
          $conds = array();
        }
      }

      $filter = array_merge($filter, $conds);
    }

    /**
     * Записывает порядки сортировки в массив.
     * @param array $list    Входной массив порядков сортировки.
     * @param array &$orders Результирующий массив.
     */
    protected static function _orders($list, &$orders, $level = 1)
    {
      $listCount = count($list);
      $isSingle = $level === 1 && $listCount === 2 && is_scalar($list[0]) && is_scalar($list[1]);

      if ($isSingle) {
        self::_ordersSet($orders, $list[0], $list[1]);
        return;
      }

      foreach ($list as $name => $value) {

        if (is_int($name)) {
          $name = $value;
          $value = 'ASC';
        }

        if (is_array($name)) {
          self::_orders($name, $orders, $level + 1);
          continue;
        }

        self::_ordersSet($orders, $name, $value);
      }
    }

    /**
     * Добавляет в коллекцию один порядок сортировки.
     * @param  array  &$orders Коллекция порядков сортировки.
     * @param  string $name    Имя свойства.
     * @param  string $value   Направление сортировки.
     * @return void
     */
    protected static function _ordersSet(&$orders, $name, $value)
    {
      if (is_string($value)) $value = strtoupper($value);
      $value = $value && $value !== 'DESC' ? 'ASC' : 'DESC';
      $orders[$name] = $value;
    }

    /**
     * Записывает максимальное количество элементов и начальную позицию из списка в две переменные.
     * @param  array   $list    Список значений.
     * @param  integer &$limit  Максимальное количество.
     * @param  integer &$offset Начальная позиция.
     * @return void
     */
    protected static function _limits($list, &$limit, &$offset)
    {
      $valueA = isset($list[0]) ? $list[0] : null;
      $valueB = isset($list[1]) ? $list[1] : null;

      if (is_array($valueA)) {
        self::_limits($valueA, $limit, $offset);
        return;
      }

      $valueA = $valueA === null ? null : $valueA * 1;
      $valueB = $valueB === null ? null : $valueB * 1;

      $limit = $valueA;
      $offset = $valueB;
    }
  }
}